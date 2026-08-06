"use client";

import { useEffect, useRef, type ReactNode } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

/**
 * Pins the wrapped section briefly on desktop while the signal-flow diagram
 * assembles as the user scrolls through it, then releases and continues
 * scrolling normally. Falls back to a simple one-time reveal on smaller
 * viewports (pinning is unreliable with mobile browser chrome), and is
 * skipped entirely for prefers-reduced-motion, where the static, fully
 * visible markup (already correct in the server HTML) is left untouched.
 */
export function ScrollCinematic({
  children,
  className,
  as = "div",
}: {
  children: ReactNode;
  className?: string;
  as?: "div" | "section";
}) {
  const containerRef = useRef<HTMLElement | null>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);
    const mm = gsap.matchMedia();

    mm.add({ isDesktop: "(min-width: 1024px)" }, (context) => {
      const { isDesktop } = context.conditions as { isDesktop: boolean };

      const inputNodes = container.querySelectorAll('[data-diagram-part="input-node"]');
      const outputNodes = container.querySelectorAll('[data-diagram-part="output-node"]');
      const hub = container.querySelector('[data-diagram-part="hub"]');
      const inputPaths = container.querySelector('[data-diagram-part="input-paths"]');
      const outputPaths = container.querySelector('[data-diagram-part="output-paths"]');

      if (!inputNodes.length || !outputNodes.length || !hub) return;

      gsap.set(inputNodes, { opacity: 0, x: -24 });
      gsap.set(outputNodes, { opacity: 0, x: 24 });
      gsap.set(hub, { opacity: 0, scale: 0.6, transformOrigin: "50% 50%" });
      if (inputPaths) gsap.set(inputPaths, { opacity: 0 });
      if (outputPaths) gsap.set(outputPaths, { opacity: 0 });

      const timeline = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          start: "top top",
          end: isDesktop ? "+=60%" : "+=1",
          scrub: isDesktop ? 0.6 : false,
          pin: isDesktop,
          anticipatePin: 1,
          toggleActions: isDesktop ? undefined : "play none none reverse",
        },
      });

      timeline
        .to(inputNodes, { opacity: 1, x: 0, stagger: 0.12, duration: 0.4, ease: "power2.out" })
        .to(inputPaths, { opacity: 1, duration: 0.3 }, "<0.1")
        .to(hub, { opacity: 1, scale: 1, duration: 0.35, ease: "back.out(1.7)" }, "-=0.15")
        .to(outputPaths, { opacity: 1, duration: 0.3 }, "<0.1")
        .to(outputNodes, { opacity: 1, x: 0, stagger: 0.1, duration: 0.4, ease: "power2.out" }, "<0.05");

      return () => {
        timeline.scrollTrigger?.kill();
        timeline.kill();
      };
    });

    return () => mm.revert();
  }, []);

  const Tag = as;

  return (
    <Tag ref={containerRef as never} className={className}>
      {children}
    </Tag>
  );
}

"use client";

import { useEffect, useRef, type ReactNode } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { cn } from "@/lib/cn";

/**
 * A horizontal connector line draws in above the wrapped steps as the
 * scrub position advances, and each `[data-diagram-part="step"]` child
 * rises into place in sequence -- the same scroll-tied language as
 * HowItWorks, generalised for any ordered step/stage list.
 */
export function ConnectedSteps({
  children,
  className,
  lineClassName,
}: {
  children: ReactNode;
  className?: string;
  lineClassName?: string;
}) {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const line = container.querySelector('[data-diagram-part="connector-line"]');
      const items = container.querySelectorAll('[data-diagram-part="step"]');
      if (!items.length) return;

      if (line) gsap.set(line, { scaleX: 0, transformOrigin: "left center" });
      gsap.set(items, { opacity: 0, y: 20 });

      const timeline = gsap.timeline({
        scrollTrigger: { trigger: container, start: "top 80%", end: "top 40%", scrub: 0.6 },
      });

      if (line) timeline.to(line, { scaleX: 1, duration: 0.4 });
      timeline.to(items, { opacity: 1, y: 0, stagger: 0.12, duration: 0.35 }, line ? "-=0.2" : 0);
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <div ref={containerRef} className={cn("relative", className)}>
      <div
        aria-hidden="true"
        data-diagram-part="connector-line"
        className={cn("absolute inset-x-0 top-0 hidden h-px bg-gold-500/25 sm:block", lineClassName)}
      />
      {children}
    </div>
  );
}

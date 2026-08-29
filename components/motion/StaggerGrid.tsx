"use client";

import { useEffect, useRef, type ReactNode } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

/**
 * Animates direct children marked with data-stagger-item into view with a
 * quick staggered pop-in, once, as the grid scrolls into view. Skips
 * entirely for prefers-reduced-motion (server-rendered markup is already
 * fully visible, so no-JS/reduced-motion clients see everything as-is).
 */
export function StaggerGrid({
  children,
  className,
  itemSelector = '[data-stagger-item]',
}: {
  children: ReactNode;
  className?: string;
  itemSelector?: string;
}) {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const items = container.querySelectorAll(itemSelector);
      if (!items.length) return;

      gsap.set(items, { opacity: 0, y: 24, scale: 0.96 });

      gsap.to(items, {
        opacity: 1,
        y: 0,
        scale: 1,
        stagger: 0.08,
        duration: 0.5,
        ease: "power2.out",
        scrollTrigger: {
          trigger: container,
          start: "top 82%",
          once: true,
        },
      });
    }, containerRef);

    return () => ctx.revert();
  }, [itemSelector]);

  return (
    <div ref={containerRef} className={className}>
      {children}
    </div>
  );
}

"use client";

import { useEffect, useRef } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

const chaosNodes = [
  { x: 10, y: 12 },
  { x: 26, y: 30 },
  { x: 8, y: 46 },
  { x: 30, y: 8 },
  { x: 20, y: 58 },
  { x: 34, y: 42 },
];

const chaosLines = [
  "M10 12 L26 30",
  "M26 30 L8 46",
  "M30 8 L20 58",
  "M8 46 L34 42",
  "M10 12 L30 8",
  "M20 58 L26 30",
];

const orderRows = [14, 30, 46, 62];

export function FrictionDiagram() {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const chaos = container.querySelector('[data-diagram-part="chaos"]');
      const connector = container.querySelector('[data-diagram-part="connector"]');
      const orderGroups = container.querySelectorAll('[data-diagram-part="order-row"]');

      if (!chaos || !orderGroups.length) return;

      gsap.set(chaos, { opacity: 0 });
      gsap.set(connector, { opacity: 0, scaleX: 0, transformOrigin: "left center" });
      gsap.set(orderGroups, { opacity: 0, x: 10 });

      const timeline = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          start: "top 80%",
          end: "top 30%",
          scrub: 0.6,
        },
      });

      timeline
        .to(chaos, { opacity: 1, duration: 0.3 })
        .to(connector, { opacity: 1, scaleX: 1, duration: 0.3 }, "-=0.05")
        .to(orderGroups, { opacity: 1, x: 0, stagger: 0.15, duration: 0.4 }, "-=0.1");
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <div
      ref={containerRef}
      className="relative aspect-[16/9] w-full max-w-2xl"
      role="img"
      aria-label="Diagram showing scattered, disconnected operational tasks on the left resolving into an ordered, structured workflow on the right."
    >
      <svg viewBox="0 0 100 68" className="h-full w-full" aria-hidden="true">
        <g opacity="0.55" data-diagram-part="chaos">
          {chaosLines.map((d) => (
            <path key={d} d={d} stroke="var(--color-ink-500)" strokeWidth="0.4" fill="none" />
          ))}
          {chaosNodes.map((node) => (
            <circle key={`${node.x}-${node.y}`} cx={node.x} cy={node.y} r="1.6" className="fill-ink-600" />
          ))}
        </g>

        <path
          d="M40 34 H60"
          stroke="var(--color-gold-500)"
          strokeWidth="0.5"
          strokeDasharray="1.5 2"
          className="animate-dashflow"
          data-diagram-part="connector"
        />

        <g>
          {orderRows.map((y, index) => (
            <g key={y} data-diagram-part="order-row">
              <line x1="66" y1={y} x2="94" y2={y} stroke="var(--color-ink-600)" strokeWidth="0.35" />
              <circle cx="66" cy={y} r="1.4" className="fill-gold-500" style={{ opacity: 1 - index * 0.12 }} />
            </g>
          ))}
        </g>
      </svg>
    </div>
  );
}

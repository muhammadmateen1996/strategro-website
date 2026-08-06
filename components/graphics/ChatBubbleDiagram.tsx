"use client";

import { useEffect, useRef } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export function ChatBubbleDiagram() {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const nodes = container.querySelectorAll('[data-diagram-part="node"]');
      if (!nodes.length) return;

      gsap.set(nodes, { opacity: 0, y: 12 });

      const timeline = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          start: "top 80%",
          end: "top 45%",
          scrub: 0.6,
        },
      });

      timeline.to(nodes, { opacity: 1, y: 0, stagger: 0.2, duration: 0.35 });
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <div
      ref={containerRef}
      className="w-full max-w-sm rounded-2xl border border-paper-50/10 bg-ink-900 p-6"
      role="img"
      aria-label="Chat conversation showing a customer question answered instantly by an AI chatbot."
    >
      <div className="space-y-3">
        <div
          data-diagram-part="node"
          className="ml-auto max-w-[75%] rounded-2xl rounded-tr-sm bg-ink-800 px-4 py-2.5 text-sm text-paper-100"
        >
          Do you cover deliveries outside London?
        </div>
        <div
          data-diagram-part="node"
          className="mr-auto max-w-[80%] rounded-2xl rounded-tl-sm bg-gold-500 px-4 py-2.5 text-sm text-ink-950"
        >
          Yes &mdash; nationwide, with same-day options in most areas.
        </div>
      </div>
      <div
        data-diagram-part="node"
        className="mt-4 flex items-center gap-2 text-xs text-paper-100/50"
      >
        <span className="size-1.5 rounded-full bg-gold-400" aria-hidden="true" />
        Answered instantly, day or night
      </div>
    </div>
  );
}

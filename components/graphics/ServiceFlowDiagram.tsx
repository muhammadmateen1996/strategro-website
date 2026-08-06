"use client";

import { Fragment, useEffect, useRef } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import type { LucideIcon } from "lucide-react";
import { cn } from "@/lib/cn";

interface Stage {
  icon: LucideIcon;
  label: string;
  emphasize?: boolean;
}

export function ServiceFlowDiagram({ stages, ariaLabel }: { stages: Stage[]; ariaLabel: string }) {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const nodes = container.querySelectorAll('[data-diagram-part="node"]');
      const lines = container.querySelectorAll('[data-diagram-part="line"]');
      if (!nodes.length) return;

      gsap.set(nodes, { opacity: 0, y: 14, scale: 0.9, transformOrigin: "50% 50%" });
      gsap.set(lines, { opacity: 0, scaleX: 0, transformOrigin: "left center" });

      const timeline = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          start: "top 80%",
          end: "top 40%",
          scrub: 0.6,
        },
      });

      nodes.forEach((node, index) => {
        timeline.to(node, { opacity: 1, y: 0, scale: 1, duration: 0.3 }, index * 0.18);
        const line = lines[index - 1];
        if (line) timeline.to(line, { opacity: 1, scaleX: 1, duration: 0.25 }, index * 0.18 - 0.08);
      });
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <div ref={containerRef} className="relative w-full max-w-lg py-8" role="img" aria-label={ariaLabel}>
      <div className="flex items-center">
        {stages.map((stage, index) => (
          <Fragment key={stage.label}>
            <div className="flex shrink-0 flex-col items-center gap-2" data-diagram-part="node">
              <span
                className={cn(
                  "flex items-center justify-center rounded-full border",
                  stage.emphasize
                    ? "size-16 border-gold-500 bg-gold-500 text-ink-950"
                    : "size-12 border-paper-50/15 bg-ink-900 text-gold-400"
                )}
              >
                <stage.icon className={stage.emphasize ? "size-7" : "size-5"} aria-hidden="true" />
              </span>
              <span className="whitespace-nowrap text-[11px] font-medium text-paper-100/70">
                {stage.label}
              </span>
            </div>
            {index < stages.length - 1 && (
              <div className="mx-1.5 h-px flex-1 bg-gold-500/40 sm:mx-2.5" data-diagram-part="line" />
            )}
          </Fragment>
        ))}
      </div>
    </div>
  );
}

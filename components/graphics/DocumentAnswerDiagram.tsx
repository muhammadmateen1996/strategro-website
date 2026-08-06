"use client";

import { useEffect, useRef } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { FileText, MessageCircleQuestion, CheckCircle2 } from "lucide-react";

const documents = [0, 1, 2];

export function DocumentAnswerDiagram() {
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const docs = container.querySelectorAll('[data-diagram-part="document"]');
      const connector = container.querySelector('[data-diagram-part="connector"]');
      const question = container.querySelector('[data-diagram-part="question"]');
      const answer = container.querySelector('[data-diagram-part="answer"]');

      if (!docs.length || !answer) return;

      docs.forEach((doc) => {
        const rotate = Number(doc.getAttribute("data-final-rotate")) || 0;
        const y = Number(doc.getAttribute("data-final-y")) || 0;
        gsap.set(doc, { opacity: 0, rotation: rotate, y: y - 24, scale: 0.85, transformOrigin: "50% 50%" });
      });
      gsap.set(connector, { opacity: 0, scaleX: 0, transformOrigin: "left center" });
      gsap.set(question, { opacity: 0, scale: 0.7, transformOrigin: "50% 50%" });
      gsap.set(answer, { opacity: 0, y: 16 });

      const timeline = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          start: "top 80%",
          end: "top 30%",
          scrub: 0.6,
        },
      });

      docs.forEach((doc, index) => {
        const rotate = Number(doc.getAttribute("data-final-rotate")) || 0;
        const y = Number(doc.getAttribute("data-final-y")) || 0;
        timeline.to(doc, { opacity: 1, rotation: rotate, y, scale: 1, duration: 0.3 }, index * 0.08);
      });

      timeline
        .to(connector, { opacity: 1, scaleX: 1, duration: 0.25 }, "-=0.05")
        .to(question, { opacity: 1, scale: 1, duration: 0.25 }, "-=0.1")
        .to(answer, { opacity: 1, y: 0, duration: 0.3 }, "-=0.05");
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <div
      ref={containerRef}
      className="relative w-full max-w-md"
      role="img"
      aria-label="Diagram showing multiple company documents being indexed, a question being asked, and a sourced answer being returned with a citation."
    >
      <div className="flex items-center justify-between gap-3">
        <div className="relative flex h-24 w-16 items-center justify-center">
          {documents.map((index) => (
            <span
              key={index}
              data-diagram-part="document"
              data-final-rotate={(index - 1) * 8}
              data-final-y={index * 2}
              className="absolute flex size-14 items-center justify-center rounded-xl border border-paper-50/15 bg-ink-900 shadow-lg"
              style={{
                transform: `rotate(${(index - 1) * 8}deg) translateY(${index * 2}px)`,
                zIndex: documents.length - index,
              }}
            >
              <FileText className="size-6 text-gold-400" aria-hidden="true" />
            </span>
          ))}
        </div>

        <svg viewBox="0 0 40 10" className="h-3 w-16 flex-1" aria-hidden="true">
          <path
            d="M0 5 H40"
            stroke="var(--color-gold-500)"
            strokeWidth="1"
            strokeDasharray="2 2.5"
            className="animate-dashflow"
            data-diagram-part="connector"
          />
        </svg>

        <span
          data-diagram-part="question"
          className="flex size-14 shrink-0 items-center justify-center rounded-full border border-gold-500/40 bg-ink-800"
        >
          <MessageCircleQuestion className="size-6 text-gold-400" aria-hidden="true" />
        </span>
      </div>

      <div
        data-diagram-part="answer"
        className="mx-auto mt-6 max-w-xs rounded-2xl border border-paper-50/10 bg-ink-900 p-4"
      >
        <div className="flex items-start gap-2.5">
          <CheckCircle2 className="mt-0.5 size-4 shrink-0 text-gold-400" aria-hidden="true" />
          <div>
            <p className="text-sm text-paper-100">
              &ldquo;Client onboarding requires signed engagement letter and ID verification.&rdquo;
            </p>
            <p className="mt-1.5 text-xs text-paper-100/50">
              Source: Client Onboarding Policy, v3
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

"use client";

import { useState } from "react";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { cn } from "@/lib/cn";

const steps = [
  {
    label: "Discover",
    title: "We map how work actually moves today.",
    description:
      "Every tool, handoff, and manual step is documented, including the parts nobody thinks to mention because they've always done it that way.",
  },
  {
    label: "Design",
    title: "We propose the system before we build anything.",
    description:
      "You see exactly what will change, what stays the same, and where the automation fits, before any development starts.",
  },
  {
    label: "Build",
    title: "We connect your tools and test against real data.",
    description:
      "Workflows, chatbots, or voice agents are built, tested against real scenarios, and refined with your team before going live.",
  },
  {
    label: "Optimise",
    title: "We monitor and refine after launch.",
    description:
      "Systems are reviewed against real usage, so accuracy and reliability improve as your business changes.",
  },
];

export function HowItWorks() {
  const [active, setActive] = useState(0);

  return (
    <section className="bg-ink-950 py-24 sm:py-28">
      <Container>
        <SectionHeading
          eyebrow="How Strategro Works"
          tone="light"
          title="A system built in four deliberate stages."
          description="No stage is skipped, and nothing goes live until it has been tested against how your business actually operates."
        />

        <div className="mt-16">
          <div
            role="tablist"
            aria-label="Strategro's process stages"
            className="relative grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-4 sm:gap-8"
          >
            <div
              aria-hidden="true"
              className="absolute left-0 right-0 top-5 hidden h-px bg-paper-50/15 sm:block"
            />
            {steps.map((step, index) => (
              <button
                key={step.label}
                type="button"
                role="tab"
                id={`step-tab-${index}`}
                aria-selected={active === index}
                aria-controls={`step-panel-${index}`}
                onClick={() => setActive(index)}
                className="focus-ring group relative flex flex-col items-start gap-3 text-left"
              >
                <span
                  className={cn(
                    "relative z-10 flex size-10 items-center justify-center rounded-full border text-sm font-semibold transition-colors duration-300",
                    active === index
                      ? "border-gold-500 bg-gold-500 text-ink-950"
                      : "border-paper-50/25 bg-ink-950 text-paper-100/70 group-hover:border-gold-500/60"
                  )}
                >
                  0{index + 1}
                </span>
                <span
                  className={cn(
                    "font-display text-base transition-colors duration-300 sm:text-lg",
                    active === index ? "text-gold-400" : "text-paper-100/70 group-hover:text-paper-50"
                  )}
                >
                  {step.label}
                </span>
              </button>
            ))}
          </div>

          <div className="mt-10 rounded-2xl border border-paper-50/10 bg-ink-900 p-8 sm:p-10">
            {steps.map((step, index) => (
              <div
                key={step.label}
                role="tabpanel"
                id={`step-panel-${index}`}
                aria-labelledby={`step-tab-${index}`}
                hidden={active !== index}
                className="animate-rise"
              >
                <h3 className="font-display text-2xl text-paper-50 sm:text-3xl">{step.title}</h3>
                <p className="mt-4 max-w-2xl text-base leading-relaxed text-paper-100/75">
                  {step.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </Container>
    </section>
  );
}

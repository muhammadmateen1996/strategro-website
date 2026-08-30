import type { Metadata } from "next";
import { LineChart, ShieldCheck, TestTube2 } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Reveal } from "@/components/motion/Reveal";
import { StaggerGrid } from "@/components/motion/StaggerGrid";
import { TiltCard } from "@/components/motion/TiltCard";
import { CtaSection } from "@/components/sections/CtaSection";
import { JsonLd, breadcrumbSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "Algorithmic Trading Labs",
  description:
    "Strategro Labs designs and tests specialist algorithmic trading systems. A small, separate offering alongside our core AI automation work.",
  alternates: { canonical: "/labs/algorithmic-trading" },
};

const principles = [
  {
    icon: TestTube2,
    title: "Rigorously tested",
    description:
      "Strategies are backtested and forward-tested before any live consideration, with clear documentation of assumptions and limitations.",
  },
  {
    icon: ShieldCheck,
    title: "Risk-aware by design",
    description:
      "Position sizing and risk controls are built into every system, not added as an afterthought.",
  },
  {
    icon: LineChart,
    title: "Specialist, not primary",
    description:
      "This is a focused Labs offering alongside our core AI automation work, for clients with a specific quantitative trading requirement.",
  },
];

export default function AlgorithmicTradingPage() {
  const breadcrumb = breadcrumbSchema([
    { name: "Home", url: siteConfig.url },
    { name: "Labs", url: `${siteConfig.url}/labs/algorithmic-trading` },
  ]);

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: breadcrumb })} />

      <section className="bg-ink-950 py-20 sm:py-24">
        <Container>
          <Reveal>
            <p className="mb-5 inline-flex items-center gap-2 rounded-full border border-gold-500/30 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-gold-400">
              Strategro Labs
            </p>
            <h1 className="max-w-3xl font-display text-4xl leading-[1.1] text-paper-50 sm:text-5xl">
              Specialist algorithmic trading systems.
            </h1>
            <p className="mt-6 max-w-2xl text-lg leading-relaxed text-paper-100/75">
              Alongside our core AI automation work, Strategro Labs builds and evaluates algorithmic
              trading systems for clients with a specific quantitative requirement. This is a small,
              separate offering &mdash; not our primary focus.
            </p>
          </Reveal>
        </Container>
      </section>

      <section className="bg-paper-50 py-20">
        <Container>
          <SectionHeading
            eyebrow="Approach"
            title="Quantitative rigour applied carefully."
            description="Trading systems carry real risk. Our approach favours tested, documented, risk-managed strategies over speculative claims."
          />
          <StaggerGrid className="mt-12 grid gap-6 sm:grid-cols-3">
            {principles.map((principle) => (
              <div key={principle.title} data-stagger-item className="h-full">
                <TiltCard className="h-full rounded-2xl border border-ink-950/8 bg-white p-7">
                  <span className="flex size-11 items-center justify-center rounded-xl bg-ink-950 text-gold-400">
                    <principle.icon className="size-5" aria-hidden="true" />
                  </span>
                  <h3 className="mt-5 font-display text-lg text-ink-950">{principle.title}</h3>
                  <p className="mt-2.5 text-sm leading-relaxed text-ink-700">{principle.description}</p>
                </TiltCard>
              </div>
            ))}
          </StaggerGrid>
        </Container>
      </section>

      <CtaSection
        heading="Have a quantitative trading requirement?"
        subhead="Get in touch to discuss whether a Strategro Labs engagement is the right fit."
        buttonLabel="Start a conversation"
      />
    </>
  );
}

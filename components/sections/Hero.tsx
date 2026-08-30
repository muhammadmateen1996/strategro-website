import { Container } from "@/components/ui/Container";
import { Button } from "@/components/ui/Button";
import { ScrollCinematic } from "@/components/motion/ScrollCinematic";
import { Spotlight } from "@/components/motion/Spotlight";
import { SignalFlowDiagram } from "@/components/graphics/SignalFlowDiagram";
import { siteConfig } from "@/lib/site-config";

export function Hero() {
  return (
    <ScrollCinematic
      as="section"
      className="relative overflow-hidden bg-ink-950 pb-20 pt-16 sm:pb-28 sm:pt-20 lg:pb-32"
    >
      <div
        aria-hidden="true"
        className="pointer-events-none absolute inset-x-0 top-0 h-[520px] bg-[radial-gradient(ellipse_at_top,_rgba(201,154,68,0.14),_transparent_60%)]"
      />
      <Spotlight />
      <Container className="relative grid gap-14 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-8">
        <div>
          <p className="mb-6 inline-flex items-center gap-2 rounded-full border border-gold-500/30 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-gold-400">
            Signal to System
          </p>
          <h1 className="font-display text-4xl leading-[1.08] text-paper-50 sm:text-5xl lg:text-[3.4rem]">
            Turn Operational Drag Into Intelligent Systems.
          </h1>
          <p className="mt-6 max-w-xl text-lg leading-relaxed text-paper-100/75">
            We build the AI systems that catch what&rsquo;s slipping through the cracks &mdash;
            the calls, the messages, the leads &mdash; and turn them into one system that
            keeps working after everyone else has gone home.
          </p>
          <div className="mt-9 flex flex-wrap items-center gap-4">
            <Button href={siteConfig.ctaPrimary.href} variant="primary">
              {siteConfig.ctaPrimary.label}
            </Button>
            <Button href={siteConfig.ctaSecondary.href} variant="secondary">
              {siteConfig.ctaSecondary.label}
            </Button>
          </div>
        </div>

        <SignalFlowDiagram />
      </Container>
    </ScrollCinematic>
  );
}

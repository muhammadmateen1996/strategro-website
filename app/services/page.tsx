import type { Metadata } from "next";
import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { StaggerGrid } from "@/components/motion/StaggerGrid";
import { TiltCard } from "@/components/motion/TiltCard";
import { CtaSection } from "@/components/sections/CtaSection";
import { services } from "@/content/services";
import { JsonLd, breadcrumbSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "AI Automation Services for UK & UAE Businesses",
  description:
    "Strategro's core services: AI workflow automation, chatbots, voice AI receptionists, RAG knowledge assistants, and AI lead & content systems.",
  alternates: { canonical: "/services" },
};

export default function ServicesIndexPage() {
  const breadcrumb = breadcrumbSchema([
    { name: "Home", url: siteConfig.url },
    { name: "Services", url: `${siteConfig.url}/services` },
  ]);

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: breadcrumb })} />

      <section className="bg-ink-950 py-20 sm:py-24">
        <Container>
          <SectionHeading
            eyebrow="Services"
            tone="light"
            as="h1"
            title="AI systems built around a specific operational outcome."
            description="Each service solves one bottleneck cleanly. Most Strategro clients start with one and add others as their systems mature."
          />
        </Container>
      </section>

      <section className="bg-paper-50 py-20 sm:py-24">
        <Container>
          <StaggerGrid className="grid gap-6 lg:grid-cols-2">
            {services.map((service) => (
              <Link
                key={service.slug}
                data-stagger-item
                href={`/services/${service.slug}`}
                className="card-glow focus-ring group block h-full rounded-2xl border border-ink-950/8 bg-white transition-all duration-300 hover:-translate-y-1 hover:border-gold-500/40"
              >
                <TiltCard className="flex h-full flex-col rounded-[inherit] p-8">
                  <span className="mb-5 flex size-12 items-center justify-center rounded-xl bg-ink-950 text-gold-400">
                    <service.icon className="size-5" aria-hidden="true" />
                  </span>
                  <h2 className="font-display text-2xl text-ink-950">{service.name}</h2>
                  <p className="mt-3 text-base leading-relaxed text-ink-700">{service.description}</p>
                  <span className="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-gold-600">
                    Explore this service
                    <ArrowRight className="size-3.5 transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true" />
                  </span>
                </TiltCard>
              </Link>
            ))}
          </StaggerGrid>
        </Container>
      </section>

      <CtaSection
        heading="Not sure which system fits first?"
        subhead="An AI Systems Audit identifies your highest-impact automation opportunity in one call."
      />
    </>
  );
}

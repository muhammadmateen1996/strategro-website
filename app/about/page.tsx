import type { Metadata } from "next";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Button } from "@/components/ui/Button";
import { JsonLd, breadcrumbSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "About Strategro",
  description:
    "Strategro Ltd is a UK-registered digital technology company building AI automation for SMEs, founded by Muhammad Mateen Qazi.",
  alternates: { canonical: "/about" },
};

const principles = [
  {
    title: "Practical over hyped",
    description:
      "We build systems that solve a defined operational problem. If AI isn't the right tool for something, we say so.",
  },
  {
    title: "Grounded in your operations",
    description:
      "Every system starts with how your business actually works today, not a generic template applied regardless of context.",
  },
  {
    title: "Built to be maintained",
    description:
      "Workflows and assistants are documented clearly, so your team understands and can evolve what's been built.",
  },
];

const trackRecord = [
  { stat: "60–95%", label: "Typical reduction in manual workflow time across delivered projects" },
  { stat: "24/7", label: "Automated coverage delivered via deployed chatbot and voice infrastructure" },
  { stat: "2024", label: "Strategro Ltd founded and registered in London, United Kingdom" },
];

export default function AboutPage() {
  const breadcrumb = breadcrumbSchema([
    { name: "Home", url: siteConfig.url },
    { name: "About", url: `${siteConfig.url}/about` },
  ]);

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: breadcrumb })} />

      <section className="bg-ink-950 py-20 sm:py-24">
        <Container>
          <h1 className="max-w-3xl font-display text-4xl leading-[1.1] text-paper-50 sm:text-5xl">
            AI automation, built around how your business actually works.
          </h1>
          <p className="mt-6 max-w-2xl text-lg leading-relaxed text-paper-100/75">
            Strategro Ltd is a UK-registered digital technology company building AI-powered
            business process automation for SMEs &mdash; particularly in logistics, real estate,
            law, and professional services, where response speed and information flow directly
            affect revenue.
          </p>

          <div className="mt-12 grid gap-8 border-t border-paper-50/10 pt-10 sm:grid-cols-3">
            {trackRecord.map((item) => (
              <div key={item.label}>
                <p className="font-display text-3xl text-gold-400">{item.stat}</p>
                <p className="mt-2 text-sm leading-relaxed text-paper-100/70">{item.label}</p>
              </div>
            ))}
          </div>
        </Container>
      </section>

      <section className="bg-paper-50 py-20">
        <Container className="grid gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">
          <div>
            <p className="text-xs font-semibold uppercase tracking-[0.2em] text-gold-600">
              Founder
            </p>
            <h2 className="mt-4 font-display text-2xl text-ink-950 sm:text-3xl">
              Muhammad Mateen Qazi
            </h2>
            <p className="mt-2 text-sm text-ink-700/70">Founder &amp; Director, Strategro Ltd</p>
          </div>
          <div className="space-y-4 text-base leading-relaxed text-ink-700">
            <p>
              Muhammad is an electronics engineer and business analytics graduate who founded
              Strategro to bring practical, production-grade AI automation to SMEs &mdash; built on
              n8n, OpenAI, and retrieval-augmented generation, rather than off-the-shelf chatbot
              widgets.
            </p>
            <p>
              Before founding Strategro, he built and deployed multi-client RAG chatbot
              infrastructure, automated SEO content pipelines, and lead-to-document automation for
              real estate and logistics businesses &mdash; work that now underpins how Strategro
              designs systems for clients.
            </p>
          </div>
        </Container>
      </section>

      <section className="bg-paper-100 py-20">
        <Container className="grid gap-14 lg:grid-cols-2 lg:gap-16">
          <div>
            <SectionHeading
              eyebrow="How we think"
              title="Systems, not software for its own sake."
              description="AI is a tool for solving a specific operational problem, not a feature to bolt on. We start with what's actually slowing your business down, and design backwards from there."
            />
          </div>
          <div className="space-y-8">
            {principles.map((principle) => (
              <div key={principle.title}>
                <h3 className="font-display text-lg text-ink-950">{principle.title}</h3>
                <p className="mt-2 text-sm leading-relaxed text-ink-700">{principle.description}</p>
              </div>
            ))}
          </div>
        </Container>
      </section>

      <section className="bg-paper-50 py-20">
        <Container>
          <SectionHeading
            eyebrow="Where we work"
            title="UK and UAE businesses, particularly in time-sensitive sectors."
            description="Logistics, real estate, law firms, and professional service businesses share a common pattern: response speed and information flow have a direct, measurable effect on revenue. That's where Strategro's systems have the clearest impact."
          />
        </Container>
      </section>

      <section className="bg-ink-950 py-20">
        <Container className="text-center">
          <h2 className="font-display text-3xl text-paper-50 sm:text-4xl">
            Talk to Strategro about your operations.
          </h2>
          <div className="mt-8 flex justify-center">
            <Button href="/contact">Book an AI Systems Audit</Button>
          </div>
        </Container>
      </section>
    </>
  );
}

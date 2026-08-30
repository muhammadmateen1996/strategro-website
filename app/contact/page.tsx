import type { Metadata } from "next";
import { Mail } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Reveal } from "@/components/motion/Reveal";
import { JsonLd, breadcrumbSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "Contact — Book an AI Systems Audit",
  description:
    "Book a free AI Systems Audit with Strategro to identify your highest-impact automation opportunity.",
  alternates: { canonical: "/contact" },
};

export default function ContactPage() {
  const breadcrumb = breadcrumbSchema([
    { name: "Home", url: siteConfig.url },
    { name: "Contact", url: `${siteConfig.url}/contact` },
  ]);

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: breadcrumb })} />

      <section className="bg-ink-950 py-20 sm:py-24">
        <Container>
          <SectionHeading
            eyebrow="Contact"
            tone="light"
            as="h1"
            title="Book an AI Systems Audit."
            description="A 30-minute conversation to identify where automation would save your business the most time. No jargon, no pressure."
          />
        </Container>
      </section>

      <section className="bg-paper-50 py-20">
        <Container className="max-w-2xl">
          <Reveal>
            <div className="overflow-hidden rounded-2xl border border-ink-950/8 bg-white">
              <div className="border-b border-ink-950/8 px-6 py-5 sm:px-8">
                <h2 className="font-display text-xl text-ink-950">Book a call</h2>
                <p className="mt-1 text-sm text-ink-700">Pick a slot that works for you.</p>
              </div>
              <iframe
                src={siteConfig.clara.bookingUrl}
                width="100%"
                height="720"
                style={{ border: 0, display: "block" }}
                title="Book an AI Systems Audit"
                loading="lazy"
              />
            </div>
          </Reveal>

          <Reveal delay={0.1}>
            <div className="mt-10 overflow-hidden rounded-2xl border border-ink-950/8 bg-white">
              <div className="border-b border-ink-950/8 px-6 py-5 sm:px-8">
                <h2 className="font-display text-xl text-ink-950">Prefer to send a message?</h2>
                <p className="mt-1 text-sm text-ink-700">
                  Tell us a bit about what you&rsquo;d like to automate and we&rsquo;ll get back to you.
                </p>
              </div>
              <iframe
                src={siteConfig.clara.formUrl}
                width="100%"
                height="640"
                style={{ border: 0, display: "block" }}
                title="Contact form"
                loading="lazy"
              />
            </div>
          </Reveal>

          <p className="mt-8 text-center text-sm text-ink-700/70">
            Trouble with the forms above?{" "}
            <a
              href={`mailto:${siteConfig.email}`}
              className="focus-ring inline-flex items-center gap-1.5 font-medium text-gold-600 hover:underline"
            >
              <Mail className="size-3.5" aria-hidden="true" />
              Email {siteConfig.email} directly
            </a>
            .
          </p>
        </Container>
      </section>
    </>
  );
}

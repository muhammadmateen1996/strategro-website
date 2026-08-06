import type { Metadata } from "next";
import { Mail, Calendar } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { ContactForm } from "@/components/contact/ContactForm";
import { isLeadWebhookConfigured } from "@/lib/n8n";
import { JsonLd, breadcrumbSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "Contact — Book an AI Systems Audit",
  description:
    "Book a free AI Systems Audit with Strategro to identify your highest-impact automation opportunity.",
  alternates: { canonical: "/contact" },
};

export default function ContactPage() {
  const webhookConfigured = isLeadWebhookConfigured();
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
          {webhookConfigured ? <ContactForm /> : <FallbackPanel />}

          {siteConfig.calendlyUrl && webhookConfigured && (
            <div className="mt-6 flex flex-col items-start justify-between gap-4 rounded-2xl border border-ink-950/8 bg-white p-6 sm:flex-row sm:items-center">
              <div>
                <h2 className="font-display text-lg text-ink-950">Prefer to skip the form?</h2>
                <p className="mt-1 text-sm text-ink-700">Book a slot directly on our calendar.</p>
              </div>
              <a
                href={siteConfig.calendlyUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="focus-ring inline-flex shrink-0 items-center justify-center gap-2 rounded-full border border-ink-950/15 px-6 py-3 text-sm font-semibold text-ink-950 transition-colors hover:border-gold-500/60"
              >
                <Calendar className="size-4" aria-hidden="true" />
                Book via Calendly
              </a>
            </div>
          )}
        </Container>
      </section>
    </>
  );
}

function FallbackPanel() {
  return (
    <div className="rounded-2xl border border-ink-950/8 bg-white p-8 sm:p-10">
      <h2 className="font-display text-2xl text-ink-950">Let&rsquo;s talk directly.</h2>
      <p className="mt-3 text-sm leading-relaxed text-ink-700">
        Our booking form isn&rsquo;t connected yet. In the meantime, the fastest way to reach us
        is directly below.
      </p>
      <div className="mt-8 flex flex-col gap-4 sm:flex-row">
        <a
          href={`mailto:${siteConfig.email}`}
          className="focus-ring inline-flex items-center justify-center gap-2 rounded-full bg-gold-500 px-6 py-3.5 text-sm font-semibold text-ink-950 transition-colors hover:bg-gold-400"
        >
          <Mail className="size-4" aria-hidden="true" />
          Email {siteConfig.email}
        </a>
        {siteConfig.calendlyUrl && (
          <a
            href={siteConfig.calendlyUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="focus-ring inline-flex items-center justify-center gap-2 rounded-full border border-ink-950/15 px-6 py-3.5 text-sm font-semibold text-ink-950 transition-colors hover:border-gold-500/60"
          >
            <Calendar className="size-4" aria-hidden="true" />
            Book directly via Calendly
          </a>
        )}
      </div>
    </div>
  );
}

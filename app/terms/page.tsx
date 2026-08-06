import type { Metadata } from "next";
import { Container } from "@/components/ui/Container";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "Terms of Service",
  description: `Terms governing the use of ${siteConfig.legalName}'s website and services.`,
  alternates: { canonical: "/terms" },
  robots: { index: true, follow: true },
};

export default function TermsPage() {
  return (
    <section className="bg-paper-50 py-20 sm:py-24">
      <Container className="max-w-3xl">
        <h1 className="font-display text-4xl text-ink-950">Terms of Service</h1>
        <p className="mt-3 text-sm text-ink-700/70">Last updated: 6 August 2026</p>

        <div className="prose prose-neutral mt-10 max-w-none prose-headings:font-display">
          <p>
            These terms govern your use of this website, operated by {siteConfig.legalName}
            (&ldquo;Strategro&rdquo;, &ldquo;we&rdquo;, &ldquo;us&rdquo;). By using this website, you
            agree to these terms.
          </p>

          <h2>Use of this website</h2>
          <p>
            This website is provided for information about Strategro&rsquo;s services. You may not
            use it in any way that could damage, disable, or impair the site, or interfere with
            another party&rsquo;s use of it.
          </p>

          <h2>No guaranteed outcomes</h2>
          <p>
            Content on this website, including service descriptions and illustrative workflow
            examples, is provided for general information. It does not constitute a guarantee of
            specific results. Any engagement with Strategro is governed by a separate written
            agreement setting out scope, deliverables, and terms.
          </p>

          <h2>Intellectual property</h2>
          <p>
            The content, design, and branding of this website are the property of{" "}
            {siteConfig.legalName} unless otherwise stated, and may not be reproduced without
            permission.
          </p>

          <h2>Third-party links</h2>
          <p>
            This website may link to third-party services (such as a booking or scheduling tool).
            We are not responsible for the content or practices of third-party websites.
          </p>

          <h2>Limitation of liability</h2>
          <p>
            To the extent permitted by law, {siteConfig.legalName} is not liable for any indirect
            or consequential loss arising from the use of this website.
          </p>

          <h2>Governing law</h2>
          <p>
            These terms are governed by the laws of England and Wales, without prejudice to any
            mandatory consumer protections applicable in your jurisdiction.
          </p>

          <h2>Contact</h2>
          <p>
            Questions about these terms can be sent to{" "}
            <a href={`mailto:${siteConfig.email}`}>{siteConfig.email}</a>.
          </p>

          <p className="text-sm text-ink-700/60">
            This page is a starting template and should be reviewed by a qualified legal advisor
            before publication.
          </p>
        </div>
      </Container>
    </section>
  );
}

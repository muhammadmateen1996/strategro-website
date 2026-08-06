import type { Metadata } from "next";
import { Container } from "@/components/ui/Container";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "Privacy Policy",
  description: `How ${siteConfig.legalName} collects, uses, and protects your data.`,
  alternates: { canonical: "/privacy-policy" },
  robots: { index: true, follow: true },
};

export default function PrivacyPolicyPage() {
  return (
    <section className="bg-paper-50 py-20 sm:py-24">
      <Container className="max-w-3xl">
        <h1 className="font-display text-4xl text-ink-950">Privacy Policy</h1>
        <p className="mt-3 text-sm text-ink-700/70">Last updated: 6 August 2026</p>

        <div className="prose prose-neutral mt-10 max-w-none prose-headings:font-display">
          <p>
            This policy explains how {siteConfig.legalName} (&ldquo;Strategro&rdquo;,
            &ldquo;we&rdquo;, &ldquo;us&rdquo;) collects, uses, and protects information when you
            visit this website or use our services.
          </p>

          <h2>Information we collect</h2>
          <p>
            When you submit an enquiry through our contact form, we collect the information you
            provide, including your name, work email, company, website or LinkedIn URL, business
            type, automation requirements, and approximate team size. We also collect standard
            technical information (such as IP address and browser type) through normal web server
            logging.
          </p>

          <h2>How we use your information</h2>
          <p>
            We use the information you provide to respond to your enquiry, arrange and conduct AI
            Systems Audit calls, and, where you have consented, to contact you about our services.
            We do not sell your personal data to third parties.
          </p>

          <h2>How your enquiry is processed</h2>
          <p>
            Contact form submissions are transmitted securely from our server to our internal
            automation systems to route your enquiry to the right person. We do not expose this
            connection to your browser.
          </p>

          <h2>Data retention</h2>
          <p>
            We retain enquiry data for as long as necessary to respond to you and, where a
            business relationship begins, for the duration of that relationship and as required by
            applicable law.
          </p>

          <h2>Your rights</h2>
          <p>
            Depending on your jurisdiction, you may have the right to access, correct, or request
            deletion of your personal data. To exercise these rights, contact us at{" "}
            <a href={`mailto:${siteConfig.email}`}>{siteConfig.email}</a>.
          </p>

          <h2>Cookies and analytics</h2>
          <p>
            We may use privacy-conscious analytics tools to understand how visitors use this
            website, in order to improve it. Where analytics are enabled, this is disclosed in the
            site&rsquo;s cookie or analytics configuration.
          </p>

          <h2>Contact</h2>
          <p>
            Questions about this policy can be sent to{" "}
            <a href={`mailto:${siteConfig.email}`}>{siteConfig.email}</a>.
          </p>

          <p className="text-sm text-ink-700/60">
            This page is a starting template and should be reviewed by a qualified legal advisor
            before publication, to ensure it reflects your actual data practices and applicable UK
            and UAE data protection law.
          </p>
        </div>
      </Container>
    </section>
  );
}

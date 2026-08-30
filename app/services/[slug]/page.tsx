import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import { Check, ArrowRight } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { Button } from "@/components/ui/Button";
import { Reveal } from "@/components/motion/Reveal";
import { StaggerGrid } from "@/components/motion/StaggerGrid";
import { ConnectedSteps } from "@/components/motion/ConnectedSteps";
import { CtaSection } from "@/components/sections/CtaSection";
import { ServiceHeroDiagram } from "@/components/graphics/ServiceHeroDiagram";
import { getRelatedServices, getServiceBySlug, services } from "@/content/services";
import { JsonLd, breadcrumbSchema, faqSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export function generateStaticParams() {
  return services.map((service) => ({ slug: service.slug }));
}

export async function generateMetadata({
  params,
}: PageProps<"/services/[slug]">): Promise<Metadata> {
  const { slug } = await params;
  const service = getServiceBySlug(slug);
  if (!service) return {};

  return {
    title: service.metaTitle,
    description: service.metaDescription,
    alternates: { canonical: `/services/${service.slug}` },
    openGraph: {
      title: service.metaTitle,
      description: service.metaDescription,
      url: `/services/${service.slug}`,
    },
  };
}

export default async function ServiceDetailPage({ params }: PageProps<"/services/[slug]">) {
  const { slug } = await params;
  const service = getServiceBySlug(slug);
  if (!service) notFound();

  const related = getRelatedServices(service);
  const breadcrumb = breadcrumbSchema([
    { name: "Home", url: siteConfig.url },
    { name: "Services", url: `${siteConfig.url}/services` },
    { name: service.name, url: `${siteConfig.url}/services/${service.slug}` },
  ]);
  const faqs = faqSchema(service.faqs);

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: breadcrumb })} />
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: faqs })} />

      <section className="bg-ink-950 py-20 sm:py-24">
        <Container>
          <nav aria-label="Breadcrumb" className="mb-8 text-xs text-paper-100/50">
            <Link href="/services" className="focus-ring hover:text-gold-400">
              Services
            </Link>
            <span className="mx-2" aria-hidden="true">
              /
            </span>
            <span className="text-paper-100/80">{service.name}</span>
          </nav>

          <div className="grid gap-12 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-8">
            <div>
              <span className="mb-5 flex size-12 items-center justify-center rounded-xl bg-gold-500 text-ink-950">
                <service.icon className="size-5" aria-hidden="true" />
              </span>
              <h1 className="font-display text-4xl leading-[1.1] text-paper-50 sm:text-5xl">
                {service.name}
              </h1>
              <p className="mt-5 max-w-2xl text-lg leading-relaxed text-paper-100/75">
                {service.heroSubhead}
              </p>
              <div className="mt-8">
                <Button href="/contact">Book an AI Systems Audit</Button>
              </div>
            </div>
            <div className="flex justify-center lg:justify-end">
              <ServiceHeroDiagram slug={service.slug} />
            </div>
          </div>
        </Container>
      </section>

      <section className="bg-paper-50 py-20">
        <Container className="grid gap-12 lg:grid-cols-2">
          <Reveal>
            <h2 className="font-display text-2xl text-ink-950">Where this usually shows up</h2>
            <ul className="mt-6 space-y-4">
              {service.problems.map((problem) => (
                <li key={problem} className="flex items-start gap-3 text-ink-700">
                  <span className="mt-1.5 size-1.5 shrink-0 rounded-full bg-gold-500" aria-hidden="true" />
                  {problem}
                </li>
              ))}
            </ul>
          </Reveal>
          <Reveal delay={0.1}>
            <h2 className="font-display text-2xl text-ink-950">Is this the right fit?</h2>
            <ul className="mt-6 space-y-4">
              {service.decisionCriteria.map((criterion) => (
                <li key={criterion} className="flex items-start gap-3 text-ink-700">
                  <Check className="mt-0.5 size-4 shrink-0 text-gold-600" aria-hidden="true" />
                  {criterion}
                </li>
              ))}
            </ul>
          </Reveal>
        </Container>
      </section>

      <section className="bg-paper-100 py-20">
        <Container>
          <h2 className="font-display text-2xl text-ink-950 sm:text-3xl">What&rsquo;s included</h2>
          <StaggerGrid className="mt-10 grid gap-6 sm:grid-cols-2">
            {service.whatItIncludes.map((item) => (
              <div key={item.title} data-stagger-item className="h-full rounded-2xl border border-ink-950/8 bg-paper-50 p-7">
                <h3 className="font-display text-lg text-ink-950">{item.title}</h3>
                <p className="mt-2.5 text-sm leading-relaxed text-ink-700">{item.description}</p>
              </div>
            ))}
          </StaggerGrid>
        </Container>
      </section>

      <section className="bg-ink-950 py-20">
        <Container>
          <h2 className="font-display text-2xl text-paper-50 sm:text-3xl">The workflow</h2>
          <ConnectedSteps className="mt-14">
            <div className="grid gap-6 pt-8 sm:grid-cols-2 lg:grid-cols-4">
              {service.workflow.map((stage, index) => (
                <div
                  key={stage.step}
                  data-diagram-part="step"
                  className="rounded-2xl border border-paper-50/10 bg-ink-900 p-6"
                >
                  <span className="text-xs font-semibold uppercase tracking-[0.2em] text-gold-500">
                    0{index + 1}
                  </span>
                  <h3 className="mt-3 font-display text-lg text-paper-50">{stage.step}</h3>
                  <p className="mt-2 text-sm leading-relaxed text-paper-100/70">{stage.description}</p>
                </div>
              ))}
            </div>
          </ConnectedSteps>
        </Container>
      </section>

      <section className="bg-paper-50 py-20">
        <Container className="max-w-3xl">
          <h2 className="font-display text-2xl text-ink-950 sm:text-3xl">Common questions</h2>
          <StaggerGrid className="mt-8 divide-y divide-ink-950/10 border-t border-ink-950/10">
            {service.faqs.map((faq) => (
              <details key={faq.question} data-stagger-item className="group py-5">
                <summary className="focus-ring flex cursor-pointer list-none items-center justify-between gap-4 font-medium text-ink-950">
                  {faq.question}
                  <span className="shrink-0 text-gold-600 transition-transform group-open:rotate-45">+</span>
                </summary>
                <p className="mt-3 text-sm leading-relaxed text-ink-700">{faq.answer}</p>
              </details>
            ))}
          </StaggerGrid>
        </Container>
      </section>

      {related.length > 0 && (
        <section className="bg-paper-100 py-20">
          <Container>
            <h2 className="font-display text-2xl text-ink-950 sm:text-3xl">Often paired with</h2>
            <StaggerGrid className="mt-8 grid gap-6 sm:grid-cols-2">
              {related.map((relatedService) => (
                <Link
                  key={relatedService.slug}
                  data-stagger-item
                  href={`/services/${relatedService.slug}`}
                  className="focus-ring group flex items-center justify-between gap-4 rounded-2xl border border-ink-950/8 bg-paper-50 p-6 transition-colors hover:border-gold-500/40"
                >
                  <div>
                    <h3 className="font-display text-lg text-ink-950">{relatedService.name}</h3>
                    <p className="mt-1.5 text-sm text-ink-700">{relatedService.outcome}</p>
                  </div>
                  <ArrowRight className="size-4 shrink-0 text-gold-600 transition-transform group-hover:translate-x-1" aria-hidden="true" />
                </Link>
              ))}
            </StaggerGrid>
          </Container>
        </section>
      )}

      <CtaSection />
    </>
  );
}

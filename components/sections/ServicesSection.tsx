import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { StaggerGrid } from "@/components/motion/StaggerGrid";
import { services } from "@/content/services";

export function ServicesSection() {
  return (
    <section id="services" className="bg-paper-50 py-24 sm:py-28">
      <Container>
        <SectionHeading
          eyebrow="What We Build"
          title="Five systems. One outcome: less drag, more capacity."
          description="Each service is designed to solve one operational bottleneck cleanly, and to work together as your systems mature."
        />

        <StaggerGrid className="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {services.map((service) => (
            <Link
              key={service.slug}
              data-stagger-item
              href={`/services/${service.slug}`}
              className="focus-ring group flex h-full flex-col justify-between rounded-2xl border border-ink-950/8 bg-white p-7 transition-all duration-300 hover:-translate-y-1 hover:border-gold-500/40 hover:shadow-xl hover:shadow-ink-950/5"
            >
              <div>
                <span className="mb-5 flex size-11 items-center justify-center rounded-xl bg-ink-950 text-gold-400 transition-transform duration-300 group-hover:scale-110">
                  <service.icon className="size-5" aria-hidden="true" />
                </span>
                <h3 className="font-display text-xl text-ink-950">{service.name}</h3>
                <p className="mt-2.5 text-sm leading-relaxed text-ink-700">{service.outcome}</p>
              </div>
              <span className="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-gold-600">
                Learn more
                <ArrowRight className="size-3.5 transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true" />
              </span>
            </Link>
          ))}

          <Link
            data-stagger-item
            href="/services"
            className="focus-ring flex h-full flex-col justify-center rounded-2xl border border-dashed border-ink-950/15 p-7 text-center transition-colors hover:border-gold-500/50"
          >
            <span className="font-display text-lg text-ink-950">See every service</span>
            <span className="mt-2 inline-flex items-center justify-center gap-1.5 text-sm font-medium text-gold-600">
              View all
              <ArrowRight className="size-3.5" aria-hidden="true" />
            </span>
          </Link>
        </StaggerGrid>
      </Container>
    </section>
  );
}

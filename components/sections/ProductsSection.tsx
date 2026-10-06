import { ArrowUpRight } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { StaggerGrid } from "@/components/motion/StaggerGrid";
import { TiltCard } from "@/components/motion/TiltCard";
import { products } from "@/content/products";
import { cn } from "@/lib/cn";

export function ProductsSection() {
  return (
    <section id="products" className="relative scroll-mt-20 overflow-hidden bg-ink-950 py-24 sm:py-28">
      <div aria-hidden="true" className="tech-grid pointer-events-none absolute inset-0" />
      <div
        aria-hidden="true"
        className="pointer-events-none absolute left-1/2 top-0 size-[720px] -translate-x-1/2 -translate-y-1/3 rounded-full bg-[radial-gradient(circle,_rgba(201,154,68,0.14),_transparent_62%)] blur-3xl"
      />

      <Container className="relative">
        <SectionHeading
          eyebrow="Our Products"
          tone="light"
          title="Software we built, running for real businesses."
          description="Each product started as a system we built for a client problem, then became something any business can sign up for."
        />

        <StaggerGrid className="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-6">
          {products.map((product, index) => (
            <a
              key={product.slug}
              data-stagger-item
              href={product.url}
              target="_blank"
              rel="noopener noreferrer"
              aria-label={`${product.name}: ${product.tagline} (opens in a new tab)`}
              className={cn(
                "focus-ring group block h-full rounded-2xl",
                index < 2 ? "lg:col-span-3" : "lg:col-span-2"
              )}
            >
              <TiltCard className="product-card card-glow flex h-full flex-col rounded-2xl p-7">
                <div className="flex items-start justify-between gap-4">
                  <span className="flex size-12 items-center justify-center rounded-xl border border-gold-500/25 bg-gold-500/10 text-gold-400 transition-transform duration-300 group-hover:scale-110">
                    <product.icon className="size-5" aria-hidden="true" />
                  </span>
                  <span className="inline-flex items-center gap-1.5 rounded-full border border-paper-50/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.15em] text-paper-100/60">
                    <span className="relative flex size-1.5">
                      <span className="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-70" />
                      <span className="relative inline-flex size-1.5 rounded-full bg-emerald-400" />
                    </span>
                    Live
                  </span>
                </div>

                <p className="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-gold-400">
                  {product.tagline}
                </p>
                <h3 className="mt-2 font-display text-2xl tracking-tight text-paper-50">{product.name}</h3>
                <p className="mt-3 flex-1 text-sm leading-relaxed text-paper-100/70">{product.description}</p>

                <div className="mt-6 flex items-center justify-between gap-4 border-t border-paper-50/10 pt-5">
                  <span className="text-xs text-paper-100/50">{product.audience}</span>
                  <span className="inline-flex items-center gap-1.5 text-sm font-medium text-gold-400">
                    {product.url.replace(/^https:\/\//, "")}
                    <ArrowUpRight
                      className="size-4 transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5"
                      aria-hidden="true"
                    />
                  </span>
                </div>
              </TiltCard>
            </a>
          ))}
        </StaggerGrid>
      </Container>
    </section>
  );
}

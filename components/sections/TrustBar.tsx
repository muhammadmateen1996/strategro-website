import { Container } from "@/components/ui/Container";
import { Reveal } from "@/components/motion/Reveal";

const industries = [
  "Logistics",
  "Real Estate",
  "Law Firms",
  "Professional Services",
  "UK Businesses",
  "UAE Businesses",
];

export function TrustBar() {
  return (
    <section className="overflow-hidden border-b border-ink-950/10 bg-paper-50 py-14">
      <Container>
        <Reveal>
          <p className="text-center font-display text-xl leading-snug text-ink-900 sm:text-2xl">
            Built for teams where time, response speed, and information flow matter.
          </p>
        </Reveal>
      </Container>

      <div className="relative mt-9 w-full">
        <div
          aria-hidden="true"
          className="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-paper-50 to-transparent sm:w-32"
        />
        <div
          aria-hidden="true"
          className="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-paper-50 to-transparent sm:w-32"
        />
        <div className="flex w-max animate-marquee items-center [animation-play-state:running] motion-safe:hover:[animation-play-state:paused]">
          {[...industries, ...industries].map((industry, index) => (
            <span key={`${industry}-${index}`} className="flex shrink-0 items-center">
              <span className="whitespace-nowrap px-6 text-xs font-semibold uppercase tracking-[0.2em] text-ink-700/60 sm:px-8">
                {industry}
              </span>
              <span className="size-1 shrink-0 rounded-full bg-gold-500/50" aria-hidden="true" />
            </span>
          ))}
        </div>
      </div>
    </section>
  );
}

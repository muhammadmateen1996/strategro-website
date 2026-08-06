import { Container } from "@/components/ui/Container";
import { Reveal } from "@/components/motion/Reveal";

const industries = ["Logistics", "Real Estate", "Law Firms", "Professional Services"];

export function TrustBar() {
  return (
    <section className="border-b border-ink-950/10 bg-paper-50 py-14">
      <Container>
        <Reveal>
          <p className="text-center font-display text-xl leading-snug text-ink-900 sm:text-2xl">
            Built for teams where time, response speed, and information flow matter.
          </p>
        </Reveal>
        <div className="mt-8 flex flex-wrap items-center justify-center gap-x-10 gap-y-3">
          {industries.map((industry) => (
            <span
              key={industry}
              className="text-xs font-semibold uppercase tracking-[0.2em] text-ink-700/60"
            >
              {industry}
            </span>
          ))}
        </div>
      </Container>
    </section>
  );
}

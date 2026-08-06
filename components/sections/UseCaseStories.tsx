import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Reveal } from "@/components/motion/Reveal";
import { useCases } from "@/content/use-cases";
import { ArrowRight } from "lucide-react";

export function UseCaseStories() {
  return (
    <section className="bg-paper-100 py-24 sm:py-28">
      <Container>
        <SectionHeading
          eyebrow="Systems We Build"
          title="What this looks like in practice."
          description="These are illustrative examples of the type of system Strategro designs for each sector, not published client results."
        />

        <div className="mt-14 grid gap-6 lg:grid-cols-3">
          {useCases.map((story, index) => (
            <Reveal key={story.slug} delay={index * 0.08}>
              <article className="flex h-full flex-col rounded-2xl border border-ink-950/8 bg-paper-50 p-7">
                <span className="inline-flex w-fit rounded-full bg-ink-950 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.15em] text-gold-400">
                  {story.industry} &middot; Illustrative example
                </span>
                <h3 className="mt-5 font-display text-xl leading-snug text-ink-950">{story.title}</h3>
                <p className="mt-3 text-sm leading-relaxed text-ink-700">{story.scenario}</p>

                <div className="mt-6 flex flex-1 flex-col gap-4 text-sm">
                  <ol className="space-y-2 border-l-2 border-gold-500/30 pl-4">
                    {story.systemFlow.map((flowStep, stepIndex) => (
                      <li key={flowStep} className="relative text-ink-700">
                        <span className="absolute -left-[1.35rem] top-1.5 size-2 rounded-full bg-gold-500" aria-hidden="true" />
                        <span className="font-medium text-ink-950">{stepIndex + 1}.</span> {flowStep}
                      </li>
                    ))}
                  </ol>
                </div>

                <span className="mt-6 inline-flex w-fit items-center gap-1.5 text-sm font-medium text-gold-600">
                  See related services
                  <ArrowRight className="size-3.5" aria-hidden="true" />
                </span>
              </article>
            </Reveal>
          ))}
        </div>
      </Container>
    </section>
  );
}

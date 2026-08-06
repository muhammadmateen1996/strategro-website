import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Reveal } from "@/components/motion/Reveal";
import { useCases } from "@/content/use-cases";
import { ArrowRight, CheckCircle2 } from "lucide-react";
import { cn } from "@/lib/cn";

export function UseCaseStories() {
  return (
    <section className="bg-paper-100 py-24 sm:py-28">
      <Container>
        <SectionHeading
          eyebrow="Systems We Build"
          title="Real projects, not just a promise."
          description="Most of what's below is real, delivered Strategro work with real results. Where a scenario isn't backed by a named client yet, it's clearly marked as illustrative."
        />

        <div className="mt-14 grid gap-6 lg:grid-cols-3">
          {useCases.map((story, index) => (
            <Reveal key={story.slug} delay={index * 0.08}>
              <article className="flex h-full flex-col rounded-2xl border border-ink-950/8 bg-paper-50 p-7">
                <span
                  className={cn(
                    "inline-flex w-fit rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.15em]",
                    story.isReal ? "bg-gold-500 text-ink-950" : "bg-ink-950 text-gold-400"
                  )}
                >
                  {story.industry} &middot; {story.isReal ? `Real project${story.client ? ` — ${story.client}` : ""}` : "Illustrative example"}
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

                  {story.results && (
                    <div className="rounded-xl bg-ink-950 p-4">
                      <p className="text-[11px] font-semibold uppercase tracking-[0.15em] text-gold-400">
                        Results
                      </p>
                      <ul className="mt-2.5 space-y-2">
                        {story.results.map((result) => (
                          <li key={result} className="flex items-start gap-2 text-xs leading-relaxed text-paper-100/85">
                            <CheckCircle2 className="mt-0.5 size-3.5 shrink-0 text-gold-400" aria-hidden="true" />
                            {result}
                          </li>
                        ))}
                      </ul>
                    </div>
                  )}
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

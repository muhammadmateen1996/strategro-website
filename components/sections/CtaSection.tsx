import { Container } from "@/components/ui/Container";
import { Button } from "@/components/ui/Button";
import { Reveal } from "@/components/motion/Reveal";

export function CtaSection() {
  return (
    <section className="bg-gold-500 py-20 sm:py-24">
      <Container>
        <Reveal>
          <div className="mx-auto max-w-2xl text-center">
            <h2 className="font-display text-3xl leading-[1.1] text-ink-950 sm:text-4xl">
              See What Your Business Could Automate First.
            </h2>
            <p className="mt-5 text-base leading-relaxed text-ink-900/80 sm:text-lg">
              A 30-minute AI Systems Audit shows you exactly where automation would save the most
              time, with no obligation and no jargon.
            </p>
            <div className="mt-9 flex justify-center">
              <Button href="/contact" variant="primary" className="bg-ink-950 text-paper-50 hover:bg-ink-900">
                Book an AI Systems Audit
              </Button>
            </div>
          </div>
        </Reveal>
      </Container>
    </section>
  );
}

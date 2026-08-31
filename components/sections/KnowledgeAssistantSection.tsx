import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Reveal } from "@/components/motion/Reveal";
import { Button } from "@/components/ui/Button";
import { DocumentAnswerDiagram } from "@/components/graphics/DocumentAnswerDiagram";

export function KnowledgeAssistantSection() {
  return (
    <section className="relative overflow-hidden bg-ink-950 py-24 sm:py-28">
      <div
        aria-hidden="true"
        className="pointer-events-none absolute left-1/2 top-1/2 size-[640px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[radial-gradient(circle,_rgba(201,154,68,0.08),_transparent_65%)] blur-3xl"
      />
      <Container className="relative grid gap-14 lg:grid-cols-2 lg:items-center lg:gap-12">
        <div>
          <SectionHeading
            eyebrow="Internal Knowledge"
            tone="light"
            title="Your documents, answering questions on their own."
            description="A RAG knowledge assistant reads your policies, procedures, and internal documents, and gives staff accurate, sourced answers instead of a folder to search through."
          />
          <ul className="glass-dark mt-8 space-y-4 rounded-2xl p-6">
            {[
              "Every answer is grounded in a real document, not a guess.",
              "The assistant declines to answer when nothing relevant exists.",
              "Access can be restricted by team or role.",
            ].map((point) => (
              <li key={point} className="flex items-start gap-3 text-sm text-paper-100/80">
                <span className="mt-1.5 size-1.5 shrink-0 rounded-full bg-gold-500" aria-hidden="true" />
                {point}
              </li>
            ))}
          </ul>
          <div className="mt-9">
            <Button href="/services/rag-knowledge-assistants" variant="secondary">
              Explore RAG Knowledge Assistants
            </Button>
          </div>
        </div>

        <Reveal>
          <div className="flex justify-center">
            <DocumentAnswerDiagram />
          </div>
        </Reveal>
      </Container>
    </section>
  );
}

import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Reveal } from "@/components/motion/Reveal";
import { FrictionDiagram } from "@/components/graphics/FrictionDiagram";

const frictionPoints = [
  {
    title: "Manual administration",
    description: "Staff re-key the same information across systems that should already talk to each other.",
  },
  {
    title: "Slow lead response",
    description: "Enquiries sit unanswered for hours, and prospects move on to whoever replies first.",
  },
  {
    title: "Disconnected tools",
    description: "CRM, email, spreadsheets, and internal tools hold different versions of the same truth.",
  },
  {
    title: "Support backlogs",
    description: "The same questions get answered manually, again and again, by people who could be doing more.",
  },
  {
    title: "Scattered knowledge",
    description: "Answers exist somewhere in a document, but finding them costs more time than it should.",
  },
  {
    title: "Wasted staff time",
    description: "Skilled people spend hours on repetitive operations instead of the work that needs judgement.",
  },
];

export function ProblemsSection() {
  return (
    <section className="relative overflow-hidden bg-ink-950 py-24 sm:py-28">
      <div
        aria-hidden="true"
        className="pointer-events-none absolute -left-40 top-0 size-[560px] rounded-full bg-[radial-gradient(circle,_rgba(201,154,68,0.1),_transparent_65%)] blur-3xl"
      />
      <Container className="relative">
        <div className="grid gap-16 lg:grid-cols-[0.9fr_1.1fr] lg:items-start lg:gap-12">
          <div>
            <SectionHeading
              eyebrow="The Problem"
              tone="light"
              title="Operational drag hides in the gaps between systems."
              description="None of this looks urgent day to day. It quietly costs revenue, response time, and staff hours until someone maps it out."
            />
            <div className="mt-10 flex justify-center lg:hidden">
              <FrictionDiagram />
            </div>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            {frictionPoints.map((point, index) => (
              <Reveal key={point.title} delay={index * 0.05}>
                <div className="glass-dark card-glow h-full rounded-2xl p-5">
                  <h3 className="font-display text-lg text-paper-50">{point.title}</h3>
                  <p className="mt-2 text-sm leading-relaxed text-paper-100/70">{point.description}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>

        <div className="mt-16 hidden justify-center lg:flex">
          <FrictionDiagram />
        </div>
      </Container>
    </section>
  );
}

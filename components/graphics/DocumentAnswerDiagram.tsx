import { FileText, MessageCircleQuestion, CheckCircle2 } from "lucide-react";

const documents = [0, 1, 2];

export function DocumentAnswerDiagram() {
  return (
    <div
      className="relative w-full max-w-md"
      role="img"
      aria-label="Diagram showing multiple company documents being indexed, a question being asked, and a sourced answer being returned with a citation."
    >
      <div className="flex items-center justify-between gap-3">
        <div className="relative flex h-24 w-16 items-center justify-center">
          {documents.map((index) => (
            <span
              key={index}
              className="absolute flex size-14 items-center justify-center rounded-xl border border-paper-50/15 bg-ink-900 shadow-lg"
              style={{
                transform: `rotate(${(index - 1) * 8}deg) translateY(${index * 2}px)`,
                zIndex: documents.length - index,
              }}
            >
              <FileText className="size-6 text-gold-400" aria-hidden="true" />
            </span>
          ))}
        </div>

        <svg viewBox="0 0 40 10" className="h-3 w-16 flex-1" aria-hidden="true">
          <path
            d="M0 5 H40"
            stroke="var(--color-gold-500)"
            strokeWidth="1"
            strokeDasharray="2 2.5"
            className="animate-dashflow"
          />
        </svg>

        <span className="flex size-14 shrink-0 items-center justify-center rounded-full border border-gold-500/40 bg-ink-800">
          <MessageCircleQuestion className="size-6 text-gold-400" aria-hidden="true" />
        </span>
      </div>

      <div className="mx-auto mt-6 max-w-xs rounded-2xl border border-paper-50/10 bg-ink-900 p-4">
        <div className="flex items-start gap-2.5">
          <CheckCircle2 className="mt-0.5 size-4 shrink-0 text-gold-400" aria-hidden="true" />
          <div>
            <p className="text-sm text-paper-100">
              &ldquo;Client onboarding requires signed engagement letter and ID verification.&rdquo;
            </p>
            <p className="mt-1.5 text-xs text-paper-100/50">
              Source: Client Onboarding Policy, v3
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

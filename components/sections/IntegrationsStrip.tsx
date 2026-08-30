import { Workflow, Sparkles, MessageCircle, LayoutGrid } from "lucide-react";
import { Container } from "@/components/ui/Container";

const integrations = [
  { label: "n8n", icon: Workflow },
  { label: "OpenAI", icon: Sparkles },
  { label: "WhatsApp Business API", icon: MessageCircle },
  { label: "Google Workspace", icon: LayoutGrid },
];

export function IntegrationsStrip() {
  return (
    <section className="border-t border-paper-50/10 bg-ink-950 py-8">
      <Container className="flex flex-wrap items-center justify-center gap-3 sm:justify-between">
        <span className="text-xs font-semibold uppercase tracking-[0.2em] text-paper-100/50">
          Built on
        </span>
        <div className="flex flex-wrap items-center justify-center gap-3">
          {integrations.map((item) => (
            <span
              key={item.label}
              className="inline-flex items-center gap-2 rounded-full border border-paper-50/10 px-4 py-2 text-sm text-paper-100/75"
            >
              <item.icon className="size-4 text-gold-400" aria-hidden="true" />
              {item.label}
            </span>
          ))}
        </div>
      </Container>
    </section>
  );
}

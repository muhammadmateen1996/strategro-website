import { Newspaper } from "lucide-react";

export function BlogEmptyState({
  title = "Insights are on their way.",
  description = "Connect WordPress as a headless CMS to publish posts here, or check back soon.",
}: {
  title?: string;
  description?: string;
}) {
  return (
    <div className="flex flex-col items-center rounded-2xl border border-dashed border-ink-950/15 px-8 py-16 text-center">
      <span className="flex size-12 items-center justify-center rounded-full bg-ink-950 text-gold-400">
        <Newspaper className="size-5" aria-hidden="true" />
      </span>
      <h3 className="mt-5 font-display text-xl text-ink-950">{title}</h3>
      <p className="mt-2 max-w-sm text-sm leading-relaxed text-ink-700">{description}</p>
    </div>
  );
}

import { cn } from "@/lib/cn";

export function Logo({ className, tone = "light" }: { className?: string; tone?: "light" | "dark" }) {
  const textColor = tone === "light" ? "text-paper-50" : "text-ink-950";

  return (
    <span className={cn("inline-flex items-center gap-2.5", className)}>
      <svg width="30" height="30" viewBox="0 0 30 30" fill="none" aria-hidden="true">
        <circle cx="5" cy="6" r="2" className="fill-gold-500" />
        <circle cx="5" cy="15" r="2" className="fill-gold-500/70" />
        <circle cx="5" cy="24" r="2" className="fill-gold-500/40" />
        <path
          d="M7 6 L15 15 M7 15 H15 M7 24 L15 15"
          stroke="currentColor"
          strokeWidth="1.2"
          className={tone === "light" ? "text-paper-50/40" : "text-ink-950/30"}
        />
        <rect x="15" y="10" width="10" height="10" rx="3" className="fill-gold-500" />
      </svg>
      <span className={cn("font-display text-lg font-medium tracking-tight", textColor)}>
        Strategro
      </span>
    </span>
  );
}

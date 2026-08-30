import type { ReactNode } from "react";
import { cn } from "@/lib/cn";

export function SectionHeading({
  eyebrow,
  title,
  description,
  align = "left",
  tone = "dark",
  className,
  as: Tag = "h2",
}: {
  eyebrow?: string;
  title: ReactNode;
  description?: ReactNode;
  align?: "left" | "center";
  tone?: "dark" | "light";
  className?: string;
  as?: "h1" | "h2";
}) {
  return (
    <div
      className={cn(
        "max-w-2xl",
        align === "center" && "mx-auto text-center",
        className
      )}
    >
      {eyebrow && (
        <p
          className={cn(
            "mb-4 text-xs font-semibold uppercase tracking-[0.2em]",
            tone === "dark" ? "text-gold-600" : "text-gold-400"
          )}
        >
          {eyebrow}
        </p>
      )}
      <Tag
        className={cn(
          "font-display text-3xl leading-[1.04] tracking-tight sm:text-4xl lg:text-5xl",
          tone === "dark" ? "text-ink-950" : "text-paper-50"
        )}
      >
        {title}
      </Tag>
      {description && (
        <p
          className={cn(
            "mt-5 text-base leading-relaxed sm:text-lg",
            tone === "dark" ? "text-ink-700" : "text-paper-100/80"
          )}
        >
          {description}
        </p>
      )}
    </div>
  );
}

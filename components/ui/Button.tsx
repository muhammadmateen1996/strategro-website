import Link from "next/link";
import type { ReactNode } from "react";
import { ArrowUpRight } from "lucide-react";
import { cn } from "@/lib/cn";

type ButtonVariant = "primary" | "secondary" | "ghost";

const variantStyles: Record<ButtonVariant, string> = {
  primary:
    "bg-gold-500 text-ink-950 hover:bg-gold-400 shadow-[0_1px_0_0_rgba(255,255,255,0.15)_inset]",
  secondary: "bg-transparent text-paper-50 border border-paper-50/30 hover:border-paper-50/70",
  ghost: "bg-transparent text-ink-950 hover:text-gold-600",
};

interface ButtonProps {
  children: ReactNode;
  href: string;
  variant?: ButtonVariant;
  showArrow?: boolean;
  className?: string;
  external?: boolean;
}

export function Button({
  children,
  href,
  variant = "primary",
  showArrow = true,
  className,
  external = false,
}: ButtonProps) {
  const classes = cn(
    "focus-ring group inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-medium tracking-wide transition-colors duration-200",
    variantStyles[variant],
    className
  );

  const content = (
    <>
      {children}
      {showArrow && (
        <ArrowUpRight
          className="size-4 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
          aria-hidden="true"
        />
      )}
    </>
  );

  if (external) {
    return (
      <a href={href} target="_blank" rel="noopener noreferrer" className={classes}>
        {content}
      </a>
    );
  }

  return (
    <Link href={href} className={classes}>
      {content}
    </Link>
  );
}

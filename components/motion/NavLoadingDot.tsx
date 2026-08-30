"use client";

import { useLinkStatus } from "next/link";
import { cn } from "@/lib/cn";

/**
 * Must render inside a <Link>. Shows a small pulsing dot next to the
 * label while that link's navigation is pending (blocked prefetch, slow
 * connection). Delayed 100ms and reserved at fixed size so fast,
 * already-prefetched navigations never flash it.
 */
export function NavLoadingDot({ className }: { className?: string }) {
  const { pending } = useLinkStatus();

  return (
    <span
      aria-hidden="true"
      className={cn(
        "inline-block size-1.5 shrink-0 rounded-full bg-gold-400 opacity-0",
        pending && "animate-nav-dot",
        className
      )}
    />
  );
}

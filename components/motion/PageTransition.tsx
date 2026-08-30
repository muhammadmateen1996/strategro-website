"use client";

import type { ReactNode } from "react";
import { usePathname } from "next/navigation";
import { AnimatePresence, motion, useReducedMotion } from "motion/react";

/**
 * Crossfades route content on client-side navigation. Keyed by pathname so
 * AnimatePresence treats each route as its own enter/exit pair. Header and
 * Footer live outside this wrapper (in the root layout) and never
 * unmount, so only the page body transitions.
 *
 * Uses motion/react rather than GSAP here on purpose: this wrapper's whole
 * job is mount/unmount timing, which is exactly what React (and therefore
 * motion, which never touches the DOM outside React's own tree) already
 * tracks correctly -- unlike GSAP's ScrollTrigger pin, which restructures
 * the DOM itself and previously broke this same route-transition path.
 */
export function PageTransition({ children }: { children: ReactNode }) {
  const pathname = usePathname();
  const shouldReduceMotion = useReducedMotion();

  if (shouldReduceMotion) {
    return <>{children}</>;
  }

  return (
    <AnimatePresence mode="wait" initial={false}>
      <motion.div
        key={pathname}
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        exit={{ opacity: 0, y: -6 }}
        transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
      >
        {children}
      </motion.div>
    </AnimatePresence>
  );
}

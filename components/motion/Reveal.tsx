"use client";

import { useSyncExternalStore, type ReactNode } from "react";
import { motion, useReducedMotion } from "framer-motion";

function subscribeNoop() {
  return () => {};
}

/**
 * True only once hydrated on the client. Using useSyncExternalStore (rather
 * than an effect that calls setState) avoids a hydration-mismatch warning
 * while still deferring the "mounted" flip to after the first client render.
 */
function useMounted() {
  return useSyncExternalStore(
    subscribeNoop,
    () => true,
    () => false
  );
}

/**
 * Renders fully visible, static content on the server (and for any client
 * without JS), then progressively enhances into a scroll-triggered fade-in
 * once mounted. This avoids content that depends on an IntersectionObserver
 * ever firing to become visible at all &mdash; important for crawlers and
 * no-JS clients, since server HTML must carry the real content either way.
 */
export function Reveal({
  children,
  className,
  delay = 0,
  y = 20,
}: {
  children: ReactNode;
  className?: string;
  delay?: number;
  y?: number;
}) {
  const shouldReduceMotion = useReducedMotion();
  const mounted = useMounted();

  if (!mounted || shouldReduceMotion) {
    return <div className={className}>{children}</div>;
  }

  return (
    <motion.div
      className={className}
      initial={{ opacity: 0, y }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-80px" }}
      transition={{ duration: 0.6, delay, ease: [0.16, 1, 0.3, 1] }}
    >
      {children}
    </motion.div>
  );
}

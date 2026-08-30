"use client";

import { useRef, type PointerEvent as ReactPointerEvent, type ReactNode } from "react";
import { motion, useMotionValue, useSpring } from "motion/react";
import { useIsInteractive } from "@/components/motion/useIsInteractive";

/**
 * Wraps interactive children (buttons/links) and gives them a subtle pull
 * toward the cursor on hover. Disabled on touch devices and
 * prefers-reduced-motion, where children render as a plain, unwrapped div.
 */
export function Magnetic({
  children,
  strength = 0.35,
  className,
}: {
  children: ReactNode;
  strength?: number;
  className?: string;
}) {
  const ref = useRef<HTMLDivElement>(null);
  const enabled = useIsInteractive();
  const x = useMotionValue(0);
  const y = useMotionValue(0);
  const springX = useSpring(x, { stiffness: 300, damping: 20, mass: 0.5 });
  const springY = useSpring(y, { stiffness: 300, damping: 20, mass: 0.5 });

  if (!enabled) {
    return <div className={className}>{children}</div>;
  }

  function handlePointerMove(event: ReactPointerEvent<HTMLDivElement>) {
    const el = ref.current;
    if (!el) return;
    const rect = el.getBoundingClientRect();
    x.set((event.clientX - (rect.left + rect.width / 2)) * strength);
    y.set((event.clientY - (rect.top + rect.height / 2)) * strength);
  }

  function handlePointerLeave() {
    x.set(0);
    y.set(0);
  }

  return (
    <motion.div
      ref={ref}
      className={className}
      style={{ x: springX, y: springY }}
      onPointerMove={handlePointerMove}
      onPointerLeave={handlePointerLeave}
    >
      {children}
    </motion.div>
  );
}

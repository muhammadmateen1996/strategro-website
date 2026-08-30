"use client";

import { useRef, type PointerEvent as ReactPointerEvent, type ReactNode } from "react";
import { motion, useMotionTemplate, useMotionValue, useSpring } from "motion/react";
import { cn } from "@/lib/cn";
import { useIsInteractive } from "@/components/motion/useIsInteractive";

/**
 * 3D pointer-tilt wrapper with a soft gold glare that tracks the cursor.
 * The tilt lives on an inner layer so it never fights a parent's own
 * transform (e.g. a GSAP stagger-entrance running on the outer card).
 * Disabled on touch devices and prefers-reduced-motion.
 */
export function TiltCard({ children, className }: { children: ReactNode; className?: string }) {
  const ref = useRef<HTMLDivElement>(null);
  const enabled = useIsInteractive();

  const rotateX = useSpring(0, { stiffness: 260, damping: 24 });
  const rotateY = useSpring(0, { stiffness: 260, damping: 24 });
  const glareX = useMotionValue(50);
  const glareY = useMotionValue(50);
  const glareOpacity = useSpring(0, { stiffness: 260, damping: 30 });
  const glareBackground = useMotionTemplate`radial-gradient(220px circle at ${glareX}% ${glareY}%, rgba(221,187,111,0.18), transparent 70%)`;

  if (!enabled) {
    return <div className={className}>{children}</div>;
  }

  function handlePointerMove(event: ReactPointerEvent<HTMLDivElement>) {
    const el = ref.current;
    if (!el) return;
    const rect = el.getBoundingClientRect();
    const px = (event.clientX - rect.left) / rect.width;
    const py = (event.clientY - rect.top) / rect.height;
    rotateY.set((px - 0.5) * 12);
    rotateX.set((0.5 - py) * 12);
    glareX.set(px * 100);
    glareY.set(py * 100);
    glareOpacity.set(1);
  }

  function handlePointerLeave() {
    rotateX.set(0);
    rotateY.set(0);
    glareOpacity.set(0);
  }

  return (
    <motion.div
      ref={ref}
      className={cn("relative", className)}
      style={{ rotateX, rotateY, transformPerspective: 900 }}
      onPointerMove={handlePointerMove}
      onPointerLeave={handlePointerLeave}
    >
      {children}
      <motion.div
        aria-hidden="true"
        className="pointer-events-none absolute inset-0 rounded-[inherit]"
        style={{ background: glareBackground, opacity: glareOpacity }}
      />
    </motion.div>
  );
}

"use client";

import { useEffect, useRef, useState } from "react";
import { motion, useSpring, useTransform, type SpringOptions } from "motion/react";
import { cn } from "@/lib/cn";
import { useIsInteractive } from "@/components/motion/useIsInteractive";

const DEFAULT_SPRING: SpringOptions = { stiffness: 200, damping: 24, mass: 0.4 };

/**
 * Ambient radial glow that follows the pointer within its positioned parent.
 * The parent must already be `relative overflow-hidden` (all current call
 * sites are). No-ops entirely on touch devices and prefers-reduced-motion,
 * so the section's static background is left untouched there.
 */
export function Spotlight({
  className,
  size = 480,
  springOptions = DEFAULT_SPRING,
}: {
  className?: string;
  size?: number;
  springOptions?: SpringOptions;
}) {
  const containerRef = useRef<HTMLDivElement>(null);
  const [isHovered, setIsHovered] = useState(false);
  const enabled = useIsInteractive();

  const mouseX = useSpring(0, springOptions);
  const mouseY = useSpring(0, springOptions);
  const spotlightLeft = useTransform(mouseX, (x) => `${x - size / 2}px`);
  const spotlightTop = useTransform(mouseY, (y) => `${y - size / 2}px`);

  useEffect(() => {
    if (!enabled) return;
    const parent = containerRef.current?.parentElement;
    if (!parent) return;

    const handleMouseMove = (event: MouseEvent) => {
      const { left, top } = parent.getBoundingClientRect();
      mouseX.set(event.clientX - left);
      mouseY.set(event.clientY - top);
    };
    const onEnter = () => setIsHovered(true);
    const onLeave = () => setIsHovered(false);

    parent.addEventListener("mousemove", handleMouseMove);
    parent.addEventListener("mouseenter", onEnter);
    parent.addEventListener("mouseleave", onLeave);
    return () => {
      parent.removeEventListener("mousemove", handleMouseMove);
      parent.removeEventListener("mouseenter", onEnter);
      parent.removeEventListener("mouseleave", onLeave);
    };
  }, [enabled, mouseX, mouseY]);

  if (!enabled) return null;

  return (
    <motion.div
      ref={containerRef}
      aria-hidden="true"
      className={cn(
        "pointer-events-none absolute rounded-full bg-[radial-gradient(circle_at_center,rgba(221,187,111,0.16),rgba(201,154,68,0.06)_45%,transparent_72%)] blur-2xl transition-opacity duration-500",
        isHovered ? "opacity-100" : "opacity-0",
        className
      )}
      style={{ width: size, height: size, left: spotlightLeft, top: spotlightTop }}
    />
  );
}

"use client";

import { useSyncExternalStore } from "react";

const HOVER_QUERY = "(hover: hover) and (pointer: fine)";
const REDUCED_MOTION_QUERY = "(prefers-reduced-motion: reduce)";

function subscribe(callback: () => void) {
  const hoverMql = window.matchMedia(HOVER_QUERY);
  const motionMql = window.matchMedia(REDUCED_MOTION_QUERY);
  hoverMql.addEventListener("change", callback);
  motionMql.addEventListener("change", callback);
  return () => {
    hoverMql.removeEventListener("change", callback);
    motionMql.removeEventListener("change", callback);
  };
}

function getSnapshot() {
  return window.matchMedia(HOVER_QUERY).matches && !window.matchMedia(REDUCED_MOTION_QUERY).matches;
}

function getServerSnapshot() {
  return false;
}

/**
 * True only for fine-pointer, hover-capable, non-reduced-motion clients,
 * after hydration. Gates whether cursor-following effects (Spotlight,
 * Magnetic, TiltCard) activate at all; everyone else gets the plain,
 * fully-visible static markup.
 */
export function useIsInteractive() {
  return useSyncExternalStore(subscribe, getSnapshot, getServerSnapshot);
}

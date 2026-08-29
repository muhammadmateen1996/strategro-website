"use client";

import { useEffect, useRef } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { Container } from "@/components/ui/Container";
import { Button } from "@/components/ui/Button";

export function CtaSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const section = sectionRef.current;
    if (!section) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const heading = section.querySelector('[data-cta-part="heading"]');
      const sub = section.querySelector('[data-cta-part="sub"]');
      const button = section.querySelector('[data-cta-part="button"]');
      const glow = section.querySelector('[data-cta-part="glow"]');

      if (!heading) return;

      gsap.set(heading, { opacity: 0, y: 24, scale: 0.96, transformOrigin: "50% 50%" });
      gsap.set([sub, button], { opacity: 0, y: 16 });
      if (glow) gsap.set(glow, { opacity: 0, scale: 0.7 });

      const timeline = gsap.timeline({
        scrollTrigger: { trigger: section, start: "top 78%", once: true },
      });

      if (glow) timeline.to(glow, { opacity: 1, scale: 1, duration: 0.9, ease: "power2.out" }, 0);
      timeline
        .to(heading, { opacity: 1, y: 0, scale: 1, duration: 0.55, ease: "back.out(1.6)" }, 0.05)
        .to(sub, { opacity: 1, y: 0, duration: 0.4 }, "-=0.25")
        .to(button, { opacity: 1, y: 0, duration: 0.4 }, "-=0.2");
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section ref={sectionRef} className="relative overflow-hidden bg-gold-500 py-20 sm:py-24">
      <div
        data-cta-part="glow"
        aria-hidden="true"
        className="pointer-events-none absolute inset-0 flex items-center justify-center"
      >
        <div className="size-[36rem] rounded-full bg-[radial-gradient(circle,_rgba(6,15,13,0.14),_transparent_65%)]" />
      </div>

      <Container className="relative">
        <div className="mx-auto max-w-2xl text-center">
          <h2
            data-cta-part="heading"
            className="font-display text-3xl leading-[1.1] text-ink-950 sm:text-4xl"
          >
            See What Your Business Could Automate First.
          </h2>
          <p
            data-cta-part="sub"
            className="mt-5 text-base leading-relaxed text-ink-900/80 sm:text-lg"
          >
            A 30-minute AI Systems Audit shows you exactly where automation would save the most
            time, with no obligation and no jargon.
          </p>
          <div data-cta-part="button" className="mt-9 flex justify-center">
            <Button href="/contact" variant="primary" className="bg-ink-950 text-paper-50 hover:bg-ink-900">
              Book an AI Systems Audit
            </Button>
          </div>
        </div>
      </Container>
    </section>
  );
}

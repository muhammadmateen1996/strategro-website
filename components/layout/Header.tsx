"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { Menu, X, ChevronDown } from "lucide-react";
import { Container } from "@/components/ui/Container";
import { services } from "@/content/services";
import { siteConfig } from "@/lib/site-config";
import { cn } from "@/lib/cn";
import { Logo } from "@/components/layout/Logo";

const navLinks = [
  { label: "About", href: "/about" },
  { label: "Labs", href: "/labs/algorithmic-trading" },
  { label: "Insights", href: "/blog" },
  { label: "Contact", href: "/contact" },
];

export function Header() {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [servicesOpen, setServicesOpen] = useState(false);
  const pathname = usePathname();
  const [lastPathname, setLastPathname] = useState(pathname);

  if (pathname !== lastPathname) {
    setLastPathname(pathname);
    setMobileOpen(false);
    setServicesOpen(false);
  }

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    document.body.style.overflow = mobileOpen ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [mobileOpen]);

  return (
    <header
      className={cn(
        "sticky top-0 z-50 w-full bg-ink-950/95 backdrop-blur transition-shadow duration-300 supports-[backdrop-filter]:bg-ink-950/90",
        (scrolled || mobileOpen) && "shadow-[0_1px_0_0_rgba(250,248,242,0.08)]"
      )}
    >
      <Container className="flex h-20 items-center justify-between">
        <Link href="/" className="focus-ring shrink-0" aria-label="Strategro home">
          <Logo />
        </Link>

        <nav className="hidden items-center gap-1 lg:flex" aria-label="Primary">
          <div
            className="relative"
            onMouseEnter={() => setServicesOpen(true)}
            onMouseLeave={() => setServicesOpen(false)}
          >
            <button
              type="button"
              className="focus-ring flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium text-paper-100 transition-colors hover:text-gold-400"
              aria-expanded={servicesOpen}
              aria-haspopup="true"
              onClick={() => setServicesOpen((open) => !open)}
            >
              Services
              <ChevronDown className="size-3.5" aria-hidden="true" />
            </button>
            {servicesOpen && (
              <div className="absolute left-1/2 top-full w-96 -translate-x-1/2 pt-3">
                <div className="rounded-2xl border border-paper-50/10 bg-ink-900 p-3 shadow-2xl shadow-black/40">
                  <Link
                    href="/services"
                    className="focus-ring block rounded-xl px-4 py-2.5 text-sm font-semibold text-gold-400 hover:bg-ink-800"
                  >
                    All Services
                  </Link>
                  <div className="my-1 h-px bg-paper-50/10" />
                  {services.map((service) => (
                    <Link
                      key={service.slug}
                      href={`/services/${service.slug}`}
                      className="focus-ring block rounded-xl px-4 py-2.5 text-sm text-paper-100 hover:bg-ink-800 hover:text-paper-50"
                    >
                      {service.name}
                    </Link>
                  ))}
                </div>
              </div>
            )}
          </div>

          {navLinks.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="focus-ring rounded-full px-4 py-2 text-sm font-medium text-paper-100 transition-colors hover:text-gold-400"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        <div className="hidden lg:block">
          <Link
            href={siteConfig.ctaPrimary.href}
            className="focus-ring inline-flex items-center rounded-full bg-gold-500 px-5 py-2.5 text-sm font-semibold text-ink-950 transition-colors hover:bg-gold-400"
          >
            {siteConfig.ctaPrimary.label}
          </Link>
        </div>

        <button
          type="button"
          className="focus-ring inline-flex items-center justify-center rounded-full p-2 text-paper-50 lg:hidden"
          aria-label={mobileOpen ? "Close menu" : "Open menu"}
          aria-expanded={mobileOpen}
          onClick={() => setMobileOpen((open) => !open)}
        >
          {mobileOpen ? <X className="size-6" /> : <Menu className="size-6" />}
        </button>
      </Container>

      {mobileOpen && (
        <div className="border-t border-paper-50/10 bg-ink-950 lg:hidden">
          <Container className="flex flex-col gap-1 py-6">
            <p className="px-4 pb-2 text-xs font-semibold uppercase tracking-[0.2em] text-gold-500">
              Services
            </p>
            <Link
              href="/services"
              className="focus-ring rounded-xl px-4 py-3 text-base font-medium text-paper-50 hover:bg-ink-800"
            >
              All Services
            </Link>
            {services.map((service) => (
              <Link
                key={service.slug}
                href={`/services/${service.slug}`}
                className="focus-ring rounded-xl px-4 py-3 text-base text-paper-100 hover:bg-ink-800"
              >
                {service.name}
              </Link>
            ))}
            <div className="my-3 h-px bg-paper-50/10" />
            {navLinks.map((link) => (
              <Link
                key={link.href}
                href={link.href}
                className="focus-ring rounded-xl px-4 py-3 text-base font-medium text-paper-50 hover:bg-ink-800"
              >
                {link.label}
              </Link>
            ))}
            <Link
              href={siteConfig.ctaPrimary.href}
              className="focus-ring mt-4 inline-flex items-center justify-center rounded-full bg-gold-500 px-5 py-3 text-sm font-semibold text-ink-950"
            >
              {siteConfig.ctaPrimary.label}
            </Link>
          </Container>
        </div>
      )}
    </header>
  );
}

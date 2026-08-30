import type { Metadata } from "next";
import { Fraunces, Inter } from "next/font/google";
import Script from "next/script";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { PageTransition } from "@/components/motion/PageTransition";
import { siteConfig } from "@/lib/site-config";
import { organizationSchema, JsonLd } from "@/lib/jsonld";
import "./globals.css";

const inter = Inter({
  variable: "--font-inter",
  subsets: ["latin"],
  display: "swap",
});

const fraunces = Fraunces({
  variable: "--font-fraunces",
  subsets: ["latin"],
  axes: ["opsz", "SOFT", "WONK"],
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL(siteConfig.url),
  title: {
    default: `${siteConfig.name} — ${siteConfig.tagline}`,
    template: `%s | ${siteConfig.name}`,
  },
  description: siteConfig.description,
  openGraph: {
    type: "website",
    siteName: siteConfig.name,
    title: `${siteConfig.name} — ${siteConfig.tagline}`,
    description: siteConfig.description,
    url: siteConfig.url,
  },
  twitter: {
    card: "summary_large_image",
    title: `${siteConfig.name} — ${siteConfig.tagline}`,
    description: siteConfig.description,
  },
  alternates: {
    canonical: "/",
  },
  robots: {
    index: true,
    follow: true,
  },
};

export default function RootLayout({ children }: LayoutProps<"/">) {
  return (
    <html lang="en-GB" className={`${inter.variable} ${fraunces.variable} h-full`}>
      <body className="flex min-h-full flex-col bg-paper-50 text-ink-950 antialiased">
        <a
          href="#main-content"
          className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-gold-500 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-ink-950"
        >
          Skip to content
        </a>
        <Header />
        <main id="main-content" className="flex-1">
          <PageTransition>{children}</PageTransition>
        </main>
        <Footer />
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={JsonLd({ data: organizationSchema() })}
        />
        <Script
          src={siteConfig.clara.chatScriptUrl}
          data-key={siteConfig.clara.chatKey}
          strategy="lazyOnload"
        />
      </body>
    </html>
  );
}

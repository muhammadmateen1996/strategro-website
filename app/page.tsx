import type { Metadata } from "next";
import { Hero } from "@/components/sections/Hero";
import { TrustBar } from "@/components/sections/TrustBar";
import { ProblemsSection } from "@/components/sections/ProblemsSection";
import { ServicesSection } from "@/components/sections/ServicesSection";
import { HowItWorks } from "@/components/sections/HowItWorks";
import { UseCaseStories } from "@/components/sections/UseCaseStories";
import { KnowledgeAssistantSection } from "@/components/sections/KnowledgeAssistantSection";
import { CtaSection } from "@/components/sections/CtaSection";
import { InsightsPreview } from "@/components/sections/InsightsPreview";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: `${siteConfig.name} — Strategies That Grow`,
  description: siteConfig.description,
  alternates: { canonical: "/" },
};

export default function HomePage() {
  return (
    <>
      <Hero />
      <TrustBar />
      <ProblemsSection />
      <ServicesSection />
      <HowItWorks />
      <UseCaseStories />
      <KnowledgeAssistantSection />
      <CtaSection />
      <InsightsPreview />
    </>
  );
}

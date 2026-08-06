import type { LucideIcon } from "lucide-react";

export interface ServiceSummary {
  slug: string;
  name: string;
  outcome: string;
  description: string;
  icon: LucideIcon;
}

export interface ServiceDetail extends ServiceSummary {
  metaTitle: string;
  metaDescription: string;
  primaryKeywordTheme: string;
  heroSubhead: string;
  problems: string[];
  whatItIncludes: { title: string; description: string }[];
  workflow: { step: string; description: string }[];
  decisionCriteria: string[];
  faqs: { question: string; answer: string }[];
  relatedServiceSlugs: string[];
}

export interface UseCaseStory {
  slug: string;
  industry: string;
  title: string;
  scenario: string;
  before: string[];
  after: string[];
  systemFlow: string[];
}

export const siteConfig = {
  name: "Strategro",
  legalName: "Strategro Ltd",
  tagline: "Strategies That Grow",
  description:
    "Strategro designs AI automation, customer systems, and data-driven workflows for ambitious UK and UAE businesses.",
  url: process.env.NEXT_PUBLIC_SITE_URL || "https://www.strategro.com",
  calendlyUrl: process.env.NEXT_PUBLIC_CALENDLY_URL || "",
  markets: ["United Kingdom", "United Arab Emirates"],
  email: "hello@strategro.com",
  social: {
    linkedin: "https://www.linkedin.com/company/strategro",
  },
  ctaPrimary: {
    label: "Book an AI Systems Audit",
    href: "/contact",
  },
  ctaSecondary: {
    label: "Explore Our Systems",
    href: "/services",
  },
} as const;

export const primaryNav = [
  { label: "Services", href: "/services" },
  { label: "Labs", href: "/labs/algorithmic-trading" },
  { label: "About", href: "/about" },
  { label: "Insights", href: "/blog" },
  { label: "Contact", href: "/contact" },
] as const;

export const footerNav = {
  services: [
    { label: "AI Workflow Automation", href: "/services/ai-workflow-automation" },
    { label: "AI Chatbots", href: "/services/ai-chatbots" },
    { label: "Voice AI Receptionists", href: "/services/voice-ai-receptionists" },
    { label: "RAG Knowledge Assistants", href: "/services/rag-knowledge-assistants" },
    { label: "AI Lead & Content Systems", href: "/services/ai-lead-content-systems" },
  ],
  company: [
    { label: "About Strategro", href: "/about" },
    { label: "Algorithmic Trading Labs", href: "/labs/algorithmic-trading" },
    { label: "Insights", href: "/blog" },
    { label: "Contact", href: "/contact" },
  ],
  legal: [
    { label: "Privacy Policy", href: "/privacy-policy" },
    { label: "Terms of Service", href: "/terms" },
  ],
} as const;

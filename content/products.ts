import type { LucideIcon } from "lucide-react";
import { Bot, Truck, TrendingUp, FileSignature, ShoppingBag } from "lucide-react";

export interface Product {
  slug: string;
  name: string;
  url: string;
  tagline: string;
  description: string;
  audience: string;
  icon: LucideIcon;
}

export const products: Product[] = [
  {
    slug: "clara",
    name: "Clara",
    url: "https://clara.strategro.co.uk",
    tagline: "AI front desk",
    description:
      "An AI assistant that answers website visitors, captures enquiries through smart forms, and books appointments straight into your calendar.",
    audience: "Service businesses",
    icon: Bot,
  },
  {
    slug: "lodway",
    name: "Lodway",
    url: "https://lodway.com",
    tagline: "Courier operations",
    description:
      "Dispatch, a driver app, live tracking, proof of delivery and invoicing in one system, built for small UK courier firms. 14-day free trial.",
    audience: "UK courier companies",
    icon: Truck,
  },
  {
    slug: "seo",
    name: "Strategro SEO",
    url: "https://seo.strategro.co.uk",
    tagline: "SEO content engine",
    description:
      "Plans, writes and organises search-focused content, so your site keeps publishing useful pages without a full-time content team.",
    audience: "Growing websites",
    icon: TrendingUp,
  },
  {
    slug: "proposal",
    name: "Strategro Proposals",
    url: "https://proposal.strategro.co.uk",
    tagline: "Proposal automation",
    description:
      "Turns an enquiry into a polished, on-brand proposal in minutes instead of an afternoon of copy-and-paste.",
    audience: "Agencies & consultants",
    icon: FileSignature,
  },
  {
    slug: "shopops",
    name: "ShopOps",
    url: "https://shopops.strategro.co.uk",
    tagline: "E-commerce operations",
    description:
      "Runs an eBay dropshipping store from one place: product research, listing drafts, supplier sourcing and order tracking.",
    audience: "Online sellers",
    icon: ShoppingBag,
  },
];

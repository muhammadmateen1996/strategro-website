export interface WPRendered {
  rendered: string;
}

export interface WPPost {
  id: number;
  slug: string;
  date: string;
  modified: string;
  title: WPRendered;
  excerpt: WPRendered;
  content: WPRendered;
  categories: number[];
  author: number;
  featured_media: number;
  _embedded?: {
    author?: Array<{ id: number; name: string; avatar_urls?: Record<string, string> }>;
    "wp:featuredmedia"?: Array<{
      id: number;
      source_url: string;
      alt_text: string;
      media_details?: { width?: number; height?: number };
    }>;
    "wp:term"?: Array<Array<{ id: number; name: string; slug: string; taxonomy: string }>>;
  };
}

export interface WPCategory {
  id: number;
  name: string;
  slug: string;
  count: number;
}

export interface BlogPost {
  id: number | string;
  slug: string;
  title: string;
  excerpt: string;
  content: string;
  date: string;
  modifiedDate: string;
  author: string;
  categories: string[];
  featuredImage: { url: string; alt: string; width?: number; height?: number } | null;
  source: "wordpress" | "sample";
}

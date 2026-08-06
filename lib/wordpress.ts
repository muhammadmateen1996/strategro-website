import "server-only";
import type { BlogPost, WPCategory, WPPost } from "@/types/wordpress";
import { samplePosts } from "@/content/sample-posts";

const WORDPRESS_API_URL = process.env.WORDPRESS_API_URL?.replace(/\/$/, "");
const REVALIDATE_SECONDS = 3600;
const FETCH_TIMEOUT_MS = 6000;

function stripHtml(html: string): string {
  return html
    .replace(/<[^>]*>/g, " ")
    .replace(/&nbsp;/g, " ")
    .replace(/&amp;/g, "&")
    .replace(/\s+/g, " ")
    .trim();
}

function mapPost(post: WPPost): BlogPost {
  const author = post._embedded?.author?.[0]?.name ?? "Strategro Team";
  const media = post._embedded?.["wp:featuredmedia"]?.[0];
  const terms = post._embedded?.["wp:term"]?.flat() ?? [];
  const categories = terms
    .filter((term) => term.taxonomy === "category")
    .map((term) => term.name);

  return {
    id: post.id,
    slug: post.slug,
    title: stripHtml(post.title.rendered),
    excerpt: stripHtml(post.excerpt.rendered),
    content: post.content.rendered,
    date: post.date,
    modifiedDate: post.modified,
    author,
    categories: categories.length ? categories : ["Insights"],
    featuredImage: media
      ? {
          url: media.source_url,
          alt: media.alt_text || stripHtml(post.title.rendered),
          width: media.media_details?.width,
          height: media.media_details?.height,
        }
      : null,
    source: "wordpress",
  };
}

async function wpFetch<T>(path: string): Promise<T | null> {
  if (!WORDPRESS_API_URL) return null;

  try {
    const response = await fetch(`${WORDPRESS_API_URL}${path}`, {
      next: { revalidate: REVALIDATE_SECONDS, tags: ["wordpress"] },
      signal: AbortSignal.timeout(FETCH_TIMEOUT_MS),
    });

    if (!response.ok) {
      console.error(`WordPress API responded with ${response.status} for ${path}`);
      return null;
    }

    return (await response.json()) as T;
  } catch (error) {
    console.error(`WordPress API request failed for ${path}:`, error);
    return null;
  }
}

export async function getPosts(options?: {
  page?: number;
  perPage?: number;
}): Promise<{ posts: BlogPost[]; isFallback: boolean }> {
  const page = options?.page ?? 1;
  const perPage = options?.perPage ?? 9;

  const posts = await wpFetch<WPPost[]>(
    `/posts?_embed=author,wp:featuredmedia,wp:term&per_page=${perPage}&page=${page}&orderby=date&order=desc`
  );

  if (posts && posts.length >= 0) {
    return { posts: posts.map(mapPost), isFallback: false };
  }

  const start = (page - 1) * perPage;
  return {
    posts: samplePosts.slice(start, start + perPage),
    isFallback: true,
  };
}

export async function getPostBySlug(slug: string): Promise<BlogPost | null> {
  const posts = await wpFetch<WPPost[]>(
    `/posts?_embed=author,wp:featuredmedia,wp:term&slug=${encodeURIComponent(slug)}`
  );

  if (posts) {
    return posts[0] ? mapPost(posts[0]) : null;
  }

  return samplePosts.find((post) => post.slug === slug) ?? null;
}

export async function getAllPostSlugs(): Promise<string[]> {
  const posts = await wpFetch<WPPost[]>("/posts?per_page=100&_fields=slug");

  if (posts) {
    return posts.map((post) => post.slug);
  }

  return samplePosts.map((post) => post.slug);
}

export async function getCategories(): Promise<WPCategory[]> {
  const categories = await wpFetch<WPCategory[]>("/categories?per_page=50&hide_empty=true");
  return categories ?? [];
}

export function isWordPressConfigured(): boolean {
  return Boolean(WORDPRESS_API_URL);
}

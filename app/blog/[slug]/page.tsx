import type { Metadata } from "next";
import Link from "next/link";
import Image from "next/image";
import { notFound } from "next/navigation";
import { Container } from "@/components/ui/Container";
import { Button } from "@/components/ui/Button";
import { Reveal } from "@/components/motion/Reveal";
import { formatDate } from "@/lib/format";
import { getAllPostSlugs, getPostBySlug } from "@/lib/wordpress";
import { JsonLd, articleSchema, breadcrumbSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export async function generateStaticParams() {
  const slugs = await getAllPostSlugs();
  return slugs.map((slug) => ({ slug }));
}

export async function generateMetadata({
  params,
}: PageProps<"/blog/[slug]">): Promise<Metadata> {
  const { slug } = await params;
  const post = await getPostBySlug(slug);
  if (!post) return {};

  return {
    title: post.title,
    description: post.excerpt,
    alternates: { canonical: `/blog/${post.slug}` },
    openGraph: {
      type: "article",
      title: post.title,
      description: post.excerpt,
      url: `/blog/${post.slug}`,
      publishedTime: post.date,
      modifiedTime: post.modifiedDate,
      images: post.featuredImage ? [post.featuredImage.url] : undefined,
    },
  };
}

export default async function BlogPostPage({ params }: PageProps<"/blog/[slug]">) {
  const { slug } = await params;
  const post = await getPostBySlug(slug);
  if (!post) notFound();

  const breadcrumb = breadcrumbSchema([
    { name: "Home", url: siteConfig.url },
    { name: "Insights", url: `${siteConfig.url}/blog` },
    { name: post.title, url: `${siteConfig.url}/blog/${post.slug}` },
  ]);

  const article = articleSchema({
    headline: post.title,
    description: post.excerpt,
    url: `${siteConfig.url}/blog/${post.slug}`,
    datePublished: post.date,
    dateModified: post.modifiedDate,
    authorName: post.author,
    imageUrl: post.featuredImage?.url,
  });

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: breadcrumb })} />
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: article })} />

      <article className="bg-paper-50 py-20 sm:py-24">
        <Container className="max-w-3xl">
          <nav aria-label="Breadcrumb" className="mb-8 text-xs text-ink-700/70">
            <Link href="/blog" className="focus-ring hover:text-gold-600">
              Insights
            </Link>
            <span className="mx-2" aria-hidden="true">
              /
            </span>
            <span>{post.title}</span>
          </nav>

          <Reveal>
            <div className="flex items-center gap-3 text-xs font-medium uppercase tracking-[0.15em] text-gold-600">
              <span>{post.categories[0]}</span>
              <span aria-hidden="true">&middot;</span>
              <time dateTime={post.date}>{formatDate(post.date)}</time>
            </div>

            <h1 className="mt-4 font-display text-3xl leading-[1.15] text-ink-950 sm:text-4xl">
              {post.title}
            </h1>
            <p className="mt-4 text-sm text-ink-700/70">By {post.author}</p>

            {post.featuredImage && (
              <div className="relative mt-8 aspect-[16/9] w-full overflow-hidden rounded-2xl bg-ink-900">
                <Image
                  src={post.featuredImage.url}
                  alt={post.featuredImage.alt}
                  fill
                  priority
                  sizes="(min-width: 1024px) 768px, 100vw"
                  className="object-cover"
                />
              </div>
            )}
          </Reveal>

          <div
            className="prose prose-neutral mt-10 max-w-none prose-headings:font-display prose-a:text-gold-600 prose-a:no-underline hover:prose-a:underline"
            dangerouslySetInnerHTML={{ __html: post.content }}
          />

          <Reveal className="mt-14 flex flex-col items-start gap-4 rounded-2xl border border-ink-950/8 bg-white p-8 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h2 className="font-display text-xl text-ink-950">
                See what your business could automate first.
              </h2>
              <p className="mt-1.5 text-sm text-ink-700">A free, no-obligation AI Systems Audit.</p>
            </div>
            <Button href="/contact" className="shrink-0">
              Book a call
            </Button>
          </Reveal>
        </Container>
      </article>
    </>
  );
}

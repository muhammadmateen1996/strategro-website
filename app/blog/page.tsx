import type { Metadata } from "next";
import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { BlogCard } from "@/components/blog/BlogCard";
import { BlogEmptyState } from "@/components/blog/EmptyState";
import { getPosts } from "@/lib/wordpress";
import { JsonLd, breadcrumbSchema } from "@/lib/jsonld";
import { siteConfig } from "@/lib/site-config";

export const metadata: Metadata = {
  title: "Insights",
  description:
    "Practical thinking from Strategro on AI automation, chatbots, voice AI, and knowledge assistants for UK and UAE businesses.",
  alternates: { canonical: "/blog" },
};

export default async function BlogIndexPage() {
  const { posts } = await getPosts({ perPage: 24 });
  const breadcrumb = breadcrumbSchema([
    { name: "Home", url: siteConfig.url },
    { name: "Insights", url: `${siteConfig.url}/blog` },
  ]);

  return (
    <>
      <script type="application/ld+json" dangerouslySetInnerHTML={JsonLd({ data: breadcrumb })} />

      <section className="bg-ink-950 py-20 sm:py-24">
        <Container>
          <SectionHeading
            eyebrow="Insights"
            tone="light"
            as="h1"
            title="Practical thinking on AI automation."
            description="Notes on what actually works when connecting AI to real business operations, written for people making the decision, not for hype."
          />
        </Container>
      </section>

      <section className="bg-paper-50 py-20">
        <Container>
          {posts.length > 0 ? (
            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {posts.map((post) => (
                <BlogCard key={post.id} post={post} />
              ))}
            </div>
          ) : (
            <BlogEmptyState />
          )}
        </Container>
      </section>
    </>
  );
}

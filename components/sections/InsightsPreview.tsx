import { Container } from "@/components/ui/Container";
import { SectionHeading } from "@/components/ui/SectionHeading";
import { Button } from "@/components/ui/Button";
import { BlogCard } from "@/components/blog/BlogCard";
import { BlogEmptyState } from "@/components/blog/EmptyState";
import { StaggerGrid } from "@/components/motion/StaggerGrid";
import { getPosts } from "@/lib/wordpress";

export async function InsightsPreview() {
  const { posts } = await getPosts({ perPage: 3 });

  return (
    <section className="bg-paper-50 py-24 sm:py-28">
      <Container>
        <div className="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
          <SectionHeading
            eyebrow="Insights"
            title="Practical thinking on AI automation."
            description="Notes on what actually works when connecting AI to real business operations."
          />
          <Button href="/blog" variant="ghost" className="shrink-0">
            View all insights
          </Button>
        </div>

        <div className="mt-12">
          {posts.length > 0 ? (
            <StaggerGrid className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {posts.map((post) => (
                <div key={post.id} data-stagger-item>
                  <BlogCard post={post} />
                </div>
              ))}
            </StaggerGrid>
          ) : (
            <BlogEmptyState />
          )}
        </div>
      </Container>
    </section>
  );
}

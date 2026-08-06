import Link from "next/link";
import Image from "next/image";
import { ArrowRight } from "lucide-react";
import { formatDate } from "@/lib/format";
import type { BlogPost } from "@/types/wordpress";

export function BlogCard({ post }: { post: BlogPost }) {
  return (
    <article className="group flex h-full flex-col overflow-hidden rounded-2xl border border-ink-950/8 bg-white">
      <Link href={`/blog/${post.slug}`} className="focus-ring flex h-full flex-col">
        <div className="relative aspect-[16/10] w-full overflow-hidden bg-ink-900">
          {post.featuredImage ? (
            <Image
              src={post.featuredImage.url}
              alt={post.featuredImage.alt}
              fill
              sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
              className="object-cover transition-transform duration-500 group-hover:scale-105"
            />
          ) : (
            <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-ink-900 to-ink-800">
              <span className="font-display text-2xl text-gold-500/50">S</span>
            </div>
          )}
        </div>
        <div className="flex flex-1 flex-col p-6">
          <div className="flex items-center gap-3 text-xs font-medium uppercase tracking-[0.15em] text-gold-600">
            <span>{post.categories[0]}</span>
            <span aria-hidden="true">&middot;</span>
            <time dateTime={post.date}>{formatDate(post.date)}</time>
          </div>
          <h3 className="mt-3 font-display text-lg leading-snug text-ink-950">{post.title}</h3>
          <p className="mt-2.5 line-clamp-3 flex-1 text-sm leading-relaxed text-ink-700">{post.excerpt}</p>
          <span className="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-gold-600">
            Read more
            <ArrowRight className="size-3.5 transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true" />
          </span>
        </div>
      </Link>
    </article>
  );
}

# Content Guide: Publishing to the Insights Blog

The `/blog` section of this site reads from WordPress as a **headless CMS**
via its REST API — WordPress stays the place editors publish from, but it no
longer renders any of the page templates. This guide covers what to do in
WordPress for a post to show up correctly here.

## Before you start

The frontend only shows posts once `WORDPRESS_API_URL` is configured in the
deployment (see `DEPLOYMENT.md` §6). Until then, `/blog` shows local sample
content so the site remains demoable.

## Publishing a post

1. **Write and publish the post in WordPress as normal** (Posts → Add New).
   No plugin or special post type is required — this uses WordPress's
   standard REST API (`/wp-json/wp/v2/posts`), enabled by default.
2. **Set a featured image.** It's used as the card image on `/blog` and the
   hero image on the post page. Posts without one still work — they show a
   plain branded placeholder instead of a broken image.
3. **Fill in the excerpt**, or leave it blank to let WordPress auto-generate
   one from the content. The excerpt appears on `/blog` cards and in the
   page's meta description.
4. **Assign at least one category.** The first assigned category is shown as
   the post's tag on both the card and the post page. Posts with no category
   are labelled "Insights" by default.
5. **Publish.** That's it — no separate "publish to frontend" step.

## How fast changes appear

The frontend caches WordPress responses for **up to one hour** (via Next.js
revalidation) to avoid hitting the WordPress API on every page view. This
means:

- A newly published post can take up to an hour to appear on `/blog` and in
  the sitemap.
- Edits to an already-published post can take up to an hour to show.
- There is no manual "flush cache" step required — it resolves on its own.

If you need a change to appear immediately, redeploying the frontend forces
a fresh fetch on the next request.

## What's pulled from WordPress, and how it's used

| WordPress field | Used for |
| --- | --- |
| Title | Page `<h1>`, card heading, `<title>` tag |
| Slug | URL: `/blog/your-slug` — keep slugs stable once published, since changing one changes the URL |
| Excerpt | Card preview text, meta description |
| Content | Full post body (headings, paragraphs, images, links render as authored) |
| Featured image | Card image, post hero image, Open Graph image |
| Author display name | Byline on the post page |
| Categories | Tag label shown on card and post page |
| Published/modified date | Displayed date, and `Article` structured data for SEO |

## Content guidelines for good results on this frontend

- **Use real headings (H2/H3) in the WordPress editor**, not bold text
  pretending to be a heading — the frontend renders your content's actual
  heading levels, and search engines read heading structure directly.
- **Avoid inline styles or theme-specific shortcodes** from the old
  WordPress theme — since WordPress is now content-only, any shortcode that
  depended on the old theme's PHP/JS won't render here. Stick to standard
  block editor content (paragraphs, headings, lists, images, links, quotes).
- **Keep slugs permanent once published** — the slug is the URL. If a slug
  must change, add a redirect (see `SEO-MIGRATION.md` §2).
- **Featured images should be reasonably sized** (under ~500KB, ideally
  already sensible dimensions) — the frontend serves them through
  `next/image` for optimisation, but very large source files still cost
  more at the origin fetch.

## If WordPress becomes unreachable

If the WordPress API is down or misconfigured, `/blog` and individual post
pages automatically fall back to local sample content rather than showing an
error page. This is intentional — it keeps the site up even during a
WordPress outage — but it does mean genuinely new content won't appear until
the connection is restored. Check `WORDPRESS_API_URL` and WordPress's own
uptime first if posts stop updating.

# SEO Migration Plan

This document covers what's needed to preserve search visibility when
cutting over from the existing WordPress site to this Next.js frontend. It's
a plan to follow at cutover time, not something this repository does
automatically.

## 1. Preserving important WordPress URLs

Before cutover, export a full list of currently-indexed URLs from the live
WordPress site:

1. Export the URL list from Google Search Console (**Pages** report, or the
   **Coverage** report) — this reflects what's actually indexed, which
   matters more than every URL that technically exists.
2. Cross-reference against the WordPress XML sitemap (typically
   `/sitemap.xml` or `/sitemap_index.xml` if Yoast/RankMath is installed).
3. Group URLs by type: pages, blog posts, category/tag archives, and any
   custom post types.

This site's route structure is:

| Content type | New route pattern |
| --- | --- |
| Homepage | `/` |
| Services overview | `/services` |
| Individual services | `/services/ai-workflow-automation`, `/services/ai-chatbots`, `/services/voice-ai-receptionists`, `/services/rag-knowledge-assistants`, `/services/ai-lead-content-systems` |
| Labs | `/labs/algorithmic-trading` |
| About | `/about` |
| Blog index | `/blog` |
| Blog posts | `/blog/[slug]` (slug is preserved from WordPress) |
| Contact | `/contact` |
| Legal | `/privacy-policy`, `/terms` |

Blog post slugs are pulled directly from WordPress via the REST API, so
**existing post URLs under `/blog/[slug]` will match automatically** as long
as the WordPress slug is unchanged. The types of URLs that need explicit
redirects are ones whose *path structure* changes — e.g. if the current site
serves posts at `/news/post-title` rather than `/blog/post-title`, or serves
service pages at different paths than the list above.

## 2. 301 redirects — only where URLs genuinely change

Do not create redirects for URLs that already match the new structure
one-to-one (e.g. an existing `/blog/why-x` post staying at `/blog/why-x`
needs nothing).

For URLs whose path changes, add 301 redirects at the edge — either in
Coolify's reverse proxy configuration, or via Next.js `redirects()` in
`next.config.ts` if you'd rather manage them in code and version-control
them alongside the rest of the site. Example pattern once real legacy paths
are known:

```ts
// next.config.ts
const nextConfig: NextConfig = {
  // ...existing config
  async redirects() {
    return [
      { source: "/news/:slug", destination: "/blog/:slug", permanent: true },
      { source: "/our-services", destination: "/services", permanent: true },
      // Add one entry per legacy path that no longer matches the new structure.
    ];
  },
};
```

**Action needed before cutover:** pull the actual list of live WordPress
URLs (per §1) and diff it against the route table above to build this list.
This can't be completed from within this repository alone, since it depends
on the current live site's real URL structure.

## 3. Canonical domain configuration

1. Decide the canonical domain (e.g. `https://www.strategro.com` vs.
   `https://strategro.com` — pick one and redirect the other).
2. Set `NEXT_PUBLIC_SITE_URL` to the canonical form. This drives:
   - `<link rel="canonical">` on every page (via `metadataBase` in
     `app/layout.tsx`)
   - Open Graph `url` metadata
   - `sitemap.xml` entries
3. Configure a redirect from the non-canonical host (e.g. bare domain →
   `www`, or vice versa) at the DNS/reverse-proxy level in Coolify.
4. Ensure only one version of the site is reachable over both `http` and
   `https` without a redirect loop — Coolify's automatic TLS handles the
   `http → https` redirect; the `www`/non-`www` choice is a separate step.

## 4. Sitemap submission

1. Once live, confirm `https://<your-domain>/sitemap.xml` returns a valid
   sitemap (it's generated dynamically from `app/sitemap.ts`, covering
   static pages, all service pages, and every WordPress post slug).
2. In Google Search Console (and Bing Webmaster Tools, if used):
   - Submit the new sitemap URL under **Sitemaps**.
   - Do **not** delete the old WordPress sitemap submission until the new
     site has been live and redirects verified for at least a few weeks —
     keeping both visible helps Search Console reflect the transition
     accurately.

## 5. Search Console checks

After cutover:

1. **URL Inspection tool:** spot-check 10–15 of the most important legacy
   URLs (homepage, top service pages, top-performing blog posts) to confirm
   they either resolve directly or redirect correctly, and that Google can
   fetch and index the new URL.
2. **Coverage/Pages report:** monitor for a spike in `404` or
   `Page with redirect` errors in the two weeks after cutover — this
   usually indicates a missed redirect from §2.
3. **Performance report:** compare click-through and impression trends for
   your top 20 queries before/after cutover, filtering by page, to catch any
   page that lost visibility unexpectedly.
4. **Core Web Vitals report:** the new site is built for strong Core Web
   Vitals (static generation, minimal client JS, `next/image`), but confirm
   real-user data after a few weeks of traffic.
5. Re-verify domain ownership in Search Console if the canonical domain
   changes (§3) — a new property may be required for a scheme/subdomain
   change.

## 6. Rollback safety

Because DNS cutover is a separate, manual step from deployment (see
`DEPLOYMENT.md` §5), reverting to the WordPress site during an SEO issue is
simply a DNS change back to the previous target — no data loss, since
WordPress itself is untouched throughout.

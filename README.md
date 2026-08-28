# Strategro Website

Marketing website for Strategro Ltd — "Strategies That Grow." Built with Next.js
(App Router), TypeScript, and Tailwind CSS, with WordPress available as an
optional headless CMS for the Insights (blog) section.

This project lives on the `revamp/strategro-nextjs-preview` branch and is a
standalone redesign — it does not touch or depend on any existing WordPress
installation in this repository.

## Tech stack

- **Framework:** Next.js 16 (App Router, TypeScript, standalone output)
- **Styling:** Tailwind CSS v4
- **Animation:** Framer Motion (scroll reveals, UI transitions), CSS keyframes
  for lightweight diagram animation
- **Icons:** Lucide
- **CMS:** WordPress REST API (optional, headless) with local sample-content
  fallback
- **Chat, contact form & booking:** Clara (embedded chat widget, contact form,
  and booking calendar, hosted at clara.strategro.co.uk)

## Project structure

```
app/                      Routes (App Router)
  services/                /services and /services/[slug]
  labs/algorithmic-trading /labs/algorithmic-trading
  blog/                    /blog and /blog/[slug]
  contact/, about/, privacy-policy/, terms/
  api/health/              Health check for Coolify / load balancers
  opengraph-image.tsx      Generated social share image
  sitemap.ts, robots.ts    Generated SEO files
  layout.tsx, globals.css  Root layout, fonts, Tailwind theme, Clara chat widget

components/
  layout/                  Header, Footer, Logo
  ui/                      Button, Container, SectionHeading
  motion/                  Reveal (scroll-in fade), ScrollCinematic (GSAP hero pin)
  graphics/                Original SVG/GSAP diagrams (hero, problems, RAG, per-service)
  sections/                Homepage sections
  blog/                    BlogCard, empty state

content/                   Structured copy: services, use-case examples, sample posts
lib/                       wordpress.ts, jsonld.ts, site-config.ts (incl. Clara URLs/key)
types/                     Shared TypeScript types
```

## Local development

```bash
npm install
cp .env.example .env.local   # fill in values as needed — all are optional locally
npm run dev
```

Open [http://localhost:3000](http://localhost:3000).

With no environment variables set, the site runs fully functional using:

- **Blog:** local sample posts (`content/sample-posts.ts`) instead of WordPress
- **Chat, contact form & booking:** Clara's live Strategro instance (the
  defaults baked into `lib/site-config.ts`)

This means the project is reviewable and demoable out of the box, before any
WordPress connection exists.

### Quality checks

```bash
npm run lint      # ESLint
npx tsc --noEmit  # TypeScript
npm run build     # Production build
```

## Environment variables

See [`.env.example`](./.env.example) for the full list. Summary:

| Variable | Required | Purpose |
| --- | --- | --- |
| `WORDPRESS_API_URL` | No | Base WP REST API URL. Unset = sample blog content. |
| `NEXT_PUBLIC_SITE_URL` | Recommended | Canonical domain for metadata, sitemap, OG tags. |
| `NEXT_PUBLIC_CLARA_CHAT_KEY` | No | Overrides the default Clara chat widget key. |
| `NEXT_PUBLIC_CLARA_FORM_URL` | No | Overrides the default Clara contact form URL. |
| `NEXT_PUBLIC_CLARA_BOOKING_URL` | No | Overrides the default Clara booking URL. |
| `NEXT_PUBLIC_GA_ID` | No | Analytics, if used. |

None of these are secrets that need to exist for the site to build or run —
everything degrades gracefully when unset.

## Deployment

See [`DEPLOYMENT.md`](./DEPLOYMENT.md) for the full Coolify deployment guide,
including preview vs. production environments and rollback steps.

See [`SEO-MIGRATION.md`](./SEO-MIGRATION.md) for the legacy WordPress URL
redirect plan.

See [`CONTENT-GUIDE.md`](./CONTENT-GUIDE.md) for how to publish a WordPress
post so it appears on this frontend.

## Quick Coolify deployment summary

1. Push this branch and open a PR (or point Coolify at the branch directly
   for a preview deployment).
2. In Coolify, create a new **Dockerfile**-based application pointing at this
   repository and the `revamp/strategro-nextjs-preview` branch.
3. Set the build/runtime environment variables listed above (all optional to
   start with).
4. Coolify will build using the included `Dockerfile` (Next.js standalone
   output) and expose the app on port `3000`.
5. Point Coolify's health check at `GET /api/health`.
6. Deploy to a **preview domain first**. Do not point the production domain
   at this deployment until it's been reviewed.

Full details, including how to safely cut over the production domain later,
are in [`DEPLOYMENT.md`](./DEPLOYMENT.md).

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
- **Validation:** Zod
- **CMS:** WordPress REST API (optional, headless) with local sample-content
  fallback
- **Lead delivery:** n8n webhook (server-to-server only)

## Project structure

```
app/                      Routes (App Router)
  services/                /services and /services/[slug]
  labs/algorithmic-trading /labs/algorithmic-trading
  blog/                    /blog and /blog/[slug]
  contact/, about/, privacy-policy/, terms/
  api/contact/             Contact form submission endpoint
  api/health/              Health check for Coolify / load balancers
  sitemap.ts, robots.ts    Generated SEO files
  layout.tsx, globals.css  Root layout, fonts, Tailwind theme

components/
  layout/                  Header, Footer, Logo
  ui/                      Button, Container, SectionHeading
  motion/                  Reveal (scroll-in animation wrapper)
  graphics/                Original SVG system diagrams (hero, problems, RAG)
  sections/                Homepage sections
  blog/                    BlogCard, empty state
  contact/                 ContactForm (client component)

content/                   Structured copy: services, use-case examples, sample posts
lib/                       wordpress.ts, n8n.ts, validation.ts, jsonld.ts, site-config.ts
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
- **Contact page:** a mailto/booking fallback panel instead of the live form,
  since there's no webhook configured to receive submissions

This means the project is reviewable and demoable out of the box, before any
WordPress or n8n connection exists.

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
| `NEXT_PUBLIC_CALENDLY_URL` | No | Booking link shown as a contact fallback. |
| `N8N_LEAD_WEBHOOK_URL` | No | Server-only. Unset = contact page shows a fallback panel instead of the form. |
| `N8N_LEAD_WEBHOOK_SECRET` | No | Signs lead payloads with an HMAC-SHA256 header when set. |
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

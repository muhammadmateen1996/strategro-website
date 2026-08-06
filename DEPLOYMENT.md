# Deployment Guide

This guide covers deploying the Strategro Next.js site to Coolify, from a
preview environment through to a production domain switch, plus rollback.

**Nothing in this repository deploys, changes DNS, or touches the existing
WordPress installation automatically.** Every step below is manual and under
your control.

---

## 1. GitHub

1. This project lives on the `revamp/strategro-nextjs-preview` branch.
2. Push the branch to GitHub (already done if you're reading this from the
   repository).
3. Optionally open a pull request into your default branch once the preview
   has been reviewed and approved — do not merge before that review.
4. Coolify can either track a specific branch directly, or you can point it
   at a PR/tag once you're ready to promote further. For the initial
   preview, pointing Coolify at `revamp/strategro-nextjs-preview` directly is
   simplest.

---

## 2. Coolify: create the application

1. In Coolify, create a **new resource → Application**.
2. Source: this GitHub repository, branch `revamp/strategro-nextjs-preview`.
3. Build pack: **Dockerfile** (the repository includes a production-ready
   `Dockerfile` using Next.js's `standalone` output — no Coolify-side Nixpacks
   configuration is required).
4. Port: **3000** (matches `EXPOSE 3000` and `ENV PORT=3000` in the
   `Dockerfile`).
5. Health check path: `/api/health` (returns `{ "status": "ok" }` with a
   `200`).

## 3. Environment variables

Set these in Coolify's environment variable panel for the application. None
are required for a first deploy — the site runs in a fully functional
fallback mode without any of them (sample blog content, contact-fallback
panel instead of a live form).

| Variable | Scope | Notes |
| --- | --- | --- |
| `NEXT_PUBLIC_SITE_URL` | Build + Runtime | Set to the **preview** domain initially, e.g. `https://strategro-preview.yourdomain.com`. Update and redeploy when you cut over to production. |
| `WORDPRESS_API_URL` | Build + Runtime | Only set once you have a headless WordPress instance to connect (see §6). |
| `NEXT_PUBLIC_CALENDLY_URL` | Build + Runtime | Optional booking link fallback. |
| `N8N_LEAD_WEBHOOK_URL` | Runtime only | Server-only — never exposed to the browser. Leave unset until your n8n workflow is ready to receive leads. |
| `N8N_LEAD_WEBHOOK_SECRET` | Runtime only | Optional HMAC signing secret for the webhook payload. |
| `NEXT_PUBLIC_GA_ID` | Build + Runtime | Optional analytics ID. |

Because `NEXT_PUBLIC_*` variables are inlined at build time, changing them
requires a rebuild, not just a redeploy/restart — Coolify handles this
automatically when you update a build-time variable and redeploy.

## 4. Deploy to a preview domain

1. Assign Coolify's generated preview domain (or a subdomain you control,
   e.g. `preview.strategro.com`) to this application. **Do not** assign your
   live production domain at this stage.
2. Trigger a deploy. Coolify will build the Docker image and start the
   container.
3. Verify:
   - `GET /api/health` returns `200`
   - Homepage, all service pages, `/about`, `/blog`, `/contact`,
     `/privacy-policy`, `/terms` all load
   - `/sitemap.xml` and `/robots.txt` return the preview domain's URLs
     correctly (confirming `NEXT_PUBLIC_SITE_URL` is set correctly)
4. Share the preview URL for review. Nothing about this step affects the
   live WordPress site or DNS.

## 5. Production domain switch (only after explicit approval)

This step is intentionally manual and separate from deployment:

1. Confirm the preview build has been reviewed and approved.
2. Update `NEXT_PUBLIC_SITE_URL` to the real production domain and redeploy
   (this changes canonical URLs, sitemap, and Open Graph metadata).
3. In Coolify, add the production domain to the application and issue/attach
   its TLS certificate.
4. **DNS cutover** (outside Coolify, in your DNS provider): update the
   production domain's DNS record (A/CNAME) to point at the new Coolify
   application, or adjust your reverse proxy/load balancer routing —
   whichever matches how the current WordPress site is served.
5. If the legacy WordPress site should remain reachable at a different
   hostname (e.g. for reference or the WordPress admin/headless API), leave
   its DNS record untouched and only repoint the domain(s) actually meant
   for the new frontend.
6. See [`SEO-MIGRATION.md`](./SEO-MIGRATION.md) before cutting over — it
   covers redirects for any URLs that change shape.

## 6. Connecting WordPress as a headless CMS

1. Ensure the WordPress REST API is reachable at
   `https://your-wp-domain/wp-json/wp/v2` (this is enabled by default in
   modern WordPress; no plugin is required for basic posts/categories).
2. Set `WORDPRESS_API_URL` to that base URL in Coolify and redeploy.
3. The `/blog` and `/blog/[slug]` routes will start pulling real posts
   (cached for up to one hour, via Next.js's `revalidate`), and the sitemap
   will include real post slugs.
4. If the WordPress API becomes unreachable at any point, the site falls
   back to local sample content automatically rather than erroring — see
   `lib/wordpress.ts`.
5. See [`CONTENT-GUIDE.md`](./CONTENT-GUIDE.md) for the editorial side of
   this (what fields WordPress needs to populate for the frontend to render
   correctly).

## 7. Rollback process

Coolify keeps previous deployments/images. To roll back:

1. In the application's **Deployments** tab, locate the last known-good
   deployment.
2. Use Coolify's **Redeploy** action on that prior deployment, or redeploy
   from the corresponding Git commit SHA.
3. If a bad deploy was caused by an environment variable change, revert the
   variable first, then redeploy.
4. Because this application is stateless (no database — content comes from
   WordPress or build-time sample data), rollback is safe and has no data
   migration implications.
5. If the issue is DNS-level (e.g. a bad production cutover), revert the DNS
   record to point back at the previous site while you investigate — DNS
   changes are outside Coolify and are the fastest way to fully back out of
   a cutover.

## 8. What this deployment does *not* do

- It does not modify, delete, or restructure any existing WordPress files or
  database.
- It does not change DNS, Coolify configuration for other applications, or
  WordPress settings on its own — every step above is a deliberate, manual
  action.
- It does not require or store any WordPress admin credentials, SSH keys, or
  production secrets in this repository.

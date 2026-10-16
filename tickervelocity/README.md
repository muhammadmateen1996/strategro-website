# TickerVelocity WordPress theme

A fast, credible finance and markets news theme built for automated
publishing with n8n. No page builder, no jQuery: the homepage, sections and
articles fill themselves from your posts.

- **Homepage:** lead story (with its key takeaways) and top stories, today's
  Morning Brief, a row per section, newsletter band, latest news with a
  markets table, and a "How we report" trust strip.
- **Articles:** key takeaways, market impact (tickers, direction, sentiment,
  time frame), contents list, a newsletter box mid-article, a geo-gated
  partner offer, sources, disclaimer and AI note, author box, related
  stories. Reading progress bar and share buttons.
- **Market strip** under the header: crypto from CoinGecko (free), plus
  indices, stocks, FX and commodities pushed by n8n. Marked as delayed, with
  the time of the last update.
- **Newsletter capture** that keeps every signup in the admin (Subscribers,
  with CSV export) and forwards it to n8n, so you can use any email tool.
- **Partner offers** (exchange and broker referral links) with tidy
  `/go/name/` links, click counts, clear labelling, risk warnings and
  country blocking (UK and US blocked by default).
- **SEO:** NewsArticle and breadcrumb structured data, Open Graph tags, a
  Google News sitemap at `/news-sitemap.xml`, thin pages kept out of the
  index, author profiles. Defers to Rank Math, Yoast or similar if installed.
- **Speed:** fonts served from the site (Newsreader, Inter, JetBrains Mono),
  preloaded, about 10 KB of JavaScript, block styles only where used.

The strategy behind it (positioning, sections, monetisation, compliance,
stack and launch plan) is in **[BLUEPRINT.md](BLUEPRINT.md)**.

## Install

1. Appearance › Themes › Add New › Upload Theme › `dist/tickervelocity-theme.zip`
   › Install › Activate.
2. **TickerVelocity › Setup › Set up TickerVelocity.** Creates:
   - sections with sub-sections: Markets, Crypto, Macro, Commodities,
     Biotech, Regulation, Layoffs, Daily Brief;
   - pages: Home, Latest, About, Editorial standards and AI policy,
     Disclaimer, Affiliate disclosure, Privacy policy, Terms, Contact,
     Newsletter;
   - the header and footer menus, and the homepage settings.

   It deletes nothing except WordPress's untouched "Hello world!" post and
   "Sample Page".
3. Optional: **Add sample articles** to preview every layout. Remove them
   from the same page before launch.
4. Appearance › Customize › **TickerVelocity**: editorial email, social
   links, newsletter name and webhook, disclaimers.
5. Users › Profile: real name, photo, bio, job title, LinkedIn (shown on
   articles and author pages; this matters for trust in finance).
6. Review the legal pages and replace everything in [brackets].

The market strip stays hidden until it has real prices. The theme never
shows made-up numbers.

## Connecting n8n

### Credentials

1. Users › Add New: a user called for example "TickerVelocity Desk", role
   **Editor**.
2. On that user's profile, **Application Passwords** › add one named "n8n".
3. In n8n, use **Basic Auth** with that username and the application
   password (spaces included or removed, both work). Your site must use HTTPS.

### Publishing an article

1. **Upload the featured image:** see `n8n/1-upload-featured-image.md`.
   Keep the returned `id`.
2. **Create the post:** `POST https://YOUR-SITE/wp-json/wp/v2/posts` with a
   JSON body like `n8n/2-publish-article.json`.

| Field | What it does |
|---|---|
| `status` | Use `draft` while you review articles yourself (recommended for the first months), `publish` later. |
| `excerpt` | The standfirst under the headline and the search description. Write it; don't let WordPress cut one from the text. |
| `categories` | Category IDs. Look them up once: `GET /wp-json/wp/v2/categories?per_page=100`. Use the most specific one (for example Central banks, not Macro). |
| `meta.tv_takeaways` | 3-5 lines, one fact each. Shown at the top of the article and on the homepage lead. |
| `meta.tv_tickers` | One per line: `SYMBOL:up|down|flat:short reason`. Builds the Market impact box. |
| `meta.tv_sentiment` | `bullish`, `bearish`, `mixed` or `neutral`. |
| `meta.tv_horizon` | Free text, for example `This week`. |
| `meta.tv_sources` | One per line: `Title | https://link`. Primary sources first. **Don't publish without at least one.** |
| `meta.tv_reviewed_by` | The editor who checked it. |
| `meta.tv_ai_image` | `true` adds "AI-generated illustration" under the image. |
| `meta.tv_breaking` | `true` shows a Breaking label for 12 hours and makes it the homepage lead. |
| `meta.tv_offer` | Empty: offers by category. `none`: no offer. An offer slug: only that one. |

The **Daily Brief** category is special: its newest post is featured on the
homepage with its takeaways as a numbered list. Post your morning brief
there.

### Internal links

Before writing, look up related coverage to link to:
`GET /wp-json/wp/v2/posts?search=federal%20reserve&per_page=5&_fields=title,link,date`.
Link two or three of them naturally in the text, newest first.

### Market strip

Indices, stocks, FX and commodities:
`POST https://YOUR-SITE/wp-json/tv/v1/market` (same Basic Auth) with a body
like `n8n/3-market-snapshot.json`. Each push replaces the previous list.
Every 5-15 minutes during market hours is plenty. Crypto prices come from
CoinGecko automatically (list them in Customize › TickerVelocity › Market
strip).

### Newsletter signups

Customize › TickerVelocity › Newsletter › **Send new subscribers to**: the
URL of an n8n Webhook node. Every new signup is sent as
`n8n/4-newsletter-webhook-payload.json`. In n8n, add the email to your email
tool (Beehiiv, MailerLite, ConvertKit...) with double opt-in. Signups are
also kept under **Subscribers** in WordPress (with CSV export), so you never
lose your list.

## Partner offers

TickerVelocity › **Partner offers**: up to four offers with name, your
referral link, a one-line description, an optional new-user perk, button
text, the categories to show it in, and the countries never to show it to.

- Shown once, at the end of matching articles, labelled "Partner offer" and
  "Affiliate link" with the crypto risk warning, and links with
  `rel="sponsored"`.
- Each gets a link like `/go/mexc/` that counts clicks. Change the
  destination any time without editing articles.
- **Country blocking needs a country signal.** The simplest is Cloudflare's
  free plan with IP Geolocation on. Without one, offers stay hidden unless
  you choose otherwise. The check runs per visitor, so page caching doesn't
  break it.
- Read the compliance notes in BLUEPRINT.md first.

## Settings reference

| Where | What |
|---|---|
| Customize › TickerVelocity › Brand and contact | Tagline, editorial email (contact and corrections), X and LinkedIn |
| Customize › TickerVelocity › Homepage | Which section rows appear and in what order, the Daily Brief category, comments on/off |
| Customize › TickerVelocity › Newsletter | Name, pitch, header button text, webhook |
| Customize › TickerVelocity › Market strip | Crypto list (`BTC:bitcoin, ETH:ethereum`), optional CoinGecko demo key |
| Customize › TickerVelocity › Disclaimers | Article disclaimer, crypto risk warning, AI note |
| Customize › Site Identity | Logo and site icon (the icon is also used in Google results) |
| Each post › Article details | Takeaways, impact, sources, reviewer and the rest, for checking and fixing before publishing |

## Files

```
tickervelocity/
  BLUEPRINT.md            strategy and launch plan
  README.md               this file
  n8n/                    request examples for the workflow
  dist/                   the installable theme zip
  tickervelocity/         the theme
    front-page.php        homepage
    index.php             Latest, sections, tags, authors, search
    single.php            article
    template-parts/       cards, newsletter, offer, impact, market strip
    inc/
      meta.php            article fields for n8n and the editor
      market.php          market strip data and REST endpoint
      newsletter.php      signup endpoint, Subscribers, CSV export
      offers.php          partner offers, /go/ links, country checks
      seo.php             structured data, news sitemap, robots, authors
      setup.php           one-click setup, sample articles, page texts
```

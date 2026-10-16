# TickerVelocity

A fast, credible finance news site on WordPress. Your **Strategro SEO app**
writes and publishes the trending-news articles; this theme makes them look
like a serious markets publication, surrounded by **live price cards**.

No page builder, no n8n, no jQuery. Everything below works on its own once
the theme is installed.

- **Live prices everywhere:** a scrolling market strip, a "Markets now"
  board on the homepage (crypto, stocks, commodities tabs), a full
  [Live prices](#live-prices) page, and "In this story" cards on every
  article for the coins and stocks it mentions. Cards update every minute
  and flash green or red when a price moves. Crypto has 7-day sparklines.
- **Articles from any tool look finished:** a "Key takeaways" section in the
  text becomes a styled box at the top, reference links become a Sources
  list, and articles that arrive without a section are filed automatically
  (Fed story to Central banks, Nvidia earnings to Earnings, and so on).
- **Referral links handled safely:** links your app adds to MEXC, Binance and
  similar sign-up pages are labelled "Ad", marked `rel="sponsored"`, disclosed
  at the top of the article with a risk warning, and only clickable in
  countries you allow (by default not the UK or US).
- **Self-filling homepage:** lead story, top stories, Markets now, today's
  Morning Brief, a row per section, newsletter, latest news, trust strip.
- **Newsletter** capture straight into MailerLite or Beehiiv (or a webhook),
  with a copy kept in WordPress.
- **SEO:** news article structured data, Google News sitemap, Open Graph
  tags, thin pages kept out of Google, author profiles. Works alongside Rank
  Math or Yoast.
- **Fast:** fonts served from the site, about 12 KB of JavaScript.

The strategy (positioning, sections, monetisation, compliance, launch plan)
is in [BLUEPRINT.md](BLUEPRINT.md).

## Install

1. Appearance › Themes › Add New › Upload Theme ›
   `dist/tickervelocity-theme.zip` › Install › Activate.
2. **TickerVelocity › Setup › Set up TickerVelocity.** It creates:
   - sections: Markets, Crypto, Macro, Commodities, Biotech, Regulation,
     Layoffs, Daily Brief, each with sub-sections;
   - pages: Home, Latest, Live prices, About, Editorial standards and AI
     policy, Disclaimer, Affiliate disclosure, Privacy policy, Terms, Contact,
     Newsletter;
   - menus and the homepage settings.

   Nothing is deleted except WordPress's untouched "Hello world!" post and
   "Sample Page".
3. Optional: **Add sample articles** to preview every layout, and remove
   them from the same page before launch.
4. Appearance › Customize › **TickerVelocity** (details below).
5. Users › Profile: your real name, photo, bio, job title and LinkedIn.
6. Review the legal pages and replace everything in [brackets].

## Connect the Strategro SEO app

1. Users › Add New: a user for the app (for example "TickerVelocity
   Newsroom"). Role **Author** if you want to approve articles first
   (they arrive as drafts or pending), **Editor** to publish directly.
2. On that user's profile: **Application Passwords** › name it "Strategro
   SEO" › Add. Copy the password.
3. In the SEO app, add the WordPress site: site address, that username and
   the application password. The site must use HTTPS.

That's all the theme needs. The app publishes normal WordPress posts
(`POST /wp-json/wp/v2/posts`), and the theme takes care of the rest:

| The app sends | The theme does |
|---|---|
| Title, content, featured image | Article layout, cover art if there's no image |
| A heading "Key takeaways" (or TL;DR, Summary, Key points, At a glance) followed by a bullet list | Moves it into the takeaways box at the top |
| Links to its research sources | Lists them under "Sources" (unless the article already has a Sources or References section) |
| Mentions of Bitcoin, Nvidia, gold, oil... | Live "In this story" price cards for tracked assets |
| No category | Files it in the best-matching sub-section (Customize › Homepage to switch off) |
| A category | Keeps it |
| An excerpt | Standfirst under the headline and search description |
| Referral links | Labels, disclosure and country check (see below) |

Optional extras the app can send as post `meta` if you add them later:
`tv_takeaways` (lines), `tv_tickers` (`NVDA:up:reason` per line, builds a
Market impact box), `tv_sentiment`, `tv_horizon`, `tv_sources`
(`Title | URL` per line), `tv_reviewed_by`, `tv_ai_image` (true), and
`tv_breaking` (true). All of them can also be filled in by hand in the
post's **Article details** box.

## Live prices

Customize › TickerVelocity › **Live prices**:

- **Crypto** works straight away (CoinGecko, free). Format:
  `BTC:bitcoin:Bitcoin, ETH:ethereum:Ethereum`. The middle part is the coin's
  id from its coingecko.com address. An optional free CoinGecko demo key
  makes it more reliable.
- **Stocks and index funds**, and **commodities**, need a free
  [Finnhub](https://finnhub.io) key (sign up, copy it from the dashboard).
  Format: `NVDA:Nvidia, SPY:S&P 500 (SPY)`. The free plan covers US-listed
  stocks and funds, so indices and commodities are shown through the funds
  that track them (SPY for the S&P 500, GLD for gold, USO for oil). Price
  lines for these build up over the first half hour.
- Prices are fetched by your server at most every five minutes and shared
  by all visitors, so traffic never increases API use.

Live price cards inside articles or pages:

- `[tv_price symbol="BTC"]`: one card.
- `[tv_prices]`: the full board with tabs. Also available as
  `[tv_prices group="crypto"]`, `group="stocks"` or `group="commodities"`,
  and `[tv_prices limit="4" tabs="no"]`.

Your SEO app can put these shortcodes in an article's text.

Have real index levels from another source? Any tool can send them to
`POST /wp-json/tv/v1/market` (same login as above):
`{"items":[{"symbol":"FTSE 100","price":8204.3,"change":0.21}]}`.

## Referral links

Your SEO app adds referral links while it writes. The theme treats a link
as a referral link when:

- it is a sign-up, invite or join link on one of the listed platforms
  (Customize › TickerVelocity › Referral links: MEXC, Binance, Bybit, OKX,
  KuCoin, Bitget and others); or
- it carries a referral code (`?ref=`, `?inviteCode=`, `?aff=` and so on);
  or
- the app marked it `rel="sponsored"`.

Ordinary links to those sites, such as an exchange's announcement cited as
a source, stay ordinary links.

Referral links are labelled **Ad**, open with `rel="sponsored nofollow"`,
and the article gets a short disclosure and crypto risk warning at the top.
With **country checks** on (the default), they appear as plain text until
the visitor's browser confirms they're in an allowed country. By default
visitors in the UK, the US, or an unknown country see text only.
Country detection needs Cloudflare (free plan) in front of the site with IP
Geolocation switched on, or a host that provides the visitor's country.
Read the compliance notes in BLUEPRINT.md before you rely on referral
income.

## Newsletter

Customize › TickerVelocity › **Newsletter** › *Send new subscribers to*:

- **MailerLite:** paste an API key (Integrations › API) and optionally a
  group ID. Turn on double opt-in for API signups in MailerLite.
- **Beehiiv:** API key and publication ID (if your plan includes the API).
- **A webhook:** each signup is POSTed as JSON.
- **WordPress only:** signups are kept under **Subscribers** (CSV export).

Every signup is also kept under Subscribers whichever you choose.

## Settings reference

| Where | What |
|---|---|
| Customize › TickerVelocity › Brand and contact | Tagline, editorial email, X and LinkedIn |
| … › Homepage | Section rows and their order, Daily Brief category, automatic section sorting, comments |
| … › Newsletter | Name, pitch, button text, email tool |
| … › Live prices | Crypto, stocks and commodities lists; Finnhub and CoinGecko keys |
| … › Referral links | Platforms, blocked countries, unknown-country behaviour, country checks on/off |
| … › Disclaimers | Article disclaimer, crypto risk warning, AI note |
| Customize › Site Identity | Logo and site icon |
| Each post › Article details | Optional takeaways, market impact, sources, reviewer, breaking |

## Files

```
README.md               this file
BLUEPRINT.md            strategy and launch plan
dist/                   the installable theme zip
tickervelocity/         the theme
  front-page.php        homepage
  index.php             Latest, sections, tags, authors, search
  single.php            article
  template-parts/       cards, price cards and board, newsletter, takeaways
  inc/
    prices.php          live prices (CoinGecko, Finnhub), shortcodes, REST
    content.php         takeaways, sources, referral links, section sorting
    meta.php            optional article fields
    newsletter.php      signups, MailerLite/Beehiiv/webhook, Subscribers
    seo.php             structured data, news sitemap, robots, authors
    setup.php           one-click setup, sample articles, page texts
```

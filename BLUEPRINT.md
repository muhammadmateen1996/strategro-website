# TickerVelocity blueprint

A lean plan for a solo founder: what to launch, how it makes money without
losing trust, and the few decisions that matter most. The theme in this
folder already implements the site described here.

## The honest view first

The idea works if TickerVelocity is a **fast, sourced markets briefing**, not
a high-volume AI content site. Three things decide whether it succeeds:

1. **Google punishes scale for its own sake.** Since 2024, Google's spam
   policies target "scaled content abuse": many pages made mainly to rank,
   however they were produced. Finance is a "Your Money or Your Life" topic,
   where Google is strictest about expertise and trust. A few genuinely
   useful, sourced articles a day beat fifty rewrites of the same wire story.
2. **Numbers must come from data, not from the model.** One invented price
   or wrong percentage ends trust in a finance site. The SEO app should
   take figures from its sources and let the AI write around them, and you
   should check articles before they go live at first. Live prices on the
   site come from data feeds, never from the writing tool.
3. **Exchange referral links are regulated.** Promoting crypto exchanges
   such as MEXC or Binance is a financial promotion in the UK and is
   restricted in many other places (details under Monetisation). The theme
   labels referral links, discloses them and keeps them unclickable for UK
   and US visitors by default, but get this checked before you rely on them.

Get those right and the side project has a real shot: the design already
looks like a serious publication, the live prices make it a place people
come back to, and the SEO app gives you speed that small competitors can't
match.

---

## 1. Brand positioning

**Identity:** the markets briefing that is fast *and* sourced. For busy
retail investors, founders and finance-curious professionals who want to
know what moved markets and why, in five minutes, without hype.

**Tone of voice:** calm, precise, numbers-first. Plain English, short
sentences, no exclamation marks, no rocket emoji, no "to the moon", no
predictions dressed up as facts. Always say what happened, why it matters,
and what to watch next.

**Trust signals (all built in):**
- Primary sources listed under every article.
- Named author and "Reviewed by" editor on every article.
- Published and updated timestamps.
- Editorial standards and an honest AI policy, linked site-wide.
- Corrections policy, with a "Spotted an error? Tell us" link on every article.
- Partner links always labelled, with risk warnings.
- Market data marked as delayed, with the time of the last update.

**Visual direction:** a serious financial publication with a modern edge.
Dark ink header and market strip, clean white reading pages, a serif
headline face, monospaced prices, one electric violet accent, and green and
red reserved strictly for price moves.

**Taglines:**
1. *Markets, decoded. Fast.* (used by default)
2. *What moved markets, and why.*
3. *The signal in the market noise.*

## 2. Website structure

**At launch (created by Setup):**

| Page | Purpose |
|---|---|
| Home | Self-updating front page: lead, top stories, Daily Brief, section rows, newsletter, latest |
| Latest | Every article, newest first |
| 8 sections with sub-sections | Topic hubs that rank and that readers browse |
| Article pages | The product |
| Author pages | Credibility: bio, role, expertise, profiles, all their articles |
| Newsletter | The main conversion page; link it everywhere |
| About | Who is behind it and why it exists |
| Editorial standards and AI policy | How you source, check, use AI and correct; the core trust page |
| Disclaimer, Affiliate disclosure, Privacy policy, Terms | Legal basics |
| Contact | Tips and corrections |

**Later, once there is traffic:**
- **Topic hubs** for recurring themes (Fed decisions, Bitcoin ETFs, oil and
  OPEC): a page that summarises the latest and links every related story.
  These become your strongest search pages.
- **Earnings and economic calendar:** high-intent and very linkable.
- **Glossary:** short explainers ("What is a basis point?") to link from
  articles.
- **Premium analysis:** paid newsletter tier, once the free list is engaged.
- **Advertise with us:** when you have numbers worth selling.

## 3. Homepage blueprint (as built)

1. **Top bar:** date, live US market session (open, pre-market, after hours,
   closed), social links.
2. **Header:** logo, sections, search, Subscribe.
3. **Market strip:** scrolling live prices, timestamped.
4. **Lead and top stories:** the newest breaking story (or newest story)
   large, with its key takeaways; four more beside it.
5. **Markets now:** eight live price cards mixing crypto, stocks and
   commodities, with tabs, sparklines and a link to the full Live prices page.
6. **Today's Morning Brief:** the latest Daily Brief as a numbered list,
   with the signup form beside it.
7. **Section rows:** Markets, Crypto, Macro, Commodities, Biotech,
   Regulation, Layoffs (order set in the Customizer). No story appears twice
   on the page.
8. **Newsletter band.**
9. **Latest:** eight more stories, with a markets table alongside.
10. **How we report:** sourced, reviewed, independent, corrected in the open.
11. **Footer:** sections, company, legal, newsletter form, full disclaimer,
    data credits.

## 4. Article page blueprint (as built)

1. Breadcrumbs (Home / Section / Sub-section) and a Breaking label if set.
2. Headline (serif), then the standfirst (the excerpt).
3. Byline: author, reviewed by, published and updated times, reading time,
   share buttons (X, LinkedIn, Telegram, WhatsApp, copy link).
4. Featured image, with an "AI-generated illustration" credit when it is one.
5. **Key takeaways:** 3-5 one-line facts (from the app's "Key takeaways"
   section, moved into a styled box).
6. **In this story:** live price cards for the coins and stocks the article
   mentions.
7. **Market impact** (optional field): tickers with direction and reason,
   overall read and time frame, marked as an editorial read, not advice.
8. **Affiliate notice** with the crypto risk warning, only when the article
   has referral links and the visitor can see them.
9. Contents list (sidebar on desktop, fold-out on phones).
10. Body text, with charts or tables where they help; the newsletter box
    appears automatically about 40% of the way in. Referral links in the
    text are labelled "Ad".
11. **Sources**, from the article's reference links, numbered with the
    domain shown.
12. **Disclaimer and AI note**, plus "Spotted an error? Tell us".
13. Author box, then "More in {section}".

Article schema: one news hook, a clear headline that says what happened, the
first paragraph answering who, what, when, then why it matters and what to
watch. 500-900 words is right for most news; the brief can be shorter.

## 5. Sections

| Section | Sub-sections |
|---|---|
| Markets | Stocks, Earnings, IPOs |
| Crypto | Bitcoin, Ethereum, Altcoins |
| Macro | Central banks, Inflation, Jobs and growth |
| Commodities | Oil and energy, Gold and metals |
| Biotech | FDA decisions, Clinical trials |
| Regulation | SEC and US, UK and EU, Crypto rules |
| Layoffs | Tech, Finance |
| Daily Brief | (none) |

Rules: one category per article (the most specific one). Don't add a new
section until it would get at least a few articles a week. **Tags:** use them
sparingly, only for entities you'll cover repeatedly (companies, tokens,
people). The theme keeps tags with fewer than five articles out of Google
automatically.

## 6. Monetisation, in this order

1. **Newsletter first.** It's the asset you own. Beehiiv suits this well:
   it has a free tier, a referral programme, an ad network for newsletters
   and paid subscriptions when you're ready. Aim every page at the signup.
2. **Referral and affiliate links** (added by the SEO app), carefully:
   - **UK:** since October 2023, promoting cryptoassets to UK consumers is a
     regulated financial promotion. Unless an FCA-authorised firm approves it
     (or another exemption applies), communicating it can be a criminal
     offence, and affiliates are not exempt. Treat exchange referral links as
     off-limits for UK visitors unless the exchange can confirm its promotions
     are lawful there.
   - **US:** many offshore exchanges don't accept US customers at all, and
     affiliate links need clear disclosure (FTC).
   - **UAE:** Dubai's virtual asset regulator (VARA) has its own marketing
     rules. You're UAE-based too, so check these specifically.
   - **EU:** under MiCA, crypto service providers need authorisation. Be
     cautious promoting any that don't have it.
   - **Always:** read each programme's terms (they often ban certain
     countries and wording), label every link (the theme does this), and
     keep referral links out of news judgement: one natural mention where it
     genuinely helps the reader, never "sign up now" pressure in a news story.
     Broker, trading-tool and research-tool programmes are often simpler and
     safer than exchange referrals.

   This isn't legal advice; a short consultation with a fintech lawyer
   before relying on referral income is money well spent.
3. **Display ads later,** once traffic is steady and mostly from search.
   Networks such as Mediavine Journey, Raptive or Ezoic have traffic
   minimums (check current figures). Keep ads out of the headline area and
   out of the key takeaways.
4. **Premium analysis** (paid newsletter tier): deeper weekly pieces,
   watchlists and data, once free readers open your emails reliably.

## 7. WordPress stack

**Essential (about £10-20 a month in total):**

| Need | Choice |
|---|---|
| Hosting | A good managed host with LiteSpeed or built-in caching, UK or EU data centre |
| Theme | TickerVelocity (this folder). No page builder needed |
| SEO | Rank Math (free): titles, XML sitemaps, redirects, removing `/category/` from URLs. The theme hands schema and meta to it automatically |
| Caching | LiteSpeed Cache on LiteSpeed hosting; otherwise your host's cache or WP Super Cache |
| CDN and security edge | Cloudflare (free): speed, DDoS protection, and the visitor-country signal referral links need |
| Live prices | CoinGecko (free, crypto) and Finnhub (free key, US stocks and funds), built into the theme |
| Images | Have the SEO app upload WebP around 1600 px wide; otherwise Converter for Media |
| Security | Your host's protections plus two-factor login (Wordfence free or Solid Security) |
| Backups | UpdraftPlus (free) to cloud storage, daily |
| Newsletter | MailerLite or Beehiiv, connected directly by the theme (no plugin) |
| Affiliate links | Added by the SEO app; labelled and country-checked by the theme. No plugin needed |
| Analytics | Cloudflare Web Analytics (free, no cookies) or GA4 with a consent banner |

**Optional:** Search Console and Bing Webmaster Tools (free, set up on day
one really), Google Publisher Center, a consent manager if you add GA4 or
ads.

## 8. Publishing from the Strategro SEO app

The connection is a WordPress user with an Application Password (see
`README.md`). What the app should do, and what the theme already handles:

1. **Discover:** trending finance stories, as now.
2. **Research:** fetch the primary source and the figures; keep the links.
   Skip a story if there's no primary source.
3. **Write:** headline that says what happened, an excerpt, the body, and a
   **"Key takeaways" heading with 3-5 bullets** near the top (the theme
   turns it into the takeaways box). Link sources in the text (the theme
   lists them under Sources). Forbid any number not found in the research.
4. **Referral links:** at most one, where it genuinely helps; the theme
   labels, discloses and country-checks it.
5. **Image:** one clean illustration or chart as the featured image.
6. **Section:** send a category if the app knows the site's categories;
   otherwise leave it, and the theme files the article automatically.
7. **Internal links:** link two or three earlier TickerVelocity articles
   on the same topic (the app can search them through the WordPress REST
   API: `/wp-json/wp/v2/posts?search=...`).
8. **Developing stories:** update the existing article instead of
   publishing a near-duplicate; the theme shows "Updated" with the time.
9. **Publish as draft or pending at first** (Author role), and check each
   one; switch to direct publishing (Editor role) once you trust the output.
10. **Daily Brief:** one post a weekday in the Daily Brief category, five
    takeaways; the homepage features it and it doubles as the newsletter.

## 9. SEO system

- **URLs:** `/article-title/` (set by Setup). Short, permanent, and not
  broken by recategorising. Remove the `/category/` base with Rank Math.
- **Headlines:** say what happened, with the company or asset name early.
  No clickbait; it hurts Discover and trust.
- **Internal linking:** every article links to two or three related ones
  and to its section; topic hubs link to everything in their topic.
- **Tags:** few and meaningful (see Sections); thin tags are noindexed
  automatically.
- **Authors:** real people with photos, bios, expertise and LinkedIn. The
  theme adds them to the article's structured data.
- **E-E-A-T:** sources, reviewer, editorial standards, corrections, About
  page with a real person and company behind it.
- **Structured data:** NewsArticle, BreadcrumbList, NewsMediaOrganization
  with ethics and corrections policies. Built in, or Rank Math's if active.
- **Google News and Discover:** news sitemap at `/news-sitemap.xml` (last 48
  hours), large images allowed in previews, featured images at least
  1200 px wide.
- **Avoiding thin content:** publish when there is news, not to hit a
  number; merge follow-ups into the original story; no rewrites of the same
  wire story; every article adds context, numbers or "what to watch".

## 10. Design system (as built)

| | |
|---|---|
| Colours | Ink `#070a10` / `#0b111a` (header, strip, bands); white and `#f5f6f8` (reading); text `#0b1220` / `#3a4555`; accent violet `#6d5ef8` with a cyan `#22d3ee` gradient used sparingly; up `#0f9d6b`, down `#dc3a40`, only for prices |
| Section colours | Markets violet, Crypto amber, Macro blue, Commodities bronze, Biotech teal, Regulation cyan, Layoffs pink |
| Type | Newsreader (headlines and article text), Inter (interface), JetBrains Mono (prices and tickers); all served from the site |
| Buttons | 10 px radius, solid violet for actions, outlined for secondary |
| Cards | 16:9 images with 14 px radius, section label, time ago, serif headline; no shadows, thin rules between list items |
| Layout | 1240 px max width; 720 px reading column with a sticky sidebar; generous white space, dense information |
| Motion | Subtle: scrolling strip, live dots, fade-up on scroll; switched off for visitors who prefer reduced motion |

## 11. Launch checklist

**Week 1: foundations**
1. Domain, hosting with SSL, Cloudflare in front (IP Geolocation on).
2. Install WordPress, upload the theme, run Setup.
3. Rank Math, caching, backups, two-factor login.
4. Fill in the Customizer, your author profile and a site icon or logo.
5. Edit the legal pages; have the disclaimer and affiliate text checked.
6. MailerLite or Beehiiv account connected in the Customizer; test a signup.

**Week 2: the pipeline**
7. A user with an Application Password for the SEO app; publish a few test
   drafts and check takeaways, sources, price cards and the section chosen.
8. Free Finnhub key for stock prices; choose your crypto and stock lists.
9. Add fact and number checks to the app's prompts; review every draft.
10. Remove the sample articles.

**Week 3: soft launch**
11. Publish 3-6 strong articles a day plus the Daily Brief, for two weeks.
12. Submit both sitemaps in Search Console and Bing; set up Publisher Center.
13. Share each Daily Brief on X and LinkedIn; start the newsletter.

**After a month**
14. Look at what gets clicks and signups; build topic hubs for the winners.
15. Only then lean on referral links (after the compliance check) and,
    much later, ads.

---

## A) Best low-budget launch stack

TickerVelocity theme · managed LiteSpeed hosting · Cloudflare free · Rank
Math free · LiteSpeed Cache · UpdraftPlus · Wordfence free (2FA) · Beehiiv
or MailerLite free tier · CoinGecko and Finnhub free APIs · Cloudflare Web
Analytics · the Strategro SEO app. Roughly the cost of hosting plus your AI
usage.

## B) Best upgrade-later stack

Faster managed hosting (or a VPS with object caching) · Cloudflare Pro or
APO · Rank Math Pro (news SEO extras) · paid data feed for indices and
equities · Beehiiv paid plan (paid subscriptions, ad network) · a proper
consent manager once ads arrive · a second human editor for review.

## C) The five decisions to make first

1. **Quality bar over volume:** decide the maximum you'll publish per day
   and that nothing goes live without a primary source and a human check.
2. **Your public face:** who is the named editor (real name, photo,
   LinkedIn)? Anonymous finance sites struggle to earn trust and rankings.
3. **The newsletter is the business:** pick Beehiiv or MailerLite now and
   make the Morning Brief excellent.
4. **Where your readers are, and what you may promote there:** settle the
   referral-link question with legal advice before relying on it.
5. **The niche inside the niche:** be known for one thing first (for
   example "what moved markets before the open", or "crypto regulation
   explained") and expand once that works.

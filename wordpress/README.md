# Strategro on WordPress + Elementor

The Strategro site as a WordPress theme with a one-click page builder. Every page is
built from normal Elementor containers and free widgets, so you can edit any
text, image, button or section by dragging and dropping. The motion effects
live in the theme: switch them on for any element from the **Strategro
Motion** panel, or drag in one of the eight **Strategro Motion** widgets.

Built and tested against **Elementor 3.35.6** (works with or without
Elementor Pro) and WordPress 7.1.

## Install

Everything is in one file: **`dist/strategro-theme.zip`**. No Elementor
Pro, no kit import, no template import needed.

1. **Back up the site** (your host's backup tool, or a plugin like UpdraftPlus).
2. **Upload the theme:** Appearance › Themes › Add New › Upload Theme ›
   `strategro-theme.zip` › Install. If you installed an earlier version, choose
   **Replace active with uploaded**. Then **Activate**.
3. **Build the pages:** Appearance › **Strategro Setup** › **Build my Strategro
   site**. It lists exactly what it did when it finishes.

What the button does:
- Creates Home, Products, Custom Builds, About, Insights, Contact, Privacy
  Policy and Terms as normal Elementor pages, plus three starter blog posts.
- Creates header and footer menus named "Strategro …" (your existing menus
  are left alone).
- Sets Elementor's global colours and fonts to the Strategro brand, and
  switches on Flexbox Containers if they're off.
- Makes Home the homepage and Insights the blog page.
- **Deletes nothing.** If a page already uses one of those addresses (say
  `/about/`), it's kept as a draft called "Previous: …" at `/about-old/`.

Running it again rebuilds the Strategro pages to their original design, so
only do that before you start editing them.

## Still seeing an old design? Start with Strategro Site Check

Appearance › **Strategro Site Check** looks at your published site and tells
you, in plain words:

1. **Why old pages might still show:** the theme version that's active,
   whether a caching plugin needs clearing, old Theme Builder templates,
   old blog pages that are still published, and posts built with Elementor.
2. **Which plugins your published pages actually use:** Elementor Pro,
   Essential Addons and any other Elementor add-on, with the exact widgets
   and pages. A plugin nothing uses is marked "not used", so you can
   deactivate it.
3. **Speed:** what the theme already does, a one-click button for
   Elementor's speed settings, page caching, and images over 400 KB.
4. **Leftovers:** inactive plugins and themes, the installer's "Previous: …"
   drafts, unused saved templates, old revisions.

Nothing changes unless you press a button.

What the theme does on its own (1.4.0 and later):
- The Strategro header, footer, Insights list and articles always show, even
  if Elementor Pro Theme Builder templates (including ones set to "Elementor
  Full Width" or "Canvas") or Elementor's "Default Page Layout" would
  replace them. Pages keep any layout they chose themselves.
- Posts built with Elementor are shown in the article design, using the
  text, headings, images and links Elementor keeps for every post. Turn this
  off in Customize › Strategro Blog if you prefer their old layout.
- Old addresses `/blog/`, `/blogs/` and `/news/` go to Insights, once no
  published page uses them.

Want Elementor Pro Theme Builder templates instead of the Strategro ones?
Appearance › Customize › Strategro › tick **Use Elementor Pro Theme Builder
templates**.

After any theme update, **clear your cache** (caching plugin, host panel or
Cloudflare), or you may keep seeing old copies of pages.

## Speed

Built into the theme:
- Fraunces and Inter are served from your own site and the two main files
  are preloaded: no Google connection on each visit (faster, and better for
  privacy).
- Elementor's duplicate copies of the same fonts (every weight, from Google)
  are removed.
- WordPress's emoji script is removed.
- Block editor styles load only for the blocks a page uses, and not at all
  on pages built with Elementor.
- Article scripts load only on articles.

Worth doing on your site (Site Check tells you which apply):
- Remove plugins nothing uses. Essential Addons, for example, adds jQuery
  and its own files to every page even where none of its widgets appear.
- Switch on Elementor's speed settings (one button in Site Check).
- Add page caching (LiteSpeed Cache on LiteSpeed hosting, otherwise your
  host's recommendation or WP Super Cache).
- Shrink large images or serve WebP with an image optimiser.

## Day-to-day editing

| To change… | Go to |
|---|---|
| Any page's text, images, buttons, sections | Open the page › **Edit with Elementor** |
| Header / footer links | Appearance › Menus (`Header`, `Footer Products`, `Footer Company`, `Footer Legal`) |
| Header button, footer text, email, LinkedIn, Clara chat key | Appearance › Customize › **Strategro** |
| Logo | Appearance › Customize › Site Identity › Logo |
| Brand colours and fonts | Elementor › Site Settings › Global Colors / Global Fonts |
| Blog posts | Posts › Add New (they appear on Insights and the homepage automatically) |
| Page transitions + loader | Appearance › Customize › Strategro › *Animated page transitions* |
| Sticky header on/off | Appearance › Customize › Strategro › *Sticky header* |
| Use Theme Builder templates instead | Appearance › Customize › Strategro › *Use Elementor Pro Theme Builder templates* |
| Insights headline, intro, comments on/off, old Elementor posts in the article design | Appearance › Customize › **Strategro Blog** |
| See what's used, what's unused, speed tips | Appearance › **Strategro Site Check** |

## The blog (Insights)

The Insights list and every article are designed by the theme, so there is
nothing to build: write a post (Posts › Add New) and it appears in the
design automatically.

- **Insights page:** headline (put `*asterisks*` around words to make them
  shimmer gold) and intro are in Customize › Strategro Blog. Topic filters,
  search and article counts fill themselves in from your categories.
- **Each article:** title, category, author and reading time at the top;
  the featured image; a table of contents built from the post's headings
  (with "minutes left" while reading); share buttons; author box (from
  Users › Profile › Biographical Info); previous/next and related articles.
- **Excerpt:** if you fill in a post's Excerpt box, it shows under the title
  and on its card. Otherwise the card uses the first lines of the post.
- **No featured image?** The card gets generated cover art in the brand
  colours instead of an empty box.
- **Categories:** "Uncategorized" is never shown; give posts a real category.
- Old addresses `/blog/`, `/blogs/` and `/news/` redirect to Insights if no
  page uses them.
- The Insights page can't be edited with Elementor (WordPress fills it with
  posts), so the theme hides its "Edit with Elementor" button.
- Posts built with Elementor are shown in the article design too (their
  text, headings, images and links). Switch this off in Customize ›
  Strategro Blog to keep their own layout.

The homepage's "latest insights" block is the shortcode
`[strategro_latest_posts count="3"]`. Put it in any Shortcode widget to show
recent posts anywhere.

## Embedding a Clara form (or any HTML snippet)

Drag an **HTML** widget into any container and paste the Clara snippet.
Leads go straight to Clara as normal; the theme doesn't touch the form.

- For an `<iframe>` snippet, keep its `height` (e.g. `height="640"`) or the
  form gets cut off.
- Script-based snippets sometimes show blank inside the Elementor editor.
  That's normal; check the live page.
- Don't put `sg-tilt` or `sg-magnetic` on the form's container (it would
  move while people type). `sg-reveal` is fine.
- The Contact page already has the Clara booking calendar and form; edit
  those two HTML widgets to swap snippets.

## Strategro Motion panel

Select any widget or container in Elementor › **Advanced** › **✦ Strategro
Motion**. No class names to remember:

| Setting | Options |
|---|---|
| Entrance | Fade up from blur, Words slide up (headings) |
| Entrance delay | 0.1s to 0.6s, to play items in order |
| While scrolling | Parallax (slower / faster) |
| Cursor effect | 3D tilt with glare, Magnetic, Gold glow and lift |
| Surface | Frosted glass (dark / light), White card, Orbiting light border |
| Count up numbers | Numbers in the text count up from zero |
| Section skin *(containers)* | Dark, Light, Warm paper, Gold, with readable text colours |
| Children appear one by one *(containers)* | Staggered entrance |
| Animated background *(containers)* | Signal network, Aurora light, Blueprint grid, Cursor spotlight |
| Scroll children sideways forever *(containers)* | Turns the container into a ticker |

## Strategro Motion widgets

In the Elementor widget list, under **Strategro Motion**:

| Widget | What it does |
|---|---|
| Split Headline | Headline with shimmering gold words; words slide up one by one |
| Glow Button | Gold, outline or dark pill button with a light sweep, arrow and magnetic pull |
| Stat Counter | Big gold number that counts up, with a label |
| Motion Ticker | Endless sideways strip of words, pills or logos (speed, direction, pause on hover) |
| Live Activity Feed | The "system activity" panel; new rows slide in every few seconds |
| Motion Card | Product/feature card with icon, live badge, highlights, link, 3D tilt and glow |
| Sideways Scroll Showcase | Row of cards that pins and scrolls sideways on desktop, swipes on phones |
| Scroll Steps | Numbered steps that rise in turn as a gold line draws across |

Tip: put the Showcase in a full-width container and give the container the
Dark skin; the cards line up with the page edges automatically.

## Motion classes (advanced)

The panel and widgets use these CSS classes, which you can also type into
**Advanced › CSS Classes** yourself (separate with spaces). Effects never run inside the
Elementor editor, so everything stays visible while you edit. Visitors who
turn on "reduce motion" on their device get a still page.

### Scroll effects
| Class | Effect |
|---|---|
| `sg-reveal` | Fades up out of a blur when scrolled into view |
| `sg-split` | (Heading) words slide up one at a time |
| `sg-stagger` | (Container) its children appear one after another |
| `sg-steps` | (Container) scroll-scrubbed sequence: an `sg-line` draws across, then every `sg-step` inside rises in turn |
| `sg-line` | (Container) a gold hairline that draws itself as you scroll |
| `sg-counter` | (Heading) numbers count up from zero |
| `sg-parallax` / `sg-parallax-fast` | Drifts slower / faster than the page |
| `sg-hscroll` | (Section) pins and scrolls its `sg-hscroll-track` container sideways on desktop. The products showcase uses this |
| `sg-d1` … `sg-d6` | Delays a reveal by 0.1s steps |

### Cursor effects (mouse/trackpad only)
| Class | Effect |
|---|---|
| `sg-tilt` | 3D tilt with a light glare under the cursor |
| `sg-magnetic` | (Button) drifts toward the cursor |
| `sg-spot` | (Section) a soft light follows the cursor |

### Backgrounds and surfaces
| Class | Effect |
|---|---|
| `sg-dark` / `sg-light` / `sg-paper` / `sg-gold` | Section skins. Text inside picks readable colours automatically |
| `sg-network` | Animated signal network behind the section (reacts to the cursor) |
| `sg-aurora` | Slow drifting gold/teal light |
| `sg-grid-bg` | Faint blueprint grid |
| `sg-glass` / `sg-glass-light` | Frosted glass panel for dark / light backgrounds |
| `sg-card` | Solid white card |
| `sg-orbit` | A point of gold light circles the border |
| `sg-glow` | Gold glow and lift on hover |
| `sg-marquee` | (Container) its children scroll sideways forever |

### Type and buttons
| Class | Use on | Effect |
|---|---|---|
| `sg-display` | Heading | Hero-size headline |
| `sg-title` | Heading | Section-size headline |
| `sg-h3` | Heading | Card title |
| `sg-eyebrow` | Heading | Small gold uppercase label |
| `sg-chip` | Heading | Pill label with a pulsing dot |
| `sg-stat` | Heading | Big gold number |
| `sg-lead` | Text Editor | Larger intro paragraph |
| `sg-checks` | Text Editor | Bulleted list with gold ticks |
| `sg-btn` / `sg-btn-ghost` / `sg-btn-dark` | Button | Gold / outline / dark pill button with a light sweep on hover |
| `sg-arrow` | Button | Adds a ↗ arrow |

Gold shimmering words: in a Heading, wrap them as
`<span class="sg-gradient-text">your words</span>`.

## Developers

The page designs and installer live in `strategro/inc/installer.php`, with
content in `strategro/data/` (exported from the Next.js site). To run the
same installer from the command line:

```bash
wp eval-file wordpress/build/build-site.php --user=<admin>
```

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

## Seeing an old header or footer instead of the Strategro one?

Update to theme 1.2.0 or later. The Strategro header and footer now always
show, even if an Elementor Pro **Theme Builder** header from an earlier
design is still switched on.

To see what's left over, open Appearance › **Strategro Setup**. The
**Header and footer check** box lists every old header or footer template,
which tool made it, and an **Open** link. Delete any you don't need from
Templates › Saved Templates (hover › Trash).

Want an Elementor Pro Theme Builder header instead of the Strategro one?
Appearance › Customize › Strategro › tick **Use Elementor Pro Theme Builder
headers and footers**.

Templates made with the separate *Elementor Header & Footer Builder* plugin
can't be overridden by a theme; delete or deactivate them there.

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
| Use a Theme Builder header instead | Appearance › Customize › Strategro › *Use Elementor Pro Theme Builder headers and footers* |

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

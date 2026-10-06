# Strategro on WordPress + Elementor

The Strategro site as a WordPress theme with a one-click page builder. Every page is
built from normal Elementor containers and free widgets, so you can edit any
text, image, button or section by dragging and dropping. The motion effects
live in the theme and are switched on per element with a CSS class.

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

The homepage's "latest insights" block is the shortcode
`[strategro_latest_posts count="3"]`. Put it in any Shortcode widget to show
recent posts anywhere.

## Motion effects

Select any element in Elementor › **Advanced** › **CSS Classes** and add one
or more of these (separate with spaces). Effects never run inside the
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

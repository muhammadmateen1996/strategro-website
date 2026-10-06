# Strategro on WordPress + Elementor

The Strategro site as a WordPress theme plus an Elementor kit. Every page is
built from normal Elementor containers and free widgets, so you can edit any
text, image, button or section by dragging and dropping. The motion effects
live in the theme and are switched on per element with a CSS class.

Built and tested against **Elementor 3.35.6** (works with or without
Elementor Pro) and WordPress 7.1.

## What's in `dist/`

| File | What it is | Where it goes |
|---|---|---|
| `strategro-theme.zip` | Header, footer, brand styling, motion effects, blog layout | Appearance › Themes › Add New › Upload Theme |
| `strategro-elementor-kit.zip` | All pages, blog posts, menus, global colours and fonts | Elementor › Tools › Import / Export Kit › Import |

## Install

> Try it on a staging copy first. Importing the kit **replaces your Elementor
> global colours and fonts** and adds new pages. It does not delete your
> existing pages, but pages with the same address get a `-2` suffix.

1. **Back up the site** (your host's backup tool, or a plugin like UpdraftPlus).
2. **Check Flexbox Containers are on:** Elementor › Settings › Features ›
   *Flexbox Container* = Active (it's the default on 3.35).
3. **Install the theme:** Appearance › Themes › Add New › Upload Theme ›
   `strategro-theme.zip` › Install › **Activate**.
4. **Import the kit:** Elementor › Tools › Import / Export Kit › Import a Kit
   › `strategro-elementor-kit.zip`. Include everything when asked.
5. **Click "Finish setup"** in the blue notice at the top of the dashboard.
   It sets Insights as the blog page, connects the menus to the header and
   footer, and fixes the Privacy Policy address. It lists exactly what it
   changed.
6. **Settings › Permalinks** › choose *Post name* › Save (if it isn't already).

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

## Rebuilding the kit (developers)

`build/build-site.php` generates every page as Elementor data from
`build/content.json` (exported from the Next.js site's content) and
`build/legal.json`. With WordPress, Elementor 3.35.6 and the theme installed:

```bash
wp eval-file wordpress/build/build-site.php --user=admin
wp elementor kit export wordpress/dist/strategro-elementor-kit.zip
```

The theme folder must stay named `strategro`. The kit records that name and
switches the site to it on import.

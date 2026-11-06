# Design system

The committed design system for this theme and the source of truth for everything visual.
theme.json and the patterns must match this file. Design changes are made here first, then built.

Slugs in this file are the slugs used in theme.json and in block markup. Do not rename a slug
without recording it in `CHANGELOG.md` as a breaking change, as live content depends on it.

This file currently describes the **Mackman Blocks base theme**. For a client theme, replace the values
with the client's design system (from the studio's brief, kept unedited at `docs/brief/design-system.md`)
and keep the structure.

Last updated: 2026-11-05

## Brand summary

Base theme defaults: a generic, high-contrast neutral palette (near-black text, white/light-grey
backgrounds, a single blue accent) and the system font stack throughout. Deliberately free of any brand
identity — this is a template, not a client site, so it carries nothing beyond an accessible baseline.
A client build replaces every value below with that client's own design system.
<!-- Client theme: two or three lines on the look and feel for a designer or developer new to the client. -->

## Colour

| Name | Slug | Value | Use |
| --- | --- | --- | --- |
| Base | `base` | `#f2f2f2` | Light neutral background |
| Contrast | `contrast` | `#161616` | Body text, buttons, dark section background |
| Primary | `primary` | `#1d4ed8` | Accent: headings and links on light backgrounds (see below) |
| Secondary | `secondary` | `#ffffff` | Page background, text on dark sections |
| Tertiary | `tertiary` | `#e2e2e2` | Alternating section background |

Text and background pairs, WCAG 2.2 AA (4.5:1 normal text, 3:1 large text):

| Text | Background | Ratio | Normal text | Large text |
| --- | --- | --- | --- | --- |
| `contrast` | `secondary` | 18.10 | Pass | Pass |
| `contrast` | `base` | 16.16 | Pass | Pass |
| `contrast` | `tertiary` | 13.97 | Pass | Pass |
| `secondary` | `contrast` | 18.10 | Pass | Pass |
| `base` | `contrast` | 16.16 | Pass | Pass |
| `primary` | `secondary` | 6.70 | Pass | Pass |
| `primary` | `base` | 5.99 | Pass | Pass |
| `primary` | `tertiary` | 5.17 | Pass | Pass |
| `primary` | `contrast` | 2.70 | Fail | Fail |

Only the passing pairs may be used for text. `primary` is for text on light backgrounds (`base`, `secondary`,
`tertiary`) only, and for decorative use (borders, icons, rules) on `contrast`. A dark-section pattern must
set its own text colour explicitly (as a Group's `textColor`, for example) rather than relying on the
`heading`/`link` element defaults, which assume a light background.

## Typography

| Role | Family | Slug | Notes |
| --- | --- | --- | --- |
| Body | System font stack | `system-fonts` | Set as the default in theme.json |
| Headings | System font stack | `system-fonts` | Same as body; no separate heading typeface in the base theme |
| Available | Open Sans (300, 500, 700, normal and italic) | `open-sans` | Self-hosted, registered but not used. Open question: keep or remove — see MEMORY.md |
| Available | Geneva, Tahoma, Verdana | `geneva-verdana` | |
| Available | Cambria, Georgia | `cambria-georgia` | |

Base body size 1.15rem, line height 1.5. Fluid typography is on.

| Name | Slug | Size | Use |
| --- | --- | --- | --- |
| Hero | `hero` | 4.0689rem | Display headings |
| XX Large | `xx-large` | 3.2551rem | H1 |
| Extra Large | `extra-large` | 2.604rem | H2 |
| Large | `large` | 2.0836rem | H3 |
| Medium | `medium` | 1.6667rem | H4, lead text |
| Normal | `normal` | 1.3333rem | H5, H6 |
| Small | `small` | 1.0667rem | |
| Extra Small | `extra-small` | 0.8533rem | Captions, meta |
| XX Small | `xx-small` | 0.6827rem | |

## Spacing and layout

| Name | Slug | Size |
| --- | --- | --- |
| X Small | `x-small` | 20px |
| Small | `small` | clamp(30px, 4vw, 40px) |
| Medium | `medium` | clamp(40px, 6vw, 60px) |
| Large | `large` | clamp(50px, 8vw, 80px) |
| X Large | `x-large` | clamp(60px, 10vw, 100px) |

- Content width: 1140px
- Wide width: 1440px
- Root padding aware alignments: on
- Section padding: `medium` on all sides (default Group style)

## Components

- Buttons: square corners, weight 600, letter spacing 1px, `contrast` background. Outline variation with a 3px `contrast` border.

## Section library

Reset to empty on 2026-11-05 as part of taking the theme back to a bare-bones base (see `docs/MEMORY.md`),
then given two reference patterns on 2026-11-06 as the Phase 3 locking foundation:

| Section | Pattern slug | Category | Notes |
| --- | --- | --- | --- |
| Hero | `mackman/hero-standard-light` | hero | Page title, a subtitle bound to the `page_subtitle` post meta via Block Bindings, featured image. Content-only locked |
| Body text | `mackman/text-standard-light` | text | One heading and one paragraph. Content-only locked |

Both are used together as the `page` post type's default, locked block template (see Page types below). Add
further patterns here, under the slug convention and categories in `CLAUDE.md`
(`call-to-action`, `cards`, `hero`, `icons`, `testimonials`, `text`), as each section is needed for a client build.

## Page types

| Page type | Template | Structure | Editing |
| --- | --- | --- | --- |
| Page | `page` | Header, `post-content` (Hero + Body patterns), footer | Locked: every new page starts from the Hero and Body patterns; `template_lock: all` fixes the structure, `templateLock: contentOnly` on each pattern's Group allows editing their text and image only |
| Fallback | `index` | Header, post title, post content, footer | Open — used for every request without a dedicated template (post, archive, search, 404, and any future front page) |

`page.html` does not render its own title — the Hero pattern does, via `wp:post-title`, so it sits inside
the locked, styled Hero section rather than a plain template-level heading. This is why it differs from
`index.html`'s structure.

<!-- Client theme: list each page type, its sections in order, and whether it is locked. -->

## Editor restrictions

- Custom colours: off. Default palette, gradients and duotone: off.
- Custom font sizes: off. Default font sizes: off.
- Custom spacing sizes: off
- Default spacing sizes: off
- Core block patterns: removed
- Allowed blocks: restricted on `page` to a core content set (paragraph, heading, image, list, quote,
  separator, buttons, columns, group, post-title, post-featured-image, pattern) — see `functions.php`.
  Posts are unrestricted (full block editor, limited only by the settings above)

## Imagery and icons

Placeholder images: `/assets/images/holding.jpg`, `/assets/images/holding.svg`.
<!-- Client theme: photography style, aspect ratios, icon set and where it lives. -->

## Content rules

<!-- Client theme: tone, heading lengths, button wording, alt text expectations. -->

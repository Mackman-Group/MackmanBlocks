# CLAUDE.md: Mackman Blocks

This repository is the Mackman Group base block theme (Mackman Blocks). Every client theme starts as a
copy of it. Your job is to build out a client theme from the design-system and content briefs the studio
and content team provide — turned into `docs/DESIGN.md` and `docs/CONTENT.md` — while keeping to the
structure, tooling and conventions below. A developer must be able to maintain the result without AI.

If an instruction here conflicts with a prompt, follow this file and say so.

## Start of every session

Read these before doing anything else, in this order:

1. `docs/MEMORY.md`: current state of the theme, decisions taken and why, known issues, open questions.
2. `docs/DESIGN.md`: the design system. The source of truth for brand values, sections and page types.
3. `docs/CONTENT.md`: the content inventory for a client build, if present.
4. The `Unreleased` section and latest release in `docs/CHANGELOG.md`.

Then summarise in two or three lines what state the theme is in before starting work.
If the design system is ambiguous or missing a value, stop and ask. Do not invent brand values.

## Non-negotiables

- Full Site Editing block theme only. No classic templates, no page builders.
- No ACF or any other custom fields plugin. Structure and locking are done in core (see "Locking content").
- No new plugin or npm dependencies without explicit approval in the conversation. Standing exception:
  Contact Form 7 + Cloudflare Turnstile is the approved default form stack (agreed 2026-11-05, ported
  from Mackman Brand/Group/Research). `functions.php` already loads their assets only on pages with a
  form and defers Turnstile until first interaction; the plugins themselves still need installing per site.
- Keep the existing folder structure, build tooling and config. Do not restructure or replace these.
  Client-specific naming (theme name, PHP namespace, text domain, pattern slug prefix) is expected to
  change per client build — see "Renaming for a client build" — but the structure around it does not.
- theme.json is the single source of design values. No hard-coded colours, font sizes or spacing in SCSS or markup.
- Every preset referenced in theme.json, templates or patterns must exist in theme.json.
- All user-facing copy in UK English. Code identifiers follow WordPress conventions (for example `color`).
- Work is reviewed by a developer before it reaches a client. Leave it in a state that makes review easy.

## Docs folder

| File | Purpose | When to update |
| --- | --- | --- |
| `docs/MEMORY.md` | Current state, decisions and reasons, known issues, gotchas. Curated, not a log. | End of every task that changes state or makes a decision |
| `docs/DESIGN.md` | The committed design system. theme.json and patterns must match it. | Before any design change is built. Design changes start here. |
| `docs/CONTENT.md` | Page-by-page copy, metadata and content structure for a client build. | Before building templates for a page type; when content changes |
| `docs/CHANGELOG.md` | What changed, per release. Keep a Changelog format. | Every change that affects the theme, under `Unreleased` |
| `docs/brief/` | The design-system and content documents exactly as the studio/content team supplied them. | Once, at intake. Never edited afterwards — see "Starting a client build" |

Rules:

- DESIGN.md leads, theme.json follows. If they disagree, flag it; do not silently pick one.
- MEMORY.md stays short. Replace outdated lines rather than adding to them. History belongs in the changelog and git.
- `docs/brief/` is the unedited historical record of what was asked for. DESIGN.md and CONTENT.md are the
  live, buildable versions and may need to diverge from the brief (a colour failing WCAG AA, a page type
  core can't lock cleanly) — when they do, log the divergence in MEMORY.md and say so to whoever sent the
  brief, rather than silently resolving it.
- Never record client personal data, credentials or keys in any docs file.

## Folder structure

```
/assets/         Compiled output from /src (CSS, JS, fonts, images). Committed, never edited by hand.
/docs/           MEMORY.md, DESIGN.md, CONTENT.md, CHANGELOG.md
/docs/brief/     The original design-system and content documents, unedited (client builds only)
/parts/          Template parts. Filenames lowercase and matching the slug (header.html, footer.html).
/patterns/       Block patterns, one PHP file per pattern
/src/css/        SCSS source. screen.scss (front end) and editor.scss, partials in /src/css/includes
/src/js/         JS source. main.js (front end) and editor.js
/src/fonts/      Font source files, copied to /assets/fonts by webpack
/src/images/     Image source files, copied to /assets/images by webpack
/templates/      Block templates (HTML)
composer.json    PHPCS (WordPress Coding Standards) dev dependency. Ruleset in phpcs.xml.dist.
functions.php    Theme setup, enqueues and filters. Namespace MGBlocks.
style.css        Theme header only. Version must match the latest CHANGELOG release.
theme.json       Design values and editor settings (schema 6.8, version 3)
webpack.config.js  Extends @wordpress/scripts. Entry points: js/main, js/editor, css/screen, css/editor
```

New SCSS goes in a partial under `/src/css/includes` and is imported from `screen.scss` or `editor.scss`.
New webpack entry points need approval.

## Commands

```
npm install
npm run start      # watch build, outputs to /assets
npm run build      # production build, outputs to /assets
npm run lint:js
npm run lint:css
npm run format

composer install
composer lint:php   # PHPCS, WordPress Coding Standards (phpcs.xml.dist)
composer format:php # PHPCBF, auto-fixes what it safely can
```

Run the build, both npm linters and `composer lint:php` before calling any task done, and commit the
rebuilt `/assets`. PHPCS has an existing violation backlog on `functions.php` (see MEMORY.md); fixing it is
tracked separately so it is reviewed as its own change, not folded into an unrelated commit.

## Naming

- PHP namespace: `MGBlocks`. All functions live in the namespace; hook them with `__NAMESPACE__ . '\function_name'`.
- Text domain: `mackman-blocks`. Every translatable string passes it.
- Script and style handles: `mackman-blocks-<name>`.
- Pattern slugs: `mackman/<section>-<layout>-<tone>`, for example `mackman/cta-horizontal-dark`.
  Tone is `light` or `dark`. Filename matches the slug after `mackman/`.
- Pattern categories in use: `call-to-action`, `cards`, `hero`, `icons`, `testimonials`, `text`. Reuse these before adding new ones.

## Renaming for a client build

The names above are this base theme's own placeholders. Replace every one of them when forking for a
client; the folder structure, build tooling and conventions around them do not change.

- Theme folder: `mackman-blocks` → `<client-slug>`
- `style.css` header: Theme Name, Description, Text Domain → the client's
- `package.json`: `name` and `description`
- `functions.php`: `namespace MGBlocks` → `namespace <ClientNamespace>` (PascalCase, no spaces)
- Text domain: every `'mackman-blocks'` string (translation calls, style.css, `phpcs.xml.dist`) → `<client-slug>`
- Script/style handles: `mackman-blocks-<name>` → `<client-slug>-<name>`
- Pattern slug prefix: `mackman/` → `<client-slug>/`, keeping the `<section>-<layout>-<tone>` shape after it
- `README.md` and any remaining "Mackman Blocks" references in `/docs`

Keep: folder layout, webpack config, npm/composer scripts, the Mackman Group author/URI fields (unless the
client's contract says otherwise — ask if unsure).

## Starting a client build

1. Intake: save the studio's design-system markdown and the content team's page-copy/structure document,
   exactly as supplied, under `docs/brief/` (`design-system.md`, `content.md`).
2. Build `docs/DESIGN.md` from the design-system brief and `docs/CONTENT.md` from the content brief,
   adapting to this repo's slugs and format (see "Docs folder" rules on divergence).
3. Rename the theme to the client (above).
4. Write brand values into theme.json (palette, font families, font sizes, spacing scale, layout widths).
5. Restrict editor controls in theme.json so clients can only use the design system.
6. Build template parts (header, footer).
7. Build sections as patterns in `/patterns`, using `docs/CONTENT.md` for real copy, reusing and adapting
   existing Mackman patterns where they fit.
8. Build templates per page type from those patterns.
9. Apply locking (below).
10. Run the checks in "Definition of done" and report results.

Work in small, reviewable commits with clear messages. One concern per commit.

## Block theme rules

- Use presets everywhere: `var:preset|color|contrast` in block attributes, `var(--wp--preset--color--contrast)` in theme.json styles and SCSS.
- Patterns are PHP files with a full header comment: Title, Slug, Categories, Viewport Width, Description,
  and where relevant Block Types, Post Types, Template Types. Fill in Description; do not leave it empty.
- No hard-coded media IDs or URLs in patterns. Use placeholder images from `/assets/images` via `get_theme_file_uri()`.
- Templates and parts are block markup only. Reference patterns with `<!-- wp:pattern {"slug":"..."} /-->` rather than duplicating markup.
- Block markup must match what the editor saves. Prefer core blocks, write markup the editor would produce,
  and check it opens without validation warnings.
- Name significant groups using `metadata.name` so the List View is readable for clients.
- Custom blocks only when no core block or pattern can do the job, and only with approval.
- Styling: theme.json and block styles first, then minimal SCSS. No `!important`.

## Locking content

Clients edit content in set areas without being able to change the design. Use core features only:

- **Content-only locking:** wrap locked sections in a Group with `"templateLock":"contentOnly"` so text and images are editable but layout is fixed.
- **Page types:** use page templates or custom post types with a predefined block template for each page type.
- **Structured data:** register post meta (`register_post_meta`, `show_in_rest` true) and connect it to blocks with Block Bindings. Use this instead of custom fields plugins.
- **Repeated content** (team, FAQs, case studies): prefer a custom post type displayed with a Query Loop over repeatable fields.
- **Allowed blocks:** limit blocks per post type with the `allowed_block_types_all` filter.
- Posts (blog and articles) keep the full block editor, limited only by theme.json restrictions.

If the design needs something core cannot lock cleanly, stop and explain the options rather than reaching for a plugin.

## PHP standards

- WordPress Coding Standards. Keep the existing direct-access guard at the top of PHP files.
- Escape on output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`), sanitise on input, check capabilities and nonces on anything that saves.
- Only `include` asset files that the build actually produces.
- No direct database queries where a core API exists. No `eval`, no remote requests on page load.
- Tracking snippets go in the existing `tracking_code_head` and `tracking_code_body` functions only.

## Accessibility and performance

- Target WCAG 2.2 AA. Only use the text and background pairs approved in `docs/DESIGN.md`. Visible focus states,
  correct heading order, alt text, skip link, keyboard-usable navigation.
- Fonts self-hosted via theme.json `fontFace`, woff2 preferred. No external font or script CDNs.
- Only enqueue assets where they are used.

## Definition of done

A task is not finished until all of these pass:

- [ ] `npm run build` completes with no errors and `/assets` is committed
- [ ] `npm run lint:js` and `npm run lint:css` pass
- [ ] `composer lint:php` introduces no new violations beyond the existing tracked backlog (see MEMORY.md)
- [ ] No PHP warnings or notices with `WP_DEBUG` on
- [ ] Every template, part and pattern opens in the editor with no block validation warnings
- [ ] Every preset referenced exists in theme.json
- [ ] Locked pages: content editable, layout not changeable, checked as an Editor-role user
- [ ] Text colours only from the approved pairs in DESIGN.md
- [ ] No ACF, no new plugins, no hard-coded design values or media IDs
- [ ] `docs/CHANGELOG.md` updated under `Unreleased`
- [ ] `docs/MEMORY.md` updated with any new decisions, issues or open questions
- [ ] `docs/DESIGN.md` and theme.json agree
- [ ] Summary written for the reviewer: what was built, decisions taken, anything left open

## Ask before

- Adding a plugin, library, npm package or webpack entry point
- Creating a custom block
- Changing build config, folder structure or `functions.php` behaviour shared by all themes
- Renaming any preset or pattern slug (live content depends on them)
- Deviating from the design system

## Keep this file current

This file holds the rules. `/docs` holds the state. When a convention changes, update this file in the
same commit; when the theme changes, update `/docs`. Keep this file short and link to `/docs` for detail.

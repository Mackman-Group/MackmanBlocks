# Changelog

All notable changes to this theme are recorded here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and versions follow
[Semantic Versioning](https://semver.org/):

- **Major** (2.0.0): changes that break existing content or need work on live sites, such as renamed preset or pattern slugs or removed templates
- **Minor** (1.1.0): new patterns, templates, page types or features
- **Patch** (1.0.1): fixes and small adjustments

Add every change under `Unreleased` as you go. When a release is made, move those entries under a new
version heading with the date, and update the version in `style.css` and `package.json`.

Group entries under: Added, Changed, Deprecated, Removed, Fixed, Security.
Write each entry for a developer who was not there: what changed and where, in one line.

## [Unreleased]

## [1.0.5] - 2026-11-05

### Added

- `CLAUDE.md` with build rules and conventions for Claude Code
- `docs/MEMORY.md`, `docs/DESIGN.md` and `docs/CHANGELOG.md` as the theme's running resource library
- `functions.php`: jQuery Migrate removal, Dashicons removal for logged-out visitors, a theme.json-driven
  font preload, remote block pattern blocking, archive title prefix removal, a post type/slug body class,
  width/height backfill for local image blocks (CLS fix), an srcset width cap at 2560px, and a Contact
  Form 7 + Cloudflare Turnstile stack that only loads on pages with a form and defers Turnstile until
  first interaction. Ported and generalised from the Mackman Brand, Mackman Group and Mackman Research
  themes
- `screenshot.png`: generic placeholder wireframe (1200×900) built from the theme's own palette, replacing
  the inherited Frost theme screenshot
- `composer.json` and `phpcs.xml.dist`: PHPCS with WordPress Coding Standards, as `composer lint:php` /
  `composer format:php`, alongside the existing npm linters

### Changed

- Replaced the inherited Mackman brand colour palette with a generic, high-contrast neutral one: `base`
  `#f2f2f2`, `contrast` `#161616`, `primary` `#1d4ed8`, `secondary` `#ffffff`, `tertiary` `#e2e2e2`. Slugs
  are unchanged, only the values
- Headings now use the system font stack instead of the broken `noto` reference
- Minimum versions raised to WordPress 6.5+ / PHP 8.0+ in `style.css` and the README, covering Block
  Bindings, `WP_Font_Face_Resolver` and `WP_HTML_Tag_Processor`
- README no longer lists the Gutenberg plugin as a requirement, and now describes the `/docs` folder
- `SECURITY.md` replaced with a real reporting route (support@mackmangroup.co.uk), removing the GitHub
  placeholder text
- `customSpacingSize` turned off in theme.json, so clients can no longer enter spacing outside the scale

### Removed

- `src/audio/sample-audio-file.mp3`, an unused leftover from the Frost theme fork
- All 17 patterns from `/patterns`, and every template except `index.html` (removed `front-page`, `page`,
  `single`, `archive`, `search`, `404`), to reset the theme to a bare-bones base. `index.html` now serves
  as the universal fallback until templates and patterns are rebuilt per client.

### Fixed

- Renamed `parts/Header.html` and `parts/Footer.html` to lowercase `header.html`/`footer.html` so they match
  the `header`/`footer` slugs referenced in every template, and gave them minimal working content (site
  title and navigation in the header, site title in the footer). Previously these files were empty and
  incorrectly cased, so no header or footer rendered on any page.
- theme.json's page background now references `secondary` instead of the non-existent `white` preset
- theme.json's H2 font size now references `extra-large` instead of the non-existent `x-large`
- The new palette's `primary` passes WCAG AA as text on every light background (5.17–6.70:1), fixing the
  contrast failures the old gold accent had wherever it was used for headings or links
- Removed the `assets/js/block-variations.asset.php` include from `functions.php`, which the build never
  produced and raised a PHP warning on every editor load
- `filter_site_upload_size_limit()` no longer returns an undefined variable for a user without the `read`
  capability, and its comment now correctly says 2MB rather than 100KB
- Added the `mackman-blocks` text domain to the two `__()` calls in `functions.php` that were missing it
- `package.json`'s version aligned with `style.css`

## [1.0.4]

Baseline before the changelog was started. Release date and earlier history not recorded.
See `docs/MEMORY.md` for the state of the theme at this point.

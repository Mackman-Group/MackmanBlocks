# Theme memory

The working memory for this theme. Read at the start of every session; update at the end of any task
that changes state or makes a decision.

Keep this file short and current. Replace lines that are out of date rather than adding to them.
History lives in `CHANGELOG.md` and git. Never record client personal data, credentials or keys here.

Last reviewed: 2026-11-06

## Current state

- Theme: Mackman Blocks base theme (originally forked from WP Engine's Frost theme)
- Version: 1.0.5 in both `style.css` and `package.json`, released 2026-11-05 — Phase 2 of the clean-up
  brief is complete; see `CHANGELOG.md` for the full list
- Reset to a bare-bones base on 2026-11-05: only `templates/index.html` remains (the required fallback); all other templates and all 17 patterns were removed. Rebuild each as a client needs it.
- Built: theme.json, `index.html`, `parts/header.html` and `parts/footer.html` (minimal: site title, navigation), SCSS structure, webpack build to `/assets`
- `functions.php` carries a set of performance snippets ported from the Mackman Brand, Mackman Group and
  Mackman Research themes on 2026-11-05 (see decision below): jQuery Migrate and Dashicons removal, a
  theme.json-driven font preload, remote block pattern blocking, archive title prefix removal, a slug body
  class, image block width/height backfill, an srcset width cap, and a Contact Form 7 + Cloudflare
  Turnstile stack that loads only on pages with a form
- Phase 3 (locking foundation) built and verified live on 2026-11-06: `templates/page.html` plus two
  content-only locked patterns (`mackman/hero-standard-light`, `mackman/text-standard-light`, kept as
  reference patterns) inform the `page` post type's default template, inlined as nested block arrays in
  `functions.php` rather than referenced by slug (see gotcha below); a `page_subtitle` post meta field is
  bound to the Hero's subtitle via Block Bindings; `page` is restricted to a core content block set. Posts
  are untouched (full editor). Confirmed in the browser on `wordpress.test`: a new Page pre-populates with
  Hero + Body, each shows the simplified content-only toolbar with no move/remove, and the Body group shows
  a lock icon with no insert-after control
- Not built: any page-type templates beyond `index`/`page`, any further patterns, custom post types
- Next: the brief's last Phase 3 item — testing the editing experience with someone non-technical — still
  needs a human; I've only checked it myself as an admin. The `lint:css` and `composer lint:php` formatting
  backlogs (below) are tracked separately and not blocking

## Decisions

| Date | Decision | Reason |
| --- | --- | --- |
| 2026-11-05 | Content locking uses core features (content-only locking, block bindings, CPTs), not ACF | Keeps sites in core with a lean plugin list; maintainable without AI or plugins |
| 2026-11-05 | Pages are locked by template; posts keep the full block editor | Clients need flexibility for articles but not for core pages |
| 2026-11-05 | `docs/DESIGN.md` is the design system source of truth; theme.json follows it | Stops design knowledge being lost in prompts |
| 2026-11-05 | Removed all templates (except `index.html`) and all patterns, and fixed the header/footer parts | The template library had drifted from a clean baseline (broken part filenames, hardcoded media IDs pointing at a live external site, a pattern whose filename and slug were swapped for another pattern's). Resetting to bare bones avoids carrying that debt into the first client build |
| 2026-11-05 | Ported a set of performance snippets from Mackman Brand/Group/Research into `functions.php`, and approved Contact Form 7 + Cloudflare Turnstile as the default form stack | These had independently converged across three live sites (Group's own commit says it ported them from Brand and Research), so they're a de facto house standard worth having on day one rather than every client rediscovering them. The font preload was generalised to read theme.json rather than a hard-coded font name |
| 2026-11-05 | Replaced the inherited Mackman brand palette with a generic, high-contrast neutral palette (`base` #f2f2f2, `contrast` #161616, `primary` #1d4ed8, `secondary` #ffffff, `tertiary` #e2e2e2); headings now use the system font stack instead of the broken `noto` reference | The theme is a template, not a client site — it has no need for Mackman's own brand colours or typeface. The new palette also fixes the WCAG AA failures the old one had everywhere `primary` was used as text |
| 2026-11-05 | Minimum versions raised to WordPress 6.5+ / PHP 8.0+ in `style.css` and the README | Covers Block Bindings and `WP_Font_Face_Resolver` (both need 6.5) and `WP_HTML_Tag_Processor` (6.2); PHP 8.0 is in security support and standard on current hosting |
| 2026-11-05 | Added PHPCS with WordPress Coding Standards via composer (`composer.json`, `phpcs.xml.dist`) | PHP had no automated linting while JS and CSS already did; running it surfaced a 1,000+-violation backlog in `functions.php`, tracked as a known issue rather than fixed in the same change |
| 2026-11-05 | Closed out the rest of Phase 2's straightforward fixes: dropped the dead `block-variations.asset.php` include, fixed `filter_site_upload_size_limit()`'s undefined-variable bug, added the `mackman-blocks` text domain to its two `__()` calls, turned `customSpacingSize` off, and aligned `package.json`'s version with `style.css` | These were mechanical, no judgement calls needed |
| 2026-11-05 | Cut `1.0.5`: moved `Unreleased` to a dated release in `CHANGELOG.md`, bumped `style.css` and `package.json` together, confirmed `DESIGN.md` agrees with the final theme.json | Closes Phase 2 of the clean-up brief |
| 2026-11-05 | Documented the pipeline for a client build in `CLAUDE.md`: a design-system brief (Dylan/studio) and a content brief (Ben/content writers) land unedited in `docs/brief/`, get built into `docs/DESIGN.md` and the new `docs/CONTENT.md`, and the theme's naming (folder, namespace, text domain, pattern prefix) is renamed to the client — see CLAUDE.md's "Starting a client build" and "Renaming for a client build" | Settles how Claude Code should take the studio's and content team's own documents and this theme together into a client build, keeping the knowledge in the repo rather than a prompt |
| 2026-11-06 | Updated npm deps within their declared ranges (`@wordpress/scripts` 30.19.0→30.27.0, `webpack-remove-empty-scripts` 1.0.4→1.1.1) and rebuilt `/assets`; added a `typescript` override pinned to `5.4.5`. Left `@wordpress/scripts` 37.1.0 and `copy-webpack-plugin` 14.0.0 (both major bumps outside the declared ranges) untouched | A plain `npm update` let npm resolve `typescript@7.0.2` transitively, which crashes `lint:js` outright (`ts-api-utils`/`@typescript-eslint` 6.x predate TS7). The override keeps updates safe without pinning the whole tree. The two major bumps need their own deliberate upgrade and testing pass, not a blanket "update everything" |
| 2026-11-06 | Built Phase 3's locking foundation: a locked `page` block template (`register_post_type_args` + `template_lock: all`) built from two new content-only locked patterns, a `page_subtitle` post meta field bound via Block Bindings, and an `allowed_block_types_all` restriction on `page`. Added a `hero` pattern category | Demonstrates every technique `CLAUDE.md`'s "Locking content" section promises, using only core WordPress — the whole point of this exercise versus the ACF concept in [bruceburgoyne/wordpress-test](https://github.com/bruceburgoyne/wordpress-test), which this was checked against for reference |
| 2026-11-06 | The `page` post type's default `template` inlines the Hero/Body block trees directly, rather than referencing the two patterns by slug (`array('core/pattern', array('slug' => '...'))`) | Live testing on `wordpress.test` showed the pattern-reference form doesn't reliably resolve into visible content for a brand-new post — the placeholder block lands in `post_content` but doesn't expand client-side. The patterns stay in `/patterns` as documented, reusable reference sections; keep the two in sync by hand if either changes |

## Known issues

Found in a review of the repo on 2026-11-05; updated after the 2026-11-05 bare-bones reset and Phase 2 clean-up. Remove each line when fixed and log the fix in the changelog.

- Contact Form 7 and Cloudflare Turnstile are not installed on any site yet; the `functions.php` code that targets them is inert until they are (see `CLAUDE.md`).
- Open Sans is self-hosted as .ttf and registered but not used.
- `src/js/main.js` and `src/js/editor.js` are empty but still built and enqueued.
- `src/css/includes/blocks.scss` hard-codes a colour (`#eee`), several px paddings outside the spacing scale, and six `!important` rules.
- `npm run lint:css` fails with roughly 1,389 problems, almost all indentation (the SCSS is space-indented; stylelint expects tabs), plus some real rule violations (selector specificity order, `:before`/`:after` colon notation, `currentColor` casing, two empty partials: `header.scss` and `layout.scss`). `npm run lint:js` fails with 19 Prettier errors, all in `webpack.config.js`.
- `@wordpress/scripts` is pinned to `^30.10.0` (37.1.0 is out) and `copy-webpack-plugin` to `^12.0.2` (14.0.0
  is out). Both are major jumps that need a deliberate upgrade pass (new webpack/wp-scripts config APIs are
  likely) rather than a routine update — see the decision above.
- `composer lint:php` fails with 947 errors and 194 warnings across `functions.php` and the two new pattern
  files — same root cause as `lint:css`: space indentation where WordPress Coding Standards wants tabs,
  plus the two patterns missing a `@package` tag and blank line after their file comment (same as every
  other pattern that existed before the bare-bones reset). 1,098 of the 1,141 violations are auto-fixable
  with `composer format:php`, but that is a large, separate formatting change — do it as its own reviewable
  commit, not mixed with a functional change.

## Gotchas

- Build output goes to `/assets` (not `/build`) and is committed. Always rebuild and commit `/assets` with source changes.
- Template part filenames must match the slug exactly, lowercase.
- Don't run a plain `npm update` without checking `npm ls typescript` afterwards — npm can silently resolve
  an incompatible TypeScript major into the eslint toolchain. `package.json`'s `overrides.typescript` pins
  this; keep it until `@wordpress/eslint-plugin`'s own deps support newer TypeScript.
- A `register_post_type_args` `template` referencing a pattern by slug (`array('core/pattern', array('slug'
  => '...'))`) doesn't reliably expand for a brand-new post — inline the actual block tree instead (see the
  `page` post type's template in `functions.php`).
- Clicking "Add New" reuses an existing `auto-draft` for that post type/author rather than creating a fresh
  one, so testing a post type's default template against a stale auto-draft can look like the template isn't
  applying at all. Give the stale one a title (converts it to a real draft) or delete it, then try again.

## Open questions

- Whether to keep Open Sans self-hosted (still unused — headings now use the system font stack, same as body).
- Whether `functions.php` logic should move into `/inc` files as the theme grows. Checked Mackman Research's
  `/inc` for precedent (2026-11-05): it's a single flat file for one bespoke integration, not a real pattern.
  [bruceburgoyne/wordpress-test](https://github.com/bruceburgoyne/wordpress-test) (checked 2026-11-06) is a
  better model if we do this: `inc/editor.php`, `inc/acf.php`-equivalent, `inc/helpers.php`, `inc/blog.php`
  — split by concern, not by post type or page.
- When to run `composer format:php` to clear the PHPCS formatting backlog — needs its own commit, not mixed
  with a functional change.
- Git history has a single commit ("committing lost changes"); is there earlier history worth recovering?
- Now it's confirmed working live, whether the Hero/Body locked `page` template is the right shape to
  standardise on, or whether a client build should get to choose from a small set of locked page layouts
  (see the Services idea in [bruceburgoyne/wordpress-test](https://github.com/bruceburgoyne/wordpress-test)'s
  README) rather than one fixed template.

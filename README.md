# MackmanBlocks

An experimental block theme for designers, developers, and creators.

## About

mackman is a Full Site Editing theme for WordPress that extends the incredible power of the editor, enables builders to create amazing sites with little effort, and elevates the potential for freelancers and entrepreneurs.
The site was originally forked from WP Engines Frost theme. See the Frost theme in action at [frostwp.com](https://frostwp.com/).

## Requirements

- WordPress 6.5+
- PHP 8.0+
- Contact Form 7 and Cloudflare Turnstile, for the form-loading code in `functions.php` to do anything
  (see `CLAUDE.md`); harmless if not installed

## Getting Started

1. Set up a local WordPress development environment, we recommend using [Local](https://localwp.com/).
2. Ensure you are using WordPress 6.5+.
3. Clone / download this repository into the `/wp-content/themes/` directory of your new WordPress instance.
4. In the WordPress admin, use the Appearance > Themes screen to activate Mackman Blocks.

## Documentation

`CLAUDE.md` holds the build rules and conventions for working on this theme. `/docs` holds its running
state:

- `docs/MEMORY.md` — current state of the theme, decisions taken and why, known issues, open questions
- `docs/DESIGN.md` — the design system: the source of truth for brand values, sections and page types
- `docs/CHANGELOG.md` — what changed, per release

Read all three before starting any work on this theme.


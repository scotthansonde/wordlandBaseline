# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

WordLand Baseline is a classic (non-block/non-FSE) WordPress theme, purpose-built for blogs authored with [WordLand](https://wordland.social/). Styling is derived from Dave Winer's `baselinePlayground` CSS and inspired by Automattic's *Retrospect* theme.

The theme is deliberately narrow in scope (per README.md):
- Only the home page (all posts, reverse-chronological) and single posts are supported. Archives/category/tag/custom pages beyond `category-linkblog.php` are not a design target.
- Comments, Jetpack sharing/likes/related-posts are hidden by default, even if enabled elsewhere in WordPress/Jetpack.
- Custom menus are not supported — there is no `register_nav_menu()`/`wp_nav_menu()` anywhere, by design, not oversight.
- There is no widget/sidebar system either. The one "sidebar" (`.divSidebar` in `index.php`) is just a conditional block that runs `[feedland-blogroll]` if that specific plugin's shortcode exists — not a real `register_sidebar()` area.

There is no build step: no `package.json`, no bundler, no JS toolchain. It's plain PHP templates plus `style.css` as the real, actively-edited stylesheet (not just a theme-header stub).

## Commands

There's no test suite or linter configured. The only verification tooling available:

- **PHP syntax check a file:** `php -l path/to/file.php`
- **Local WordPress environment (requires Docker running):**
  - `wp-env start` — boots WordPress at `http://localhost:8888` (admin: `admin` / `password`), with this theme mounted per `.wp-env.json` (`"themes": ["."]`)
  - `wp-env stop` — stops it (state persists in the Docker volume between starts)
  - `wp-env run cli wp <command>` — run any wp-cli command inside the environment, e.g. `wp-env run cli wp post list`, `wp-env run cli wp theme list`
- No automated way to visually verify changes — use `wp-env` plus a browser (or chrome-devtools-mcp) to check rendering after edits, especially for CSS/template changes.

## Architecture

**`functions.php`** is the bootstrap: it `require_once`s everything in `inc/`, registers theme supports (`title-tag`, `automatic-feed-links`, `post-thumbnails`), enqueues jQuery + `css/fonts.css` (self-hosted fonts) + `style.css`, and hooks the home-page query customization and post-sorting logic described below.

**`inc/` — cross-cutting concerns, wired together via WordPress hooks:**
- `customizer.php` registers theme mod settings (Customizer checkboxes: show comments, disable pagination, disable Jetpack sharing/likes/related, show date headlines) plus a "Home Page Template" radio choice (`default` vs `linkblog`) that swaps in `category-linkblog.php` as the home template via the `home_template` filter.
- `jetpack-control.php` reads those same theme mods and applies the corresponding Jetpack filters (`sharing_show`, `jetpack_likes_enabled`, `jetpack_relatedposts_enabled`, etc.) on `init`.
- `content-links.php` implements the WordLand link-blog content pattern (see below) via `the_content` filters, applied per-post through `wordland_append_external_link()`.
- `social-icons.php` is a static lookup table of social-service SVGs (adapted from the Gutenberg core social-link block) — currently only used for the RSS icon in the footer.

**WordLand-specific data model** — this is the part that isn't obvious from any single file:
- A post is a "link-blog entry" if it has post meta `wordland_linksTo` (or the older `external_url`) — `wordland_append_external_link()` in `inc/content-links.php` appends that URL's domain as a link at the end of the post content, and the RSS feed (`the_permalink_rss` filter in `functions.php`) uses that URL as the feed item's `<link>` instead of the permalink.
- A post is "singular" if `post_title` is empty — this is a first-class concept, not an edge case. Singular posts are sorted to appear *first* within each calendar day (`baseline_sort_posts_singular_first` in `functions.php`), and instead of a title link they get a `#` anchor (`.spPermaLink`) pointing at `/{year}/{month}/{day}/#a{post_ID}` — the anchor itself is injected by `wordland_prepend_post_anchor()` prepending `<a id="a{ID}"></a>` to `the_content`.
- Templates group posts by day: `index.php`, `category-linkblog.php` (and the day-archive case in `archive.php`) all loop posts and open/close a `divDayGroup`/`divLinkblogDay` wrapper whenever `get_the_date()` changes.

**Home page query behavior** (`functions.php`, `pre_get_posts` + `the_posts` filters): when pagination is disabled (the default), the home query is rewritten to pull all posts from the current and previous calendar month instead of paginating, falling back to the 5 most recent posts if that window is too sparse.

**Template selection quirk:** `category-linkblog.php` is a `category-{slug}.php` template (WordPress's built-in template hierarchy — no extra registration needed) that also does double duty as an optional home-page template via the customizer's `home_template` filter. It runs its own `WP_Query` rather than touching the global `$wp_query`, so it's safe to use in either context.

**Naming convention:** hook callbacks and helpers are prefixed `baseline_` (theme-specific) or `wordland_` (link-blog/content-pattern specific). Follow this when adding new global functions — PHP has no namespacing here, so unprefixed generic names risk colliding with plugins.

**Text domain:** always `wordland-baseline`, matching the `Text Domain` header in `style.css`. Keep it consistent across every `__()`/`_e()` call.

**Fonts:** self-hosted in `fonts/` (Ubuntu 400/700, Rancho 400 — only the weights the theme's own CSS uses, latin + latin-ext subsets), declared via `@font-face` in `css/fonts.css` and enqueued from `functions.php`. Don't reintroduce a `fonts.googleapis.com` request — if a new weight/family is needed, download it and add it the same way.

# Blitz Dock Architecture

This document provides an overview of the core architectural decisions that keep the Blitz Dock plugin sustainable.

## Folder layout

- `assets/` — Source CSS and JavaScript for the public and admin experiences. Minified artifacts are generated during the build step.
- `includes/` — Namespaced PHP organised by feature (Core, Admin, Frontend, Channels).
- `templates/` — PHP templates that output HTML fragments for the admin and public UI.
- `languages/` — Translation files loaded on `plugins_loaded`.
- `docs/` — Project documentation.

## Naming conventions

- **PHP namespaces** use the `BlitzDock\` prefix. Functions and classes follow WordPress coding standards.
- **PHP identifiers** should be prefixed with `bd_` when they need to exist in the global scope (e.g. filters or helper functions).
- **CSS classes** follow [BEM](http://getbem.com/) naming within the `.blitz-dock` root. No global selectors or tag selectors are allowed.
- **CSS custom properties** start with `--bd-` and are defined on the `.blitz-dock` root to encourage reuse and theming.
- **JavaScript data attributes** use the `data-bd-*` namespace so the script can query elements without leaking globals.

## Asset loading flow

Asset loading is centralised in `BlitzDock\Core\Assets`.

1. The `Assets::get_asset_meta()` helper selects the minified or non-minified variant based on `SCRIPT_DEBUG` and versions the asset with the `filemtime()` of the chosen file.
2. Admin hooks register base styles, and conditionally load the Channels assets only on the Blitz Dock admin screen and tab.
3. The public frontend defers registration until the page is confirmed to render Blitz Dock. Assets are registered via `wp_register_*` and enqueued once per page.
4. Script translations for the public script are registered at load time to ensure localisation works across locales.

## JavaScript structure

- Public scripts live in `assets/js/public.js` and are wrapped in an IIFE to avoid globals.
- Event delegation is performed on the `.blitz-dock` root using `data-bd-*` selectors.
- The script reads layout information from CSS custom properties when sizing calculations are required, avoiding magic numbers.
- Keyboard interaction stays consistent: `Escape` closes the panel, focus is trapped while open, and focus returns to the launcher bubble on close.

## CSS guidelines

- All rules are scoped beneath `.blitz-dock` using low-specificity selectors.
- Component dimensions, spacing, and transitions reference shared `--bd-*` tokens defined on the root container.
- Isolation is maintained with `box-sizing` rules scoped to `.blitz-dock`.
- Focus states use the shared `--bd-focus-ring` token to ensure WCAG-compliant outlines without relying on background fills.

## Build and tooling

- Composer scripts (`composer run lint:php` / `composer run fix:php`) execute WordPress Coding Standards with PHPCompatibilityWP.
- Node build scripts (`npm run build`) create minified CSS and JS using `csso` and `esbuild`.
- `.editorconfig` enforces consistent whitespace across file types.
- `CONTRIBUTING.md` documents the workflow so future contributions remain consistent.

Keeping these foundations aligned ensures future changes remain accessible, maintainable, and predictable.
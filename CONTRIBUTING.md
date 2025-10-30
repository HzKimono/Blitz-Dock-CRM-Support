# Contributing to Blitz Dock

Thanks for your interest in improving Blitz Dock! This project ships with a minimal toolchain to keep the codebase maintainable. Please follow the steps below before opening a pull request.

## Development requirements

- PHP 7.4 or newer
- Composer
- Node.js 18+

Install PHP dependencies:

```bash
composer install
```

Install Node dependencies:

```bash
npm install
```

## Linting

Run the WordPress Coding Standards suite with PHPCompatibilityWP via Composer:

```bash
composer run lint:php
```

To automatically fix fixable coding standard issues:

```bash
composer run fix:php
```

## Asset builds

Source files live in `assets/css` and `assets/js`. Minified artifacts are generated via the Node build scripts.

```bash
npm run build:js
npm run build:css
# or run both
npm run build
```

Always commit the regenerated `.min` files together with their source changes.

## Pull request checklist

- [ ] All PHP linting commands pass
- [ ] Assets are rebuilt with `npm run build`
- [ ] Changes include relevant documentation updates
- [ ] No new warnings appear in the browser console

Thanks for keeping Blitz Dock sustainable!
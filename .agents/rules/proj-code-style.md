# Code Style Rules

## PHP / Laravel Code Quality (Pint configuration)

- Strictly follow standard PSR-12 / Laravel styling guidelines.
- Clean up unused imports, dead references, and verify correct return typehints.

## Typehints and Type Safety

- **Mandatory** on every public function signature and controller action.
- Specify exact types for array structures (e.g. `/** @param float[] $closes */`).

## Standalone Vue 3 Frontend Naming & Architecture

- Standalone folder-structure isolated in `/frontend`.
- CSS structures using custom premium Outfits & HSL styles in `/frontend/src/style.css`.
- Feature-based structure inside `/frontend/src/components/` and standalone controllers in `/frontend/src/App.vue`.

## Naming

- Backend files: standard Laravel PascalCase (`CrossoverCalculator.php`).
- Domain Ports: PascalCasePort (`BinancePort.php`).
- Frontend components: PascalCase.vue (`App.vue`).

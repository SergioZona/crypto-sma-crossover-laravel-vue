# Testing Rules

## Test Structure

| Tier | Folder | Scope / Rule |
|---|---|---|
| Unit | `backend/tests/Unit/` | Pure domain math logic. Zero database/network I/O. Fast. |
| Integration | `backend/tests/Feature/` | Integration endpoints simulation, database saves validation. |

## PHPUnit / Pest Setup

1. Run unit tests using local or docker CLI PHPUnit binaries:
   ```bash
   ./vendor/bin/phpunit
   ```
2. Verify domain logic is decoupled from Laravel database structures directly.
3. Test mocks for Binance ports inside application boundary testing.

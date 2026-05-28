# ANTIGRAVITY — Project Context

## Project

**crypto-sma-crossover-laravel-vue** — Clean Hexagonal Architecture crossover calculations SPA engine.

- **Backend**: Laravel 11 (PHP 8.4)
- **Frontend**: Standalone Vue 3 + Vite SPA (TypeScript)
- **DB**: PostgreSQL
- **Cache**: Redis
- **Response format**: JSend

## Architecture

```
domain → application → infrastructure
```

- `backend/app/Domain/` — Entities (`Candle`, `Crossover`), calculation logic.
- `backend/app/Application/Ports/` — Inbound + Outbound interfaces.
- `backend/app/Application/UseCases/` — Use case orchestrators.
- `backend/app/Infrastructure/Adapters/Inbound/` — Controller handlers.
- `backend/app/Infrastructure/Adapters/Outbound/` — Database repository and cached API clients.
- `frontend/` — Standalone modern Vue 3 interface.

## Key Rules (from CONSTITUTION.md)

1. Domain never imports from application or infrastructure.
2. Application never imports from infrastructure.
3. Secrets are NEVER saved in `.env` files — injected at container level.
4. Use standard JSend format for all response payloads.

## Common Commands

### Backend commands via Docker
```bash
# Start Docker compose (App, DB, Cache)
docker compose up -d

# Execute PHPUnit suite
docker compose exec backend ./vendor/bin/phpunit
```

### Standalone Frontend commands
```bash
cd frontend
npm install
npm run dev
```

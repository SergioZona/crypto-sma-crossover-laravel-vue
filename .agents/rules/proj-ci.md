# CI Pre-Push Rules

## MANDATORY: Run local CI before every push

Before pushing any commit to the remote, the AI agent MUST run all CI checks locally inside the Docker container environment and fix any issues.

```bash
# 1. Run backend PHPUnit/Pest suite via Docker to ensure domain logic is intact
docker compose exec backend ./vendor/bin/phpunit

# 2. Format PHP backend styling
docker compose exec backend ./vendor/bin/pint

# 3. Compile standalone Vue 3 SPA frontend assets
cd frontend && npm run build
```

## Rules

- Fix ALL errors before pushing. Never bypass them.
- If a test fails, stop, fix the issue at the root cause, then restart the sequence.
- Do NOT push if any step exits with a non-zero return code.

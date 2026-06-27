# PulseDesk

> Multi-tenant support-desk SaaS — built for **Forge 2 · Edition 1** (Hermes × OpenClaw).
> **Builder:** Aman Kumar · GitHub [@Aman-Kumar342](https://github.com/Aman-Kumar342)

PulseDesk lets multiple organizations run their own helpdesk: customers raise
tickets, agents triage and reply, admins oversee. Every record is isolated per
organization — a user in Org A can never see Org B's data.

This repo is also an **agent-built project**: the work was planned by **Hermes**
(orchestrator/PO) and implemented by **OpenClaw** (coder), coordinating over
**Slack**, with a human merging every PR to `main`. See [`agent-log.md`](agent-log.md),
[`sprints/`](sprints/), and [`slack-export/`](slack-export/) for the process trail.

---

## Tech stack

| Layer    | Technology                                   |
|----------|----------------------------------------------|
| Backend  | PHP 8.2+, **Laravel 11** (REST API)          |
| Auth     | Laravel Sanctum (token auth, 3 roles)        |
| Database | **MySQL 8**                                  |
| Frontend | **React 19 + Vite**, Tailwind CSS            |
| Tests    | Pest / PHPUnit (feature tests)               |
| CI       | GitHub Actions (`.github/workflows/ci.yml`)  |
| Models   | via **EastRouter** (see below)               |

---

## Live URL

`https://pulsedesk.69.62.76.226.sslip.io` — live (Caddy HTTPS, React build + /api proxy to Laravel)

If the live URL is down at judging time, follow **Run locally** below — the app
runs from a fresh clone.

---

## Run locally

**Prerequisites:** PHP 8.2+ (`mbstring xml bcmath curl zip mysql`), Composer,
MySQL 8, Node 20+.

### 1. Backend (Laravel API)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# configure DB in .env (DB_DATABASE / DB_USERNAME / DB_PASSWORD), then:
php artisan migrate --seed      # creates schema + demo data
php artisan serve --host 0.0.0.0 --port 8000
```

### 2. Frontend (React + Vite)

```bash
cd frontend
npm install
cp .env.example .env            # set VITE_API_URL=http://localhost:8000
npm run dev -- --host           # http://localhost:5173
```

### Seeded demo data

`php artisan migrate --seed` creates **1 organization**, **1 admin**, **2 agents**,
**2 customers**, and **~12 tickets**.

| Role     | Email (seeded)        | Password   |
|----------|-----------------------|------------|
| Admin    | `admin@pulsedesk.test`   | `password` |
| Agent    | `agent1@pulsedesk.test`  | `password` |
| Customer | `customer1@pulsedesk.test` | `password` |

> _Update the table above if the seeder uses different credentials._

---

## Running the tests

```bash
cd backend
php artisan test            # Pest/PHPUnit — covers core ticket + tenancy flows
```

CI runs the same install → migrate → test flow on every PR
([`.github/workflows/ci.yml`](.github/workflows/ci.yml)).

---

## Features

**Must (core):** organizations (tenants) · auth with 3 roles (admin/agent/customer)
· tickets (subject, description, status `open/pending/resolved/closed`, priority
`low/medium/high/urgent`, requester, assignee, tags, timestamps) · threaded replies
(public reply + internal agent-only note) · filterable ticket board (status,
priority, assignee) with text search · documented JSON API · demo seeder.

**Should (depth):** SLA policies & timers · queues / claim-a-ticket · activity-log
audit trail · dashboard metrics (open by status/priority, avg first-response time,
SLA-breach rate) · notifications.

**Stretch:** canned responses/macros · ticket merge · CSAT · customer portal · bulk
actions · realtime updates · full-text search · CSV export.

See [`ARCHITECTURE.md`](ARCHITECTURE.md) for the data model, API routes, and the
multi-tenancy approach.

---

## Multi-tenancy (security model)

Every tenant-owned record carries an `organization_id`. The tenant is derived from
the **authenticated session**, never from a client-supplied id. A global query
scope enforces isolation on every model, and a feature test asserts that Org A
cannot read or modify Org B's tickets.

---

## Models used (via EastRouter)

All agent model calls route through **EastRouter** (`https://api.eastrouter.com`).

| Use                        | Model                          |
|----------------------------|--------------------------------|
| Planning / architecture    | `z-ai/glm-5.1`     |
| Bulk coding (default)      | `z-ai/glm-5.1`                 |
| Long-horizon coding        | `moonshotai/kimi-k2.7-code`         |
| Cheap repetitive edits     | `z-ai/glm-4.5-air` |

Agent configs: [`agents/hermes/hermes-config.yaml`](agents/hermes/hermes-config.yaml)
(planner, memory enabled) and [`agents/openclaw/openclaw.json`](agents/openclaw/openclaw.json)
(coder + Slack + tools). Secrets are redacted to `${EASTROUTER_API_KEY}`.

---

## Repository layout

```
README.md            ARCHITECTURE.md      SUBMISSION.md       agent-log.md
backend/             Laravel 11 API (models, controllers, migrations, seeders, tests)
frontend/            React 19 + Vite UI
agents/hermes/       hermes-config.yaml   (planner)
agents/openclaw/     openclaw.json        (coder)
sprints/             sprint-01.md, sprint-02.md (≥2)
slack-export/        Slack export or screenshots/  (all 5 channels)
evidence/screenshots/ app + infra screenshots (incl. green CI run)
.github/workflows/   ci.yml
```

---

## Agent workflow

```
spec → tight sprint → Hermes plans & assigns in Slack
     → OpenClaw codes + tests + reports → CI runs
     → human reviews & merges PR → next sprint
```

Slack channels: `#sprint-main` (you ↔ Hermes) · `#agent-coder` (Hermes ↔ OpenClaw)
· `#agent-log` (OpenClaw reports) · `#ci-cd` (build/test) · `#human-review`
(approvals). A human is always the merge actor on `main`.

---

## License & attribution

Built by Aman Kumar for the NMG Labs Forge 2 sprint. See
[`SUBMISSION.md`](SUBMISSION.md) for the submission checklist.

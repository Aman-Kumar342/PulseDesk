# CLAUDE.md — Forge 2 · Edition 1 — **PulseDesk**

Guidance for Claude Code (and any AI agent) working in this repository.
Source of truth: https://labs.nmgdigital.com/forge2-main-7b3f9a21c4

Builder: **Aman Kumar** · GitHub `Aman-Kumar342` · amanx.gitx@gmail.com · WhatsApp 7004877604

---

## 0. The one-paragraph brief

Build **PulseDesk**, a **multi-tenant support-desk SaaS**, using **Laravel 11 +
MySQL 8 + React 19**. But the app is only half the score — the *process* is the
other half. You must run **≥2 real agile sprints** where **Hermes** (planner/PO)
and **OpenClaw** (coder) coordinate **through Slack**, OpenClaw opens PRs, CI
runs, and **you (the human) merge to `main`**. **Every artifact lives in the Git
repo** — no Google Drive / Loom / YouTube / external links are opened by judges.

---

## 1. Tech stack (required — wrong stack = capped low)

| Layer        | Tech (exact)                                            |
|--------------|---------------------------------------------------------|
| Language     | **PHP 8.2+**                                            |
| Backend      | **Laravel 11** REST API                                 |
| Auth         | **Laravel Sanctum**                                     |
| Database     | **MySQL 8**                                             |
| Frontend     | **React 19 + Vite**, **Tailwind**                       |
| Tests        | **Pest or PHPUnit** feature tests (API + tenancy flows) |
| CI           | **GitHub Actions** (`.github/workflows/ci.yml`)         |

Local Ollama is allowed **only** as a fallback if EastRouter is unreachable.
**All model usage must route through EastRouter.**

---

## 2. Deployment target — this VPS

```bash
ssh aman@69.62.76.226
```

Headless Linux server. Provision the full PulseDesk runtime, not just agents:

```bash
# Backend runtime
php -v            # need 8.2+   (php8.2-cli php8.2-mysql php8.2-mbstring
                  #              php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip)
composer -V
mysql --version   # MySQL 8

# Frontend runtime
node -v           # for React 19 + Vite
```

VPS practices:
- Run long-lived processes in **tmux** so an SSH drop doesn't kill them:
  ```bash
  tmux new -s api      # php artisan serve --host 0.0.0.0 --port 8000
  tmux new -s web      # npm run dev -- --host
  tmux new -s hermes   # Hermes orchestrator
  tmux new -s openclaw # OpenClaw gateway
  # detach: Ctrl-b d   |   reattach: tmux attach -t <name>
  ```
- A reachable **live URL** earns demo points — bind `--host 0.0.0.0` and open the
  firewall port. A "localhost-only" or dead app is explicitly capped low.
- Keep secrets in `.env` (git-ignored). Commit only `.env.example` with
  placeholders. The repo is **public** — never commit a real key.

---

## 3. Required repo layout (match it exactly — judged)

```
.
├── README.md                 # EXACT run steps · live URL (if any) · models used
├── ARCHITECTURE.md           # data model · API routes · multi-tenancy approach
├── SUBMISSION.md             # honest checklist
├── agent-log.md              # prompt→Hermes plan→OpenClaw report, real text
├── backend/                  # Laravel 11
│   ├── app/ (Models, Http/Controllers, ...)
│   ├── database/migrations, database/seeders
│   ├── tests/                # Pest/PHPUnit feature tests
│   └── .env.example
├── frontend/                 # React 19 + Vite
│   └── .env.example
├── agents/
│   ├── hermes/hermes-config.yaml     # secrets → ${EASTROUTER_API_KEY}
│   └── openclaw/openclaw.json        # secrets redacted
├── sprints/
│   ├── sprint-01.md          # goal · issues · shipped · slipped
│   └── sprint-02.md          # (minimum 2)
├── slack-export/             # real export (channels.json, users.json, per-channel)
│   └── screenshots/          # OR per-channel screenshots
├── evidence/screenshots/     # app + infra screenshots
└── .github/workflows/ci.yml
```

---

## 4. PulseDesk feature scope

### MUST (get these solid first — 14 pts "app works")
- **Organizations (tenants)**.
- **Auth with 3 roles:** admin, agent, customer (Sanctum).
- **Tickets:** subject, description, `status` (open/pending/resolved/closed),
  `priority` (low/medium/high/urgent), requester (customer), assignee (agent),
  tags/labels, created/updated timestamps.
- **Threaded replies:** a **public reply** (customer-visible) and an **internal
  note** (agents only).
- **Ticket list/board** filterable by status, priority, assignee + text search on
  subject/body.
- **Documented JSON API** consumed by the React app.
- **Seeder:** 1 org, 1 admin, 2 agents, 2 customers, ~12 tickets.

### SHOULD (depth)
SLA policies & timers (per-priority response/resolution targets) · queues &
assignment / claim a ticket · activity log / audit trail (who + when) · dashboard
metrics (open by status/priority, avg first-response time, SLA-breach rate) ·
notifications.

### STRETCH
Canned responses/macros · ticket merge · CSAT · separate customer portal · bulk
actions · realtime updates · full-text search · CSV export.

---

## 5. Multi-tenancy — security-gated (12 pts, adversarially probed)

> **Every record belongs to an org; a user from Org A can _never_ see Org B's
> data. Scope every query by `organization_id`.**

- Derive the tenant **from the authenticated session**, never from a client-supplied
  id/param.
- Apply a **global scope** (or equivalent) on every tenant-owned model so no query
  can leak across orgs. Don't rely on per-controller `where('organization_id', ...)`.
- Judges run an **adversarial cross-tenant probe**. A confirmed cross-tenant leak
  caps the whole submission low. Add a feature test that asserts Org A cannot
  read/modify Org B's tickets.

---

## 6. Agent workflow — the biggest scoring slice (20 + 16 + 10 pts)

**The loop (repeat ≥2 sprints):**
```
spec → tight sprint (a few scoped issues)
     → Hermes plans & assigns in Slack
     → OpenClaw codes + tests + reports
     → CI runs
     → YOU review & merge the PR
     → next sprint
```

- **Hermes** (orchestrator/PO): produces sprint backlogs, assigns scoped issues,
  reviews reports. Config = `agents/hermes/hermes-config.yaml`, **memory enabled**,
  provider `eastrouter`, a **planning model** (e.g. `deepseek/deepseek-v4-pro` or
  `z-ai/glm-5.1`).
- **OpenClaw** (coder): implements issues, runs code/tests, opens PRs, reports.
  Config = `agents/openclaw/openclaw.json` — a **tight, real** config with
  EastRouter model + Slack + workspace/command-exec + tools, **proven to run**.
- **Human-in-the-loop:** agents talk **through Slack**, not silently. **You** are
  the merge actor to `main`. A bot running unsupervised straight to `main` loses
  the entire process score.
- **Bonus (+5 cap):** a 2nd OpenClaw **reviewer/QA** agent (+3) and/or a
  **CI/deploy** agent (+2).

### Slack channels (all 5, with real traffic)
| Channel          | Flow                                  |
|------------------|---------------------------------------|
| `#sprint-main`   | you ↔ Hermes                          |
| `#agent-coder`   | Hermes ↔ OpenClaw                     |
| `#agent-log`     | OpenClaw's structured reports         |
| `#ci-cd`         | build / test results                  |
| `#human-review`  | release-candidate approvals           |

OpenClaw reports use: **What I Did / What's Left / What Needs Your Call**.

---

## 7. EastRouter (all models route here)

- Sign up at https://dashboard.eastrouter.com with Google or GitHub; share the
  registered email/handle with the Forge captain for the $50 top-up; create an API
  key (`sk-er_<id>_<secret>`).
- **Base URLs:**
  - OpenAI SDK / Hermes → `https://api.eastrouter.com/v1`
  - Claude-compatible (OpenClaw Anthropic mode) → `https://api.eastrouter.com/api/anthropic`
- **Models / routing (keep $50 lasting all day → keep sprints tight):**
  - Plan/architect → `deepseek/deepseek-v4-pro` (1M ctx) or `z-ai/glm-5.1`
  - Bulk coding → `z-ai/glm-5.1` (200K, recommended default)
  - Long-horizon coding → `moonshotai/kimi-k2.6` (262K)
  - Cheap repetitive edits → `z-ai/glm-4.5-air` / `deepseek/deepseek-v4-flash`

Store the key as `EASTROUTER_API_KEY` in `.env`; reference it as
`${EASTROUTER_API_KEY}` in committed configs.

---

## 8. CI / testing

- `.github/workflows/ci.yml` must **install, migrate, and run tests on each PR**.
- **Pest or PHPUnit** feature tests covering at least **core ticket + tenancy**
  flows.
- At least **one green run** must be visible on the Actions tab (screenshot it
  into `evidence/screenshots/`).

---

## 9. Evidence checklist (all in-repo — judges open no external links)

- `agents/hermes/hermes-config.yaml` + `agents/openclaw/openclaw.json` (secrets
  redacted to `${EASTROUTER_API_KEY}`).
- `agent-log.md` showing real prompt → Hermes plan/assignment → OpenClaw report.
- `sprints/sprint-01.md`, `sprint-02.md` (goal, issues, shipped, slipped).
- Slack: real export under `slack-export/` (`channels.json`, `users.json`,
  per-channel folders) **or** per-channel screenshots in `slack-export/screenshots/`
  (e.g. `sprint-main-01.png`) — visible content + timestamps.
- `evidence/screenshots/`: ticket list/board · ticket detail w/ conversation ·
  dashboard/metrics · login screen · OpenClaw gateway in terminal · Hermes running
  · a **green GitHub Actions** run.

---

## 10. Standard commands

```bash
# Backend (run from backend/)
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan test                       # Pest/PHPUnit
php artisan serve --host 0.0.0.0 --port 8000

# Frontend (run from frontend/)
npm install
cp .env.example .env
npm run dev -- --host                  # Vite
npm run build
```

---

## 11. Judging rubric (out of 100) — optimize against this

| Criterion | Pts |
|---|---|
| Agent orchestration loop (Hermes↔OpenClaw↔you) | **20** |
| OpenClaw config & execution (tight real `openclaw.json`, proven) | **16** |
| App works end-to-end (fresh clone or live URL) | **14** |
| Multi-tenant isolation (security-gated) | **12** |
| Hermes orchestration & PO setup (memory, real decomposition) | **10** |
| Sprints → CI/CD → human-merged PRs (≥2, you merge) | **10** |
| Slack workspace + export quality (all 5 channels, real traffic) | **6** |
| Code & architecture (clean Laravel/REST/data model, passing tests) | **5** |
| Repo & docs (layout, README run steps, honest SUBMISSION.md) | **4** |
| Live demo (runs on demand from fresh clone / live URL) | **3** |
| Bonus agents (QA +3, CI/deploy +2) | **+5** |

### Hard DQs
No public/clonable repo · built the wrong thing (not PulseDesk) · **fabricated
evidence** · >75% plagiarised or a forked existing app.

### Capped low
No genuine two-agent loop · confirmed cross-tenant leak · app dead /
localhost-only / frontend-with-no-backend · not the required stack.

---

## 12. Working priorities for any agent in this repo

1. **Tenancy first, always.** Every new model/query is scoped by org from the auth
   session. Never trust a client-supplied org id.
2. **MUST features before SHOULD/STRETCH.** A solid Must tier + clean tenancy beats
   half-finished depth.
3. **Small scoped issues** that fit one sprint, one PR. Never one giant commit.
4. **Evidence as you go** — append `agent-log.md`, save Slack/app screenshots into
   the repo the moment something works. Reconstructing later reads as fabricated.
5. **Humans merge `main`.** Open PRs; never auto-merge to `main`.
6. **Keep secrets out of git.** `.env.example` only.

---

## 13. Submission

Submit at the form on https://labs.nmgdigital.com/forge2-main-7b3f9a21c4
(only the **public GitHub repo URL** counts — no Drive/Loom/video). The latest
resubmission is scored. Captain: Ayush Gupta — labs@nmgdigital.com

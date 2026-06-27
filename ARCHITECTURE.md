# PulseDesk — Architecture

## Stack
- Backend: Laravel 11 (PHP 8.3), Sanctum auth, MySQL 8
- Frontend: React 19 + Vite, Tailwind CSS
- CI: GitHub Actions (backend tests + frontend build)

## Multi-tenancy (security model)
Every tenant-owned row carries `organization_id`. The tenant is derived from the
**authenticated user's session** (`auth()->user()->organization_id`), never from a
client-supplied id. A global Eloquent scope (`BelongsToOrganization`) filters every
query so Org A can never read or write Org B's data. A feature test asserts the
cross-tenant denial.

## Data model (target)
- organizations (tenants)
- users (role: admin | agent | customer, organization_id)
- tickets (subject, description, status, priority, requester_id, assignee_id,
  organization_id, timestamps)
- ticket_replies (ticket_id, author_id, body, is_internal)
- tags, ticket_tag (pivot)
- (should) sla_policies, activity_logs

## API routes (target, all under /api, Sanctum-protected)
- POST   /api/login, POST /api/logout, GET /api/me
- GET/POST           /api/tickets            (list w/ filters: status, priority, assignee, q)
- GET/PATCH/DELETE   /api/tickets/{id}
- POST               /api/tickets/{id}/replies
- POST               /api/tickets/{id}/assign
- GET                /api/dashboard/metrics

## Agent workflow
Hermes (planner, EastRouter z-ai/glm-5.1, memory on) decomposes the spec into
scoped issues in Slack; OpenClaw (coder, EastRouter) implements, tests, opens a PR
and reports in #agent-log; CI runs; a human merges to main. See agent-log.md.

## Bonus agent
OpenClaw-QA — a second OpenClaw role acting as code reviewer/QA on EastRouter; reviews PRs and posts findings to #human-review (see agent-log.md, slack-export/human-review.json).

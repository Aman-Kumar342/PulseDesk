# Sprint 03 — Activity log + role hardening (genuine Hermes→OpenClaw→QA loop)

**Goal:** Auditability + correct role-based access on top of the Must tier.

## Issues
- [x] #1 Ticket activity log / audit trail (TicketActivity, auto-log created/status/assigned/replied, GET /tickets/{ticket}/activity)
- [x] Registration (org signup) — POST /api/register + Register screen
- [x] Role-based visibility — customers see only their own tickets, never internal notes
- [x] Admin-only team management (GET/POST /api/users)
- [ ] SLA timers, notifications (backlog)

## Loop (all via EastRouter z-ai/glm-5.1)
Hermes planned in #sprint-main → OpenClaw coded → **OpenClaw-QA reviewed** (caught a defense-in-depth org-guard gap + missing tests) → OpenClaw fixed → **human merged PR #2**. See agent-log.md + slack-export/.

## Shipped
16 feature tests passing (incl. cross-tenant isolation, role visibility, user-management). CI green.

## Slipped
SLA timers and notifications deferred.

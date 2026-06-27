# Sprint 01 — Core ticketing + multi-tenancy foundation

**Goal:** Orgs, auth (3 roles, Sanctum), tickets CRUD scoped by organization_id, seeder, tests.

## Issues
- [x] #1 organizations + users(org+role) migrations & models; Sanctum login/me/logout
- [x] #2 BelongsToOrganization global scope (tenant from auth session) + cross-tenant feature test
- [x] #3 tickets migration/model + index/show/store/update/assign API (filters: status, priority, assignee, search)
- [x] #4 seeder: 1 org, admin, 2 agents, 2 customers, ~12 tickets (+ 2nd org to prove isolation)

## Shipped
- Working Laravel 11 + MySQL API; 8 feature tests passing (incl. cross-tenant isolation).
- Planned in #sprint-main by Hermes (EastRouter z-ai/glm-5.1). Merged to main by human (Aman).

## Slipped
- SLA timers / audit log (Should-tier) deferred to backlog.

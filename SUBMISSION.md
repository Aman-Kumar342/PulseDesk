# PulseDesk — Submission Checklist

- [x] Public GitHub repo (clonable)
- [x] Required stack: Laravel 11 + MySQL 8 + React 19
- [ ] Must features: orgs, 3 roles, tickets, threaded replies, filter/search, JSON API, seeder
- [ ] Multi-tenant isolation + cross-tenant feature test
- [ ] >=2 sprints with docs (sprints/sprint-0N.md)
- [ ] Real Hermes -> OpenClaw handoff in Slack (slack-export/)
- [ ] >=1 human-merged PR
- [ ] CI green on Actions tab
- [ ] Agent configs committed (agents/, secrets redacted)
- [ ] agent-log.md with real loop
- [ ] Evidence screenshots (evidence/screenshots/)

## Builder
Aman Kumar · GitHub @Aman-Kumar342 · amanx.gitx@gmail.com

## Models used (via EastRouter)
- Planner (Hermes): z-ai/glm-5.1
- Coder (OpenClaw): z-ai/glm-5.1 (option: moonshotai/kimi-k2.7-code)
- Cheap edits: z-ai/glm-4.5-air

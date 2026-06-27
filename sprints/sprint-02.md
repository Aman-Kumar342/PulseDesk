# Sprint 02 — Replies, board filters, React UI, dashboard

**Goal:** Threaded replies (public + internal), ticket board with filters+search, dashboard metrics, React UI, live deploy.

## Issues
- [x] #5 ticket_replies (public reply vs internal note; customers can't post internal) + API
- [x] #6 ticket list filters (status/priority/assignee) + text search on subject/body
- [x] #7 React 19 UI: login, ticket board, ticket detail w/ conversation, internal-note toggle
- [x] #8 dashboard metrics endpoint + cards; deployed live via Caddy (HTTPS)

## Shipped
- React 19 + Vite + Tailwind UI consuming the JSON API; live at https://pulsedesk.69.62.76.226.sslip.io.
- GitHub Actions CI green (backend tests on MySQL + frontend build).

## Slipped
- CSAT / canned responses (Stretch) deferred.

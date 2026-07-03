# File Map & Workspace Directory Structure

This document provides a tree map of the recruitment project workspace and summarizes the purpose of each key folder.

---

## 1. Project Directory Tree

```
c:\Users\Hp\Downloads\Enterprise_recruitment\
├── deploy-site/                 # Static HTML/JS preview web assets
│   ├── app.js
│   ├── dist/
│   ├── index.html
│   ├── package.json
│   └── styles.css
├── index.html                   # Global landing redirect/intro page
└── recruitment-system/          # Core Laravel 12 / Filament 3 Backend App
    ├── app/                     # Backend Logic
    │   ├── Events/              # System events (e.g., OfferAccepted)
    │   ├── Exceptions/          # Custom exceptions
    │   ├── Filament/            # Admin resources and dashboards
    │   ├── Http/                # HTTP controllers & custom middlewares
    │   ├── Listeners/           # Event listeners (e.g., email dispatchers)
    │   ├── Mail/                # HTML/text mail templates
    │   ├── Models/              # Eloquent schemas & scopes
    │   ├── Notifications/       # Custom notifications
    │   ├── Policies/            # RBAC policies for panel assets
    │   ├── Providers/           # App and Panel service bindings
    │   └── Services/            # Business logic layers (divided by module)
    ├── bootstrap/               # Framework boot & caching files
    ├── config/                  # App, database, and recruitment settings
    ├── database/                # SQLite DB file, migrations, seeders
    ├── public/                  # Public index.php and static assets
    ├── resources/               # Raw UI assets
    │   └── views/               # Blade & layouts templates
    ├── routes/                  # Route files (web.php, candidate.php)
    ├── storage/                 # Uploaded resumes and logs directory
    └── tests/                   # Test suits
```

---

## 2. Directory Explanations

### `deploy-site`
Contains a standalone, static SPA version of the landing elements. It includes pre-bundled JS/CSS styles used for isolated frontend deployment previews.

### `recruitment-system/app`
The primary hub of server-side logic:
- **`app/Filament`**: Customizes the backoffice admin portal pages and forms.
- **`app/Services`**: Isolates raw database interactions, transactions, metrics computations, and operations away from controllers.

### `recruitment-system/database`
- **`database/migrations`**: Sequenced table definitions and column details.
- **`database/seeders`**: Populates default locations, departments, standard users, and roles.
- **`database/database.sqlite`**: The local development database.

### `recruitment-system/resources`
- **`resources/views/careers`**: Holds the public careers website pages.
- **`resources/views/portal`**: Contains the Candidate Portal forms, dashboard, profile edit, and documents list templates.
- **`resources/views/layouts`**: Contains the parent layouts sharing CSS configs.

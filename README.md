# Marikina City Animal & Welfare Management System

A web-based animal welfare management platform for Marikina City — handling animal records, adoption requests, vaccination scheduling, and incident reporting.

---

## Table of Contents

- [Project Description](#project-description)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation & Setup](#installation--setup)
- [Running the App](#running-the-app)
- [Default Credentials](#default-credentials)
- [Security Notes](#security-notes)
- [Development Notes](#development-notes)

---

## Project Description

The Marikina City Animal & Welfare Management System is a PHP/MySQL web application built as a 3rd-year capstone project. It provides a unified platform for managing animal shelter operations, processing adoption requests, tracking vaccinations, and reporting animal-related incidents.

---

## Features

### User Authentication
- Secure login and registration
- Session-based authentication with bcrypt password hashing
- HTTP-only session cookies with SameSite protection
- Session regeneration on login to prevent fixation attacks

### Dashboard
- Personalized welcome with quick-link cards (Report Incident, My Reports, Adopt Animal, Vaccination)
- 3-column summary grid: My Reports · My Adoption Requests · Vaccination Schedule
- Featured Animals carousel (animals available for adoption)
- All sections scoped to the logged-in user

### Animal Management
- Full animal records with name, type, age, gender, status, description
- Status lifecycle: `In Shelter` → `Available for Adoption` → `Reserved` → `Adopted`
- `Reserved` status automatically set when an adoption request is submitted — prevents duplicate applications

### Adopt an Animal
- Browse animals with live status badges: **Available** (green) / **Reserved** (orange) / **Adopted** (teal)
- Disabled cards for Reserved/Adopted animals
- Clicking "View Details" leads to the full adoption request form

### Adoption Request Form
- Full applicant profile: personal info, address, employment, home type, other pets, children, references
- On submit: saves request with `user_id`, sets animal status to `Reserved`
- Success modal with reference ID → redirects to My Adoption Requests

### My Adoption Requests (`adoptions.php`)
- Stat cards: Total / Pending / Approved / Completed
- Table of all requests scoped to the logged-in user
- Status badges: Pending / Approved / Rejected / Completed

### Vaccination Management
- Log vaccinations per animal with vaccine type, schedule date, vet staff
- Status tracking: Upcoming / Done / Overdue

### Incident Reporting (`incidents.php`)
- Full incident form: date, time, location, barangay, animal details, victim info, severity, injury description
- Severity levels: Low / Medium / High / Critical
- On submit: success modal with Reference ID → redirects to My Reports

### My Reports (`my-incidents.php`)
- Stat cards: Total / Pending / In Progress / Resolved
- Full incident history table for the logged-in user
- **View** button opens an inline detail modal with all field data (no page redirect)

### Reports & Schedule
- System report generation
- Event/appointment scheduling

---

## Technology Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3 |
| Database | MySQL 8.x |
| Frontend | HTML5, CSS3 (custom design system) |
| Server | PHP built-in server (`php -S`) |
| Auth | Session-based, prepared statements |

### CSS Architecture
All styles are extracted into separate files in `assets/css/`:

| File | Scope |
|---|---|
| `variables.css` | CSS custom properties (colors, spacing, typography) |
| `nav.css` | Sidebar navigation |
| `admin.css` | Design system — page-header, stat-cards, tables, modals, badges |
| `forms.css` | Form layout and input styles |
| `adopt-animal.css` | Animal browse grid and card styles |
| `animal-detail.css` | Animal detail page |
| `auth.css` | Login / Register pages |
| `index.css` | Public landing page |

---

## Project Structure

```
3rd Year Capstone/
├── index.php                     # Root redirect → src/index.php
├── README.md
│
├── src/                          # All application pages
│   ├── session-handler.php       # Session config (called before session_start)
│   ├── nav-menu.php              # Sidebar navigation (included on all pages)
│   ├── page-template.php         # Shared page layout
│   │
│   ├── index.php                 # Public landing page
│   ├── login.php                 # Authentication
│   ├── register.php              # User registration
│   ├── logout.php                # Session destroy
│   │
│   ├── dashboard.php             # Main user dashboard
│   ├── animals.php               # Animal records management
│   ├── animal-detail.php         # Single animal detail + adopt CTA
│   ├── adopt-animal.php          # Browse animals grid (Available/Reserved/Adopted)
│   ├── adoption-request.php      # Full adoption application form
│   ├── adoptions.php             # My Adoption Requests (user-scoped)
│   │
│   ├── incidents.php             # Report an incident form
│   ├── my-incidents.php          # My Reports — history + detail modal
│   │
│   ├── vaccinations.php          # Vaccination records
│   ├── reports.php               # Reports module
│   └── schedule.php              # Schedule/calendar module
│
├── assets/
│   ├── css/                      # Modular CSS files (see CSS Architecture above)
│   └── images/
│       └── Lgo.png
│
└── sql/
    ├── marikina_db.sql           # Full schema + seed data
    ├── add-incidents-table.sql   # Incidents table migration
    ├── adoption-application-schema.sql  # Extended adoptions table schema
    └── update-animals.sql        # Animals table updates
```

---

## Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| id | INT PK | Auto increment |
| username | VARCHAR(50) | Unique |
| password | VARCHAR(255) | bcrypt hashed |
| full_name | VARCHAR(100) | |
| role | VARCHAR(50) | e.g. Veterinarian |
| created_at | TIMESTAMP | |

### `animals`
| Column | Type | Notes |
|---|---|---|
| id | INT PK | |
| name | VARCHAR(100) | |
| type | VARCHAR(50) | Dog, Cat, etc. |
| age | INT | Years |
| gender | VARCHAR(20) | |
| status | ENUM | `In Shelter`, `Available for Adoption`, `Reserved`, `Adopted`, `Rescued`, `Deceased` |
| intake_date | TIMESTAMP | |
| description | TEXT | |

### `adoptions`
| Column | Type | Notes |
|---|---|---|
| id | INT PK | |
| user_id | INT | FK → users.id (nullable for legacy rows) |
| animal_id | INT | FK → animals.id |
| animal_name | VARCHAR | |
| animal_type | VARCHAR | |
| applicant_name | VARCHAR | |
| email / phone / address | VARCHAR | |
| employment, home_type, home_ownership | VARCHAR | |
| rental_permission, have_yard | INT/VARCHAR | |
| other_pets_info, has_children, children_ages | VARCHAR | |
| adoption_reason | TEXT | |
| reference1_name/phone, reference2_name/phone | VARCHAR | |
| status | ENUM | `Pending`, `Approved`, `Rejected`, `Completed` |
| request_date | TIMESTAMP | |

### `incidents`
| Column | Type | Notes |
|---|---|---|
| id | INT PK | |
| user_id | INT | FK → users.id |
| incident_date / incident_time | DATE / TIME | |
| location, barangay | VARCHAR | |
| animal_type, animal_color, animal_size | VARCHAR | |
| animal_distinguishing_features | TEXT | |
| victim_name, victim_age, victim_contact | VARCHAR | |
| injury_description | TEXT | |
| severity_level | ENUM | `Low`, `Medium`, `High`, `Critical` |
| status | ENUM | `New`, `Under Review`, `Resolved`, `Closed` |
| treatment_received, remarks | TEXT | |
| created_at / updated_at | TIMESTAMP | |

### `vaccinations`
| Column | Type | Notes |
|---|---|---|
| id | INT PK | |
| animal_name | VARCHAR | |
| vaccine_type | VARCHAR | |
| schedule_date | DATETIME | |
| vet_staff | VARCHAR | |
| status | ENUM | `Upcoming`, `Done`, `Overdue` |

---

## Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/kaneshirojangg/Digital-Animal-Welfare-System-for-Marikina-City-Capstone-for-Third-Year-.git
cd "Digital-Animal-Welfare-System-for-Marikina-City-Capstone-for-Third-Year-"
```

### 2. Create the database and user

```bash
sudo mysql -u root -e "
  CREATE DATABASE IF NOT EXISTS marikina_db;
  CREATE USER IF NOT EXISTS 'marikina_user'@'localhost' IDENTIFIED BY 'marikina_password';
  GRANT ALL PRIVILEGES ON marikina_db.* TO 'marikina_user'@'localhost';
  FLUSH PRIVILEGES;
"
```

### 3. Import the schema

```bash
mysql -u marikina_user -p'marikina_password' marikina_db < sql/marikina_db.sql
```

### 4. Apply the adoption application schema (extended columns)

```bash
mysql -u marikina_user -p'marikina_password' marikina_db < sql/adoption-application-schema.sql
```

### 5. Add the `user_id` column to adoptions (if not already present)

```bash
mysql -u marikina_user -p'marikina_password' marikina_db -e \
  "ALTER TABLE adoptions ADD COLUMN IF NOT EXISTS user_id INT DEFAULT NULL AFTER id;"
```

### 6. Add `Reserved` to the animals status enum (if not already present)

```bash
mysql -u marikina_user -p'marikina_password' marikina_db -e \
  "ALTER TABLE animals MODIFY status ENUM('In Shelter','Available for Adoption','Reserved','Adopted','Rescued','Deceased') DEFAULT 'In Shelter';"
```

---

## Running the App

```bash
php -S localhost:8000 -t src
```

Open: [http://localhost:8000](http://localhost:8000)

> **Note:** Use `-t src` so the server root is the `src/` directory.

---

## Default Credentials

Register a new account via the Register page, or use any account already seeded in `marikina_db.sql`.

Database credentials (used in PHP files):

```
Host:     localhost
DB:       marikina_db
User:     marikina_user
Password: marikina_password
```

> Change these in production.

---

## Security Notes

- All DB queries use prepared statements (`mysqli::prepare`)
- Passwords hashed with `PASSWORD_DEFAULT` (bcrypt)
- Session cookies: `httponly = true`, `samesite = Lax`
- Session ID regenerated on login (`session_regenerate_id`)
- User-scoped queries on adoption/incident data (always filtered by `user_id`)

---

## Development Notes

### Key Conventions
- Every protected page starts with `include 'session-handler.php'; session_start();` followed by a session check
- All DB connections use `$conn = new mysqli('localhost', 'marikina_user', 'marikina_password', 'marikina_db')`
- CSS class naming: `page-header`, `stats-grid`, `stat-card [primary|info|warning|success]`, `big-number`, `table-container`, `status-badge status-[name]`, `empty-state`
- The `main-content` div accounts for the fixed 280px sidebar via `margin-left`

### Adding a New Page
1. Create `src/your-page.php`
2. Start with `include 'session-handler.php'; session_start();` + session guard
3. Set `$activePage = 'your-page';` (used by nav-menu.php to highlight active link)
4. Add nav item in `nav-menu.php`
5. Link CSS from `../assets/css/`


---


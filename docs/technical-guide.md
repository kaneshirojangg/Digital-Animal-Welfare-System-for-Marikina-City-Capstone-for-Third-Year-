# Technical Guide — System Architecture & Q&A
## Marikina City Animal & Welfare Management System

> This document covers how the system actually works — Frontend, Backend, Database — with precise Q&A for technical questions from the panel. Answers are 2–3 sentences max. Know these cold.

---

## SECTION 1: FRONTEND

### How It Works

The frontend is built with **HTML5 and CSS3 only** — no JavaScript frameworks, no React, no Vue. All page rendering is server-side via PHP. The browser receives a fully rendered HTML page on every request.

### Design System

All visual consistency comes from a modular CSS architecture in `assets/css/`:

| File | What It Controls |
|---|---|
| `variables.css` | Global CSS custom properties — colors, font sizes, spacing, shadows |
| `nav.css` | Fixed 280px sidebar, active link highlighting, logo area |
| `admin.css` | Core design system — page-header, stat-cards, tables, modals, status badges, empty states, buttons |
| `forms.css` | Form layouts, input fields, labels, validation states |
| `adopt-animal.css` | Animal browse grid, card layout, status badge variants (Available/Reserved/Adopted) |
| `animal-detail.css` | Single animal detail page layout |
| `auth.css` | Login and register card layout |
| `index.css` | Public landing page hero, features section |

### Key UI Components

**`page-header`** — Every page starts with this block: a title, subtitle, and optional action button aligned right.

**`stats-grid` + `stat-card [variant]`** — Used on Dashboard, My Reports, My Adoption Requests, Adoptions. Four cards per row with `primary`, `info`, `warning`, `success` color variants. Each has a `stat-card-header h3`, `big-number`, and `stat-card-footer`.

**`table-container` + `<table>`** — Consistent table layout across all list pages. `<thead>` has a tinted background. `<tbody>` rows have hover states.

**`status-badge status-[name]`** — Inline pill labels. Class name is generated dynamically from the DB value: `status-new`, `status-pending`, `status-under-review`, `status-resolved`, etc.

**`modal`** — Full-screen overlay with centered `modal-content`. Used for success confirmation on form submit (incidents, adoption), and for the incident detail view on My Reports.

**`empty-state`** — Centered icon + heading + description + CTA button shown when a query returns 0 rows.

### Navigation

`nav-menu.php` is included on every authenticated page. It reads `$activePage` (set per page as a PHP variable) to apply the `.active` CSS class to the correct nav link. The sidebar is `position: fixed` at 280px width, and every page's `.main-content` has `margin-left: 280px`.

---

### Frontend Keypoints for Defense

- **No JavaScript framework** — everything is PHP-rendered server-side, keeping the stack minimal and fully auditable
- **CSS custom properties** in `variables.css` mean one color change updates the entire system
- **Consistent design language** — every page uses the same component classes, so the UI is predictable for users
- **Responsive** — media queries in CSS handle smaller screens by collapsing the sidebar

---

### Frontend Manuscript

> "The frontend uses no JavaScript frameworks — all pages are rendered server-side by PHP and delivered to the browser as complete HTML. Visual consistency is enforced through a modular CSS design system: a `variables.css` file defines all colors, spacing, and typography as CSS custom properties, and `admin.css` provides reusable components — stat cards, tables, status badges, and modals — that are used uniformly across every page. Navigation is handled by a shared `nav-menu.php` component that highlights the active page based on a PHP variable, maintaining context for the user at all times."

---

## SECTION 2: BACKEND

### How It Works

The backend is **PHP 8.3** running on the PHP built-in web server (`php -S localhost:8000 -t src`). Every `.php` file is both the controller and the view — no MVC framework. Each page follows this structure:

```
1. include 'session-handler.php'
2. session_start()
3. Session guard (redirect to login if not authenticated)
4. Open DB connection (new mysqli)
5. Run queries
6. Close DB connection
7. Output HTML (with embedded PHP for dynamic data)
```

### Session & Authentication

**`session-handler.php`** configures the session before `session_start()` is ever called:
- Cookie: `httponly = true` (no JS access), `samesite = Lax` (CSRF protection)
- Lifetime: 3600 seconds (1 hour)
- Strict mode enabled

**`login.php`** on POST:
1. Queries `users` table for the username
2. Runs `password_verify()` against the stored bcrypt hash
3. On success: calls `session_regenerate_id(true)` to rotate the session ID
4. Sets `$_SESSION['loggedIn'] = true`, `$_SESSION['user_id']`, `$_SESSION['full_name']`

**`logout.php`**: calls `session_unset()` then `session_destroy()` then redirects to login.

### SQL Injection Prevention

Every database query that takes user input **must** use a prepared statement:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
```
The `?` placeholder is handled by MySQLi's `bind_param()` — the database driver sanitizes the value before it ever reaches SQL. No raw string interpolation is used for user inputs.

### Key Backend Flows

#### Incident Submission (`incidents.php`)
1. User POSTs form data
2. Server validates all required fields
3. `bind_param` with 18+ fields inserts into `incidents` — `user_id` from session
4. `$success_id = $conn->insert_id` captures the new record's ID
5. Page renders a success modal with the Reference ID
6. OK button redirects to `my-incidents.php`

#### Adoption Request (`adoption-request.php`)
1. Animal ID from GET param → query confirms animal exists and is `Available for Adoption`
2. User POSTs 22 application fields
3. INSERT into `adoptions` with `user_id` from session
4. Immediately runs: `UPDATE animals SET status = 'Reserved' WHERE id = $animalId`
5. This makes the animal's card show "Reserved" with a disabled button for all users

#### My Reports / My Adoption Requests (user-scoping)
- All queries filter by `user_id = $_SESSION['user_id']` — users only ever see their own data
- No admin bypass is implemented in this version

#### Password Hashing
```php
// Registration
$hashed = password_hash($password, PASSWORD_DEFAULT); // bcrypt

// Login verification
if (password_verify($input_password, $hashed_from_db)) { ... }
```
`PASSWORD_DEFAULT` currently resolves to bcrypt with cost factor 10.

---

### Backend Keypoints for Defense

- **Prepared statements on every user-input query** — prevents SQL injection at the driver level
- **Session regeneration on login** — prevents session fixation attacks
- **User-scoped queries** — `user_id` from session filters every dashboard, report, and adoption query
- **Animal auto-reservation** — after adoption form submit, a single UPDATE prevents duplicate requests without any manual intervention

---

### Backend Manuscript

> "The backend is PHP 8.3 with MySQLi — no framework, which gives us direct visibility into every request-response cycle. Authentication uses `password_hash` with bcrypt and `session_regenerate_id` on every login to prevent session fixation. All queries that accept user input are written as prepared statements using `bind_param`, which means user data is never concatenated into SQL strings — the database driver handles escaping at the protocol level. When a user submits an adoption request, the backend runs two queries atomically: an INSERT into the adoptions table and an UPDATE on the animal's status to Reserved, which instantly prevents any other user from submitting a duplicate request."

---

## SECTION 3: DATABASE

### How It Works

The database is **MySQL 8** with the schema in `sql/marikina_db.sql`. Five core tables with foreign key relationships.

### Tables & Relationships

```
users ──────────────────────┐
  id (PK)                   │
  username (UNIQUE)         │
  password                  │
  full_name                 │
  role                      │
                            │
incidents ──────────────────┤  (user_id FK → users.id)
  id (PK)                   │
  user_id (FK)  ────────────┘
  incident_date, time
  location, barangay
  animal_type, color, size
  victim_name, age, contact
  injury_description
  severity_level (ENUM: Low/Medium/High/Critical)
  status (ENUM: New/Under Review/Resolved/Closed)
  treatment_received, remarks
  created_at, updated_at

adoptions ──────────────────┐
  id (PK)                   │
  user_id (FK)  ────────────┘  (nullable — legacy rows)
  animal_id (FK → animals.id)
  animal_name, animal_type
  applicant_name, email, phone
  address, city, postal_code
  employment, home_type, home_ownership
  rental_permission, have_yard
  other_pets_info, has_children, children_ages
  adoption_reason
  reference1_name/phone, reference2_name/phone
  status (ENUM: Pending/Approved/Rejected/Completed)
  request_date

animals
  id (PK)
  name, type, age, gender
  status (ENUM: In Shelter / Available for Adoption / Reserved / Adopted / Rescued / Deceased)
  intake_date, description

vaccinations
  id (PK)
  animal_name
  vaccine_type, schedule_date, vet_staff
  status (ENUM: Upcoming/Done/Overdue)
```

### ENUM Fields — Why ENUM?

ENUM enforces a fixed set of allowed values at the database level. If application code tries to INSERT an invalid status, MySQL rejects it — this is a second layer of validation beyond PHP.

### The `Reserved` Status Flow

```
Animal: Available for Adoption
  → User views animal, clicks "View Details" (animal-detail.php)
  → User fills adoption form, clicks Submit (adoption-request.php POST)
  → Backend: INSERT into adoptions
  → Backend: UPDATE animals SET status = 'Reserved'
  → Animal card now shows orange "Reserved" badge, Adopt button disabled
  → adopt-animal.php and animal-detail.php both block non-Available animals
```

### Why `user_id` in Both `incidents` and `adoptions`?

Every query on "My Reports" and "My Adoption Requests" filters by `user_id = $_SESSION['user_id']`. Without this column, we would have to match by `applicant_name` or `victim_name` — which is unreliable if names differ by spacing or capitalization. Storing the integer `user_id` makes user-scoped queries fast and exact.

### Indexes

- `incidents.incident_date` — indexed for date-range queries
- `incidents.severity_level` — indexed for severity filtering
- `users.username` — unique constraint acts as an index

---

### Database Keypoints for Defense

- **5 tables, 3 with FK relationships** — normalized, no data redundancy
- **ENUM types** enforce valid status values at the DB level — application can't insert garbage
- **`Reserved` status** is the key deduplication mechanism for the entire adoption workflow
- **`user_id` FK on incidents and adoptions** enables all user-scoped dashboard queries

---

### Database Manuscript

> "The database has five tables: users, animals, adoptions, incidents, and vaccinations. The adoptions and incidents tables both carry a `user_id` foreign key referencing the users table, which is how we scope every dashboard query to the logged-in user without relying on matching names. Status fields use MySQL ENUM types — this enforces valid values at the database level as a second layer after PHP validation. The key design decision is the `Reserved` animal status: when an adoption request is submitted, the backend immediately updates the animal's status to Reserved, which propagates to the browse page and detail page, preventing any duplicate applications across all users."

---

## SECTION 4: DEBUG Q&A

> These are the most likely technical questions during defense. **2–3 sentences only. Precise and accurate.**

---

### Authentication & Sessions

**Q: What happens if someone manually types a URL to access dashboard.php without logging in?**
> Every protected page calls `include 'session-handler.php'` then checks `$_SESSION['loggedIn'] !== true`. If the condition fails, `header("Location: login.php")` is called immediately followed by `exit()`. The page content never renders.

**Q: What is session fixation and how do you prevent it?**
> Session fixation is when an attacker pre-sets a known session ID before the victim logs in, then hijacks the session after authentication. We call `session_regenerate_id(true)` immediately after verifying the password, which issues a brand new session ID and destroys the old one.

**Q: Why use `password_verify()` instead of comparing hashed strings directly?**
> `password_hash()` generates a different hash each time even for the same password because it embeds a unique salt. Direct comparison would always fail — `password_verify()` extracts the salt from the stored hash and re-hashes the input for comparison.

---

### SQL & Database

**Q: What is SQL injection and show how you prevent it?**
> SQL injection is when user input contains SQL syntax that modifies the intended query — for example, `' OR 1=1 --`. We use `$stmt = $conn->prepare("SELECT ... WHERE username = ?")` with `bind_param("s", $username)` — the `?` placeholder is sent separately from the SQL string, so the driver treats all user input as data, never as SQL syntax.

**Q: Why store `animal_name` in the adoptions table instead of just `animal_id`?**
> `animal_id` is stored as the foreign key for relational integrity. `animal_name` is also stored as a denormalized convenience column so that if an animal record is deleted, historical adoption records still show the name without a JOIN.

**Q: What does your `updated_at` column in incidents do?**
> It stores a timestamp that MySQL automatically updates to the current time whenever that row is modified, using `ON UPDATE CURRENT_TIMESTAMP`. This lets staff see when an incident's status was last changed without a separate audit table.

**Q: Why use `user_id` in the adoptions table instead of matching by `applicant_name`?**
> Matching by name is unreliable — a name could have extra spaces, different capitalization, or be shared between two users. `user_id` is an integer primary key that uniquely and exactly identifies the submitting user, making user-scoped queries both accurate and fast.

**Q: What is a prepared statement's `bind_param` type string?**
> The type string maps each `?` placeholder to a PHP type: `s` for string, `i` for integer, `d` for double, `b` for blob. For example `"iisss"` means the first two parameters are integers and the next three are strings. If the count or types don't match the number of `?` in the query, MySQLi returns a binding error.

---

### Adoption Workflow

**Q: What happens if two users submit an adoption request for the same animal at the exact same time?**
> The first INSERT to complete will trigger the `UPDATE animals SET status = 'Reserved'` query. The second user's form will still submit, but when they reload the page, the animal will show as Reserved. In a production system this would be handled with a database transaction and a unique constraint, but for this scope it is an identified limitation.

**Q: How does the system know an animal is no longer available after a request is submitted?**
> After a successful INSERT into `adoptions`, the backend immediately runs `UPDATE animals SET status = 'Reserved' WHERE id = $animalId`. The `adopt-animal.php` page queries `animals` with `status IN ('Available for Adoption', 'Reserved', 'Adopted')` and renders Reserved cards with a disabled button. `animal-detail.php` redirects away if status is not exactly `'Available for Adoption'`.

---

### Security

**Q: What is an HTTP-only cookie and why does it matter?**
> An HTTP-only cookie cannot be accessed by JavaScript — `document.cookie` will not return it. This prevents XSS attacks from stealing the session token even if malicious script is injected into the page.

**Q: What is SameSite=Lax on session cookies?**
> SameSite=Lax means the session cookie is only sent on same-site requests and top-level cross-site GET navigations — it is not sent on cross-site POST requests. This mitigates CSRF attacks where a malicious site tries to submit forms on behalf of an authenticated user.

**Q: How are passwords stored in the database?**
> Passwords are never stored in plaintext. `password_hash($password, PASSWORD_DEFAULT)` in PHP generates a bcrypt hash with a random salt embedded in the output string, typically 60 characters long starting with `$2y$`. Even if the database is compromised, the hashes cannot be reversed without brute force.

---

### PHP & Architecture

**Q: Why does each PHP page call `$conn->close()` before the HTML output?**
> The database connection is only needed to fetch data. Closing it before rendering HTML releases the MySQL connection back to the pool immediately, freeing server resources. All data needed for the HTML is already stored in PHP variables by the time the connection closes.

**Q: What is the purpose of `session-handler.php`?**
> It sets PHP session configuration options — like cookie security flags and lifetime — using `ini_set()` and `session_set_cookie_params()` before `session_start()` is called. These settings cannot be changed after the session starts, so this file must always be included first on every page.

**Q: Why is `exit()` called immediately after `header("Location: ...")`?**
> `header()` only queues a redirect — it does not stop PHP from continuing to execute the rest of the page. Without `exit()`, the entire page including protected content would still run and potentially be rendered or processed before the browser receives the redirect header.

---

*Prepared for Capstone 1 Defense — February 2026*

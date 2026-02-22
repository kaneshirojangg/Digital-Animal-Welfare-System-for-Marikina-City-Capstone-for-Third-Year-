# REMAINING MODULES DOCUMENTATION

## Animals.php - Animal Management Module

### Purpose
List all animals in the shelter and add new animals to the system.

### Key Functions
- Display all animals: `SELECT * FROM animals ORDER BY intake_date DESC`
- Add new animal: `INSERT INTO animals (name, type, age, gender, status, description)`
- Form validation: Check name and type are not empty
- Success/error messaging

### Database Tables
- **animals** table: id, name, type, age, gender, status, description, intake_date

### User Flow
1. Logged-in user visits animals.php
2. Sees list of all animals in database
3. Can add new animal using form (hidden by default)
4. Click "Add New Animal" to show form
5. Fill form and submit
6. New animal added to database
7. Page refreshes with new entry

### Key Variables
- `$success` - Success message after adding animal
- `$error` - Error description if validation fails
- `$result` - Query result containing all animals

---

## Adoptions.php - Adoption Request Management

### Purpose
Manage all adoption requests and view their statuses.

### Key Functions
- Add new adoption request: `INSERT INTO adoptions (animal_name, animal_type, applicant_name...)`
- List all adoptions: `SELECT * FROM adoptions ORDER BY request_date DESC`
- Form fields: animal_name, animal_type, applicant_name, applicant_contact, notes
- Status tracking: Pending, Approved, Completed

### Database Tables
- **adoptions** table: id, animal_name, animal_type, applicant_name, applicant_contact, status, request_date, notes

### UI Components
- Collapsible add form
- Table showing adoption requests
- Status indicators
- Date formatting

---

## Adoption-Request.php - Adoption Application Form

### Purpose
Detailed adoption application form for users to apply for specific animals.

### Key Functions
- Get animal details: `SELECT * FROM animals WHERE id = ? AND status = 'Available'`
- Collect detailed applicant information
- Validate all required fields
- Insert adoption request: `INSERT INTO adoptions (animal_id, applicant_name, email, phone, address...)`

### Form Fields (Detailed)
- Applicant Name, Email, Phone, Address
- City, Postal Code
- Employment
- Home Type, Home Ownership
- Rental Permission (checkbox)
- Have Yard (yes/no/maybe)
- Other Pets
- Children (yes/no, ages)
- Why Adopt (text area)
- References (2 reference contacts)

### Database Tables
- **animals** table - read only to get details
- **adoptions** table - insert with all application data

### Success Flow
1. Animal selected from adopt-animal.php
2. User redirected with ?animal_id=X
3. Animal details fetched and displayed
4. Form filled by user
5. Server validates all fields
6. Data inserted into adoptions table
7. Status set to 'Pending'
8. Success message shown with redirect option

---

## Animal-Detail.php - Individual Animal Profile

### Purpose
Display comprehensive details for a specific animal including vaccinations.

### Key Functions
- Get animal by ID: `SELECT * FROM animals WHERE id = ? AND status = 'Available'`
- Get vaccinations: `SELECT * FROM vaccinations WHERE animal_id = ? ORDER BY vaccination_date DESC`
- Display hero section with animal info
- Show vaccination history
- Link to adoption application

### Display Sections
1. **Hero Section:** Animal name, type, age, gender (gradient background)
2. **Details Section:** Breed, color, microchip, weight, description
3. **Vaccination History:** List of all vaccines, dates, veterinarian
4. **Adoption Button:** Links to adoption-request.php?animal_id=X

### Database Tables
- **animals** table
- **vaccinations** table

---

## Incidents.php - Incident Reporting System

### Purpose
Report stray animal incidents (bites, attacks, etc) with comprehensive details.

### Key Functions
- Validate incident date (not in future)
- Validate severity level (Low/Medium/High/Critical)
- Store all incident details
- Display user's incident history
- Prepared statement: `INSERT INTO incidents (user_id, incident_date, incident_time, location...)`

### Form Fields
- Incident Date & Time
- Location & Barangay
- Animal Type, Color, Size, Features
- Victim Name, Age, Contact
- Injury Description
- Severity Level (dropdown)
- Treatment Received
- Remarks

### Database Tables
- **incidents** table: Full incident record with 16 fields
- Records include: User ID, dates, location, animal info, victim info, injury details, severity

### Validation
- Date must not be future
- All required fields must be filled
- Severity must be one of 4 valid values
- Reference ID generated from insert_id

---

## Vaccinations.php - Vaccination Records (Read-Only)

### Purpose
View vaccination schedules for adopted animals (read-only for users).

### Key Functions
- Query vaccines for user's adopted animals only
- `SELECT v.* FROM vaccinations v INNER JOIN adoptions a ON v.animal_name = a.animal_name WHERE a.applicant_name = ?`
- Display in formatted table
- Show status: Scheduled, Done, Pending

### Display Columns
- Animal Name
- Vaccine Type
- Schedule Date
- Veterinary Staff
- Status

### User Access
- Can only view their own animals' vaccinations
- Cannot add or edit entries
- View-only interface

### Database Tables
- **vaccinations** table (read only via JOIN to adoptions)
- **adoptions** table (for filtering by user)

---

## Schedule.php - Appointment Calendar

### Purpose
Calendar view of user's vaccination schedules and appointments using FullCalendar library.

### Key Features
- Interactive calendar display
- Color-coded events (Green = Scheduled, Red = Cancelled, Orange = Overdue)
- Event details in popups
- Add appointment form
- Uses FullCalendar v6.1.15 library

### Calendar Event Flow
1. Fetch vaccinations for user's adopted animals
2. Convert to FullCalendar format: {title, start, color, extendedProps}
3. Pass as JSON to FullCalendar
4. Display interactive calendar
5. Click event to see details

### Add Appointment
- Title (animal name)
- Description
- Date & Time
- Type (General, Follow-up, etc)
- Inserted into vaccinations table

### Libraries
- **FullCalendar:** https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/

---

## Reports.php - System Reports

### Purpose
Generate reports on animal welfare statistics (layout ready, data queries to be implemented).

### Current State
- Framework/layout prepared
- No database queries connected yet
- Placeholder for future report generation

### Planned Reports
- Animal statistics by type
- Adoption statistics
- Incident summaries
- Vaccination compliance
- Date range filtering

### Elements Present
- Page header and styling
- Report type buttons
- Action bar with export options
- Table placeholder layout
- CSS for report styling

---

## Logout.php - Session Termination

### Purpose
Safely destroy user session and return to login page.

### Process
1. Check if user is logged in
2. Unset all session variables: `session_unset()`
3. Destroy session: `session_destroy()`
4. Clear cookies if using cookies: `setcookie(session_name(), '', time() - 42000, ...)`
5. Redirect to login.php with ?loggedout=1 parameter

### Security Features
- Destroys session data completely
- Clears cookies from browser
- Removes session file from server
- Prevents session fixation/hijacking

---

## Session-Handler.php - Session Configuration

### Purpose
Centralized session configuration for all pages.

### Configuration
```php
session_set_cookie_params([
    'httponly' => true,   // Prevents JavaScript access
    'secure' => false,    // Set to true for HTTPS
    'samesite' => 'Lax',  // CSRF protection
    'lifetime' => 86400   // 24 hours
]);
session_name('MARIKINA_AUTH');  // Cookie name
```

### Function
- **Included** at top of every page
- **Followed** by `session_start()` call
- Sets secure cookie parameters
- Names session cookie for identification

### Security Measures
- **httponly:** JavaScript cannot access session cookie
- **samesite=Lax:** Cookies sent with same-site requests
- **secure:** False for HTTP (set true for HTTPS in production)
- **lifetime:** Session expires after inactivity

---

## Nav-Menu.php - Navigation Sidebar

### Purpose
Reusable navigation component included in all pages.

### Features
- Logo/brand name
- Links to all main features
- Active page highlighting
- User profile section
- Logout link
- Responsive sidebar (collapses on mobile)

### Links Provided
- Dashboard
- Animals
- Adoptions
- Incidents
- Vaccinations
- Schedule
- Reports
- Logout

### Styling
- Fixed position left sidebar (280px width)
- Green color theme matching others
- Icons for each section
- Hover effects for interaction

### Active Page
- Uses `$activePage` variable set in each page
- Highlights current page in navigation
- Helps user understand location in system

---

## Page-Template.php - Layout Template

### Purpose
Standard page layout/wrapper for consistent UI structure.

### Sections
- Head section (meta tags, stylesheets)
- Navigation include
- Main content area with max-width
- Footer section
- Common CSS variables
- Responsive breakpoints

### Usage
- Can be included/extended for consistent layout
- Or pattern can be followed in each page

---

## File Sizes & Line Counts

| File | Lines | Size | Purpose |
|------|-------|------|---------|
| index.php | 219 | 8.2 KB | Landing page |
| login.php | 215 | 7.9 KB | Authentication |
| register.php | 177 | 5.8 KB | Account creation |
| dashboard.php | 687 | 26 KB | Main hub |
| animals.php | 212 | 8.1 KB | Animal management |
| adoptions.php | 120 | 4.5 KB | Adoption list |
| adoption-request.php | 591 | 22 KB | Adoption form |
| animal-detail.php | 439 | 16.5 KB | Animal profile |
| incidents.php | 616 | 23 KB | Incident reports |
| vaccinations.php | 257 | 9.8 KB | Vaccines view |
| schedule.php | 399 | 15 KB | Calendar |
| reports.php | 194 | 7.2 KB | Reports |
| adopt-animal.php | 242 | 9.1 KB | Animal browsing |
| logout.php | 25 | 0.9 KB | Logout |
| session-handler.php | 16 | 0.6 KB | Session config |
| nav-menu.php | 200+ | 7+ KB | Navigation |
| page-template.php | 100+ | 3+ KB | Template |

**Total:** ~5,000 lines, ~140+ KB of PHP code


# DASHBOARD.PHP - Main System Dashboard

## Overview
The main hub after login showing user statistics, adoption requests, vaccination schedules, and featured animals. Acts as the central control panel for all system features.

## Purpose
- Display user's adoption statistics
- Show recent adoption requests
- Display featured animals available for adoption
- Provide quick access to all system features
- Present vaccination schedules for adopted animals

## Database Connection
**Connection Details:**
```
Host: localhost
User: marikina_user
Password: marikina_password
Database: marikina_db
```

**Tables Used:**
- `adoptions` - Adoption requests with status tracking
- `animals` - Animal records filtered by availability
- `vaccinations` - Vaccination schedules for adopted animals
- `sessions` - Session management (implicit via session-handler.php)

## Key Features

### 1. Session Protection
```php
include 'session-handler.php';
session_start();

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}
```
- Checks if user is logged in
- Redirects to login if not authenticated
- Prevents unauthorized access

### 2. User Statistics
**Pending Adoptions:**
```php
$pending = $conn->query("SELECT COUNT(*) as count FROM adoptions 
                        WHERE applicant_name = '$full_name' 
                        AND status = 'Pending'")
                ->fetch_assoc()['count'];
```

**Approved Adoptions:**
```php
$approved = $conn->query("SELECT COUNT(*) as count FROM adoptions 
                         WHERE applicant_name = '$full_name' 
                         AND status = 'Approved'")
                ->fetch_assoc()['count'];
```

**Completed Adoptions:**
```php
$completed = $conn->query("SELECT COUNT(*) as count FROM adoptions 
                          WHERE applicant_name = '$full_name' 
                          AND status = 'Completed'")
                ->fetch_assoc()['count'];
```

### 3. Recent Adoption Requests
```php
$user_requests = $conn->query("SELECT animal_name, animal_type, status, request_date 
                              FROM adoptions 
                              WHERE applicant_name = '$full_name' 
                              ORDER BY request_date DESC 
                              LIMIT 5");
```
- Shows last 5 adoption requests
- Displays status and date
- Sorted by most recent first

### 4. Featured Animals
```php
$featured_animals = $conn->query("SELECT id, name, type, age, gender, description 
                                 FROM animals 
                                 WHERE status = 'Available for Adoption' 
                                 ORDER BY intake_date DESC 
                                 LIMIT 6");
```
- Shows 6 most recently added animals
- Only animals available for adoption
- Used for quick browsing

### 5. Vaccination Schedules
```php
$vaccination_schedules = $conn->query("
    SELECT 
        v.id, v.animal_name, v.vaccine_type, v.schedule_date, v.vet_staff, v.status
    FROM vaccinations v
    INNER JOIN adoptions a ON v.animal_name = a.animal_name
    WHERE a.applicant_name = '$full_name' 
    AND a.status = 'Completed'
    AND v.schedule_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY v.schedule_date ASC
    LIMIT 8
");
```
- Shows vaccinations for completed adoptions
- Only shows future/recent schedules (last 7 days + future)
- Sorted by date

## UI Components

### 1. Page Header
- User greeting: "Welcome, [user's full name]"
- Subtitle describing dashboard purpose

### 2. Statistics Cards Grid
- **Pending Adoptions:** Number + icon
- **Approved Adoptions:** Number + icon
- **Completed Adoptions:** Number + icon
- Each card has hover effects

### 3. Recent Requests Section
- Table showing:
  - Animal name and type
  - Status (Pending, Approved, Completed)
  - Request date and time
- Clicking row may show details

### 4. Featured Animals Section
- Grid layout of animal cards
- Each card shows: name, type, age, gender, icon
- Quick "View" or "Adopt" button

### 5. Vaccination Schedule Section
- List of upcoming/recent vaccinations
- Shows animal name, vaccine type, date
- Status indicators

## Data Flow
```
User logs in → Redirected to dashboard.php
↓
Session checked ✓
↓
Fetch user's statistics (pending, approved, completed counts)
↓
Fetch recent adoption requests (user's last 5)
↓
Fetch featured animals (6 most recent available)
↓
Fetch vaccination schedules (upcoming for adopted animals)
↓
Close database connection
↓
Render dashboard.php with all data
↓
Display styled HTML with stats, requests, animals, schedules
```

## Styling Details

### CSS Variables (Color Theme)
```css
--primary: #2c7d4e;           (Green)
--primary-dark: #1e5c38;      (Dark Green)
--danger: #dc2626;            (Red)
--warning: #f59e0b;           (Orange/Yellow)
--success: #10b981;           (Light Green)
--info: #3b82f6;              (Blue)
--text: #2d3748;              (Dark Text)
--text-light: #4b5563;        (Light Text)
--bg: #f8fafc;                (Very Light Blue)
--card-bg: #ffffff;           (White)
--border: #e2e8f0;            (Light Border)
```

### Layout
- **Sidebar Left:** 280px margin (navigation menu)
- **Main Content:** Responsive with max-width 1300px
- **Grid System:** Auto-fit columns for responsive cards
- **Shadow Effects:** 0 4px 12px rgba(0,0,0,0.05)

### Cards
- White background
- Rounded corners: 12px
- Border: 1px solid var(--border)
- Hover effect: Translate Y(-2px), shadow increase

## Navigation
- Includes `nav-menu.php` for sidebar navigation
- Active page indicator: `'dashboard'`
- Links to other major features

## Security & Validation

### Session Check
```php
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit();
}
```
- Prevents unauthorized access
- Redirects to login if session invalid

### Database Queries
- Uses `$_SESSION['user_id']` and `$_SESSION['full_name']`
- Properly constructed SQL
- Note: Some queries use string concatenation (potential SQLi concern)

## Related Pages & Navigation
- **Includes:** 
  - `session-handler.php` (session config)
  - `nav-menu.php` (navigation sidebar)
- **Links To:**
  - `animals.php` (animal list)
  - `adoptions.php` (adoption management)
  - `vaccinations.php` (vaccination records)
  - `incidents.php` (incident reports)
  - `schedule.php` (calendar)

## Common Scenarios

### Scenario 1: First-Time Login
1. User successfully logs in from login.php
2. Session created with user info
3. Dashboard displayed with:
   - Pending/Approved/Completed counts: All 0 initially
   - No recent requests
   - Featured animals displayed
   - No vaccination schedules yet

### Scenario 2: User with Active Adoption
1. User has pending adoption request
2. Dashboard shows:
   - Pending count: 1
   - Recent requests: Shows the adoption
   - Vaccination schedules: Empty (until adoption completed)

### Scenario 3: User with Completed Adoption
1. User has completed adoption
2. Dashboard shows:
   - Completed count: 1
   - Vaccination schedules: Shows upcoming vaccines for adopted animal

## Performance Considerations

### Database Queries (5 total)
1. Pending adoptions count
2. Approved adoptions count
3. Completed adoptions count
4. Recent adoption requests (5 results)
5. Featured animals (6 results)
6. Vaccination schedules (8 results)

**Optimization Tip:** Could combine counts into single query using GROUP BY

## Error Handling & Edge Cases

### If database connection fails:
```php
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

### If user has no adoption requests:
- Recent requests section shows empty
- Featured animals still display
- Encourages user to browse animals

### If user has no completed adoptions:
- Vaccination schedules section shows empty
- User directed to adoption page

## Defense/Capstone Points
- **System Design Demonstrated:**
  - Centralized dashboard concept
  - Multiple data aggregation
  - Session-based user personalization

- **Technical Skills:**
  - Multiple database queries
  - Conditional data display
  - Responsive CSS grid layout
  - Session management

- **User Experience:**
  - Quick statistics overview
  - Action-oriented layout
  - Intuitive navigation

## Notes for Defense
- **Key Learning:** Dashboard is hub of entire system
- **Challenge:** Aggregating and displaying multiple data sources
- **Solution:** Separate database queries for each statistic
- **Improvement:** Could use database views or JOIN operations for better performance
- **Security Note:** Some queries could be improved with prepared statements

## Technical Stack
- **PHP**: Session handling, database queries, data processing
- **MySQL**: User adoptions, animals, vaccinations data
- **HTML**: Semantic structure
- **CSS**: Grid layout, responsive design, animations
- **Font:** Playfair Display (headings), Inter (body)

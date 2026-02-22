# Marikina City Animal & Welfare Management System

## Table of Contents

- [Project Description](#project-description)
- [System Overview](#system-overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [System Requirements](#system-requirements)
- [Installation & Setup](#installation--setup)
- [Getting Started](#getting-started)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Security Features](#security-features)
- [Configuration](#configuration)
- [Testing](#testing)
- [Common Troubleshooting](#common-troubleshooting)
- [Development Notes](#development-notes)
- [Support & Documentation](#support--documentation)
- [Version Information](#version-information)

---

## Project Description

The Marikina City Animal & Welfare Management System is a comprehensive web-based platform designed to streamline the management of animal welfare operations for the Marikina City government. This system centralizes critical functions including animal record management, adoption request processing, vaccination scheduling, incident reporting, and administrative reporting.

The application serves as a unified solution to address operational challenges such as fragmented records management, delayed incident response, and data inaccuracy. By providing an organized database and intuitive interface, the system enables staff to efficiently manage animal welfare activities and maintain comprehensive records for compliance and decision-making purposes.

---

## System Overview

This is a three-tier web application built with PHP and MySQL, designed for use by veterinarians and administrative staff. The system provides secure authentication, role-based access control, and comprehensive data management capabilities for animal welfare operations.

**Current Status**: Production Ready | All Core Features Implemented | Fully Functional

---

## Key Features

### Authentication & Access Control
- Secure user login with server-side authentication
- User registration for veterinarians and staff
- Session-based access control with HTTP-only cookies
- Automatic session regeneration upon login for enhanced security
- Password hashing using bcrypt (PASSWORD_DEFAULT)

### Animal Management
- Comprehensive animal record database with detailed information
- Animal availability status tracking
- Support for multiple animal types and demographics
- Real-time animal listing and search capabilities
- Individual animal detail pages with complete history

### Adoption Management System
- Complete adoption request workflow and tracking
- Multi-stage adoption approval process (Pending, Approved, Completed)
- Applicant information collection and verification
- Adoption request history and status monitoring
- Employment verification for applicants

### Health & Vaccination Services
- Vaccination record management for all animals
- Vaccination schedule tracking and reminders
- Vaccine type and date documentation
- Integration with animal records

### Incident Management
- Incident reporting and documentation system
- Location-based incident tracking
- Injury and incident description recording
- Priority and status management

### Reporting & Analytics
- System report generation capabilities
- Event scheduling and calendar management
- Statistical analysis of adoption trends
- Administrative dashboard with key metrics

### Dashboard & Navigation
- Centralized dashboard displaying user statistics
- Quick-access navigation menu
- Real-time adoption status counters
- Recent activity feeds
- Featured animals display

---

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3
- **Authentication**: Session-based with prepared statements
- **Security**: SQL injection prevention, password hashing, HTTP-only cookies

---

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, or PHP built-in server)
- 50 MB disk space minimum
- Command-line access (for initial setup)

---

## Installation & Setup

### Prerequisites

Before installation, ensure you have:
- MySQL server running and accessible
- PHP installed with command-line access
- Git (for cloning the repository)

### Step 1: Clone or Download Repository

```bash
git clone <repository-url>
cd "3rd Year Capstone"
```

### Step 2: Set Up Database

Create the database and user account:

```bash
sudo mysql -u root -e "
  CREATE DATABASE IF NOT EXISTS marikina_db;
  CREATE USER IF NOT EXISTS 'marikina_user'@'localhost' IDENTIFIED BY 'marikina_password';
  GRANT ALL PRIVILEGES ON marikina_db.* TO 'marikina_user'@'localhost';
  FLUSH PRIVILEGES;
"
```

Import the database schema and seed data:

```bash
mysql -u marikina_user -p'marikina_password' marikina_db < sql/marikina_db.sql
```

### Step 3: Start the Application

Using PHP built-in server:

```bash
cd /path/to/project
php -S localhost:8000
```

The application will be available at: `http://localhost:8000`

---

## Getting Started

### Initial Login

1. Open your web browser and navigate to `http://localhost:8000`
2. Click the "Log In" button to access the login page
3. Use the following test credentials:
   - **Username**: `test`
   - **Password**: `password`

### Navigating the System

After logging in, you will be directed to the main dashboard. The left navigation menu provides access to:

- **Dashboard**: Overview of adoption statistics and recent activity
- **Animals**: View and manage all animals in the system
- **Adoptions**: Process and track adoption requests
- **Vaccinations**: Manage vaccination records and schedules
- **Incidents**: Report and review incident documentation
- **Reports**: Generate system reports and analytics
- **Schedule**: View and manage appointments and events

---

## Project Structure

```
3rd Year Capstone/
├── index.php                    # Root entry point (redirects to src/index.php)
├── startup.sh                   # Startup script for system verification
├── README.md                    # This file
│
├── src/                         # Main application files
│   ├── index.php               # Public landing page
│   ├── login.php               # User authentication
│   ├── register.php            # User registration
│   ├── logout.php              # Session termination
│   ├── session-handler.php     # Session configuration
│   │
│   ├── dashboard.php           # Main dashboard hub
│   ├── animals.php             # Animal management
│   ├── animal-detail.php       # Individual animal details
│   ├── adopt-animal.php        # Animal adoption browsing
│   ├── adoption-request.php    # Adoption application form
│   ├── adoptions.php           # Adoption request management
│   ├── vaccinations.php        # Vaccination record management
│   ├── incidents.php           # Incident reporting system
│   ├── reports.php             # Reporting module
│   ├── schedule.php            # Event scheduling
│   │
│   ├── nav-menu.php            # Navigation component
│   └── page-template.php       # Page layout template
│
├── sql/                         # Database files
│   ├── marikina_db.sql         # Database schema and seed data
│   ├── add-incidents-table.sql # Additional incident table schema
│   └── update-animals.sql      # Animal table updates
│
├── assets/                      # Static files
│   ├── css/
│   │   └── dashboard-ui.css    # Application styling
│   └── images/                 # Image assets
│
└── docs/                        # Documentation
    ├── 00-QUICK-REFERENCE.md   # Quick reference guide
    ├── 01-index.md             # Landing page documentation
    ├── 02-login.md             # Authentication documentation
    ├── 03-register.md          # Registration documentation
    ├── 04-dashboard.md         # Dashboard documentation
    ├── 05-REMAINING-MODULES.md # Module documentation
    └── DEBUGGING-GUIDE.md      # Troubleshooting guide
```

---

## Database Schema

### Core Tables

**users**
- Stores user account information and credentials
- Fields: id, username, password (hashed), full_name, role
- Primary authentication source

**animals**
- Contains all animal records
- Fields: id, name, type, age, gender, status, date_entered, description
- Used throughout adoption and health management

**adoptions**
- Tracks adoption applications and requests
- Fields: id, animal_id, applicant_name, email, phone, status, request_date
- Links animals to applicant information

**vaccinations**
- Records vaccination history and schedules
- Fields: id, animal_name, vaccine_type, schedule_date, date_given
- Manages health records for animals

**incidents**
- Documents animal and facility incidents
- Fields: id, user_id, incident_date, location, injury_description, status
- Provides incident tracking and response

---

## Security Features

- **Query Parameterization**: All database queries use prepared statements to prevent SQL injection attacks
- **Password Security**: User passwords are hashed using PHP's PASSWORD_DEFAULT algorithm (bcrypt)
- **Session Management**: HTTP-only cookies prevent JavaScript access to session tokens
- **Session Regeneration**: New session IDs generated on login to prevent session fixation attacks
- **SameSite Cookie Policy**: Cookies configured with Lax SameSite policy for CSRF protection
- **Authentication Checks**: All protected pages verify session status before content display
- **Input Validation**: Server-side validation of all form submissions

---

## Configuration

### Database Credentials

Default connection parameters (found in application files):

```
Host: localhost
Database: marikina_db
User: marikina_user
Password: marikina_password
```

**Note**: These credentials should be modified in production environments.

### Color Scheme

The application uses a cohesive color palette:

| Color | Hex Value | Usage |
|-------|-----------|-------|
| Primary Green | #2c7d4e | Headers, buttons, brand |
| Dark Green | #1e5c38 | Active states |
| Accent Orange | #e67e22 | Highlights, links |
| Danger Red | #dc2626 | Errors, warnings |
| Success Green | #10b981 | Confirmations |
| Light Background | #f8fafc | Page backgrounds |
| White | #ffffff | Cards, panels |

---

## Testing

### Verification Steps

After installation, follow these steps to verify system functionality:

1. Navigate to `http://localhost:8000`
2. Review the landing page (public content)
3. Click "Log In" and proceed to login page
4. Enter test credentials (username: `test`, password: `password`)
5. Verify successful login and dashboard display
6. Test navigation through all menu sections
7. Verify session persistence across different pages
8. Test logout functionality

### Known Test Cases

- User authentication with valid credentials
- Rejection of invalid login attempts
- Session persistence across page navigation
- Access denial for non-authenticated users
- Proper error messages for database issues

---

## Common Troubleshooting

### Database Connection Errors

**Problem**: "Connection failed" message on login page

**Solution**:
1. Verify MySQL server is running: `mysql -u root`
2. Confirm credentials are correct: `mysql -u marikina_user -p`
3. Check database exists: `SHOW DATABASES;`
4. Verify user permissions: `GRANT ALL PRIVILEGES...`

### SQL File Import Errors

**Problem**: "Access denied" when importing SQL file

**Solution**:
```bash
mysql -u marikina_user -p'marikina_password' marikina_db < sql/marikina_db.sql
```

### Session Timeout Issues

**Problem**: Users logged out unexpectedly when navigating between pages

**Solution**: Verify `session-handler.php` is included on all protected pages and session cookies are HTTP-only enabled.

### File Not Found Errors

**Problem**: 404 errors when accessing pages

**Solution**: Verify correct relative paths in redirects and includes. Ensure all PHP files are in the `src/` directory.

---

## Development Notes

### Adding New Modules

When adding new features or modules:

1. Create PHP file in `src/` directory
2. Include `session-handler.php` and verify session status
3. Use prepared statements for all database queries
4. Follow existing code structure and naming conventions
5. Add documentation to `docs/` folder
6. Test thoroughly with various user scenarios

### Code Standards

- Use prepared statements exclusively for database operations
- Implement session checks on all protected pages
- Apply consistent naming conventions (snake_case for variables)
- Include meaningful comments for complex logic
- Avoid direct SQL string concatenation

### Documentation

Each module should have a corresponding documentation file in the `docs/` folder describing:
- Purpose and functionality
- Database tables accessed
- User roles with access
- Common usage scenarios
- Integration points with other modules

---

## Support & Documentation

Detailed documentation for individual modules is provided in the `docs/` folder:

- [Landing Page](docs/01-index.md)
- [Authentication](docs/02-login.md)
- [Registration](docs/03-register.md)

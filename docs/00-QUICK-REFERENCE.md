# QUICK REFERENCE - All PHP Files

## Authentication & Session
- **login.php** - User login with session creation
- **register.php** - New veterinarian account creation
- **logout.php** - Session destruction and cleanup
- **session-handler.php** - Session configuration and initialization

## Main Dashboard & Features
- **dashboard.php** - Main hub showing statistics and recent activity
- **index.php** - Public landing page with system information

## Animal Management
- **animals.php** - List and manage all animals in shelter
- **adopt-animal.php** - Browse animals available for adoption
- **animal-detail.php** - View full details of specific animal

## Adoption System
- **adoptions.php** - Manage adoption requests
- **adoption-request.php** - Detailed adoption application form

## Health & Incidents
- **vaccinations.php** - View vaccination records for adopted animals
- **incidents.php** - Report and manage animal incidents
- **schedule.php** - Calendar view of appointments and vaccinations

## Utilities
- **reports.php** - Generate system reports
- **nav-menu.php** - Navigation sidebar component
- **page-template.php** - Page layout template

---

## Database Schema

### Tables
1. **users** - User accounts (id, username, password, full_name, role)
2. **animals** - Animal records (id, name, type, age, gender, status, etc)
3. **adoptions** - Adoption requests (id, animal_id, applicant_name, status, etc)
4. **vaccinations** - Vaccination records (id, animal_name, vaccine_type, schedule_date, etc)
5. **incidents** - Incident reports (id, user_id, incident_date, location, injury_description, etc)
6. **sessions** - Session management (id, user_id, session_token)

---

## Key Database Credentials
- **Host:** localhost
- **Database:** marikina_db
- **User:** marikina_user
- **Password:** marikina_password
- **Note:** Some files use 'root' - should be standardized

---

## Color Scheme
- **Primary Green:** #2c7d4e
- **Dark Green:** #1e5c38
- **Accent Orange:** #e67e22
- **Danger Red:** #dc2626
- **Success Green:** #10b981
- **Light Background:** #f8fafc
- **Card White:** #ffffff

---

## Important Security Notes
1. All login/register use prepared statements
2. Password hashing with PASSWORD_DEFAULT (bcrypt)
3. Session regeneration on login
4. HTTP-only cookies for session protection
5. Server-side form validation required

---

## Common Debug Patterns
- Use `echo`, `print_r()`, or `var_dump()` for variable inspection
- Check `$_SESSION` variables after login
- Verify database connection before queries
- Always check `$result->num_rows` before processing
- Use `mysqli_error()` for database errors

---

## Deployment Checklist
- [ ] Database credentials set correctly
- [ ] All tables created successfully
- [ ] Test user account exists
- [ ] HTTPS enabled (for secure cookies)
- [ ] Session timeout configured
- [ ] Error logging enabled
- [ ] Comments removed from all PHP files
- [ ] .md documentation in /docs folder


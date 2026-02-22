# LOGIN.PHP - User Authentication System

## Overview
Handles user authentication by verifying username and password against the database. Uses PHP prepared statements to prevent SQL injection and implements session management for security.

## Purpose
- Authenticate users securely
- Create user sessions upon successful login
- Display error messages for failed attempts
- Prevent unauthorized access to system features

## Database Connection
**Connection Details:**
```
Host: localhost
User: marikina_user
Password: marikina_password
Database: marikina_db
```

**Tables Used:**
- `users` table
  - `id` (Primary Key)
  - `username` (Unique)
  - `password` (Hashed with PASSWORD_DEFAULT)
  - `full_name`

## Key Features

### 1. Form Processing
**Method:** POST Request
**Form Fields:**
- `username` - User's login identifier
- `password` - User's password (plaintext input, hashed comparison in backend)
- `login` - Submit button identifier

### 2. Authentication Flow
```
User enters credentials → Form submission (POST)
↓
Validate username & password (not empty)
↓
Connect to database
↓
Prepare SQL statement: "SELECT id, password, full_name FROM users WHERE username = ?"
↓
Execute with parameter binding
↓
Compare hashed password with input using password_verify()
↓
Success: Create session variables
Failure: Show error message
```

### 3. Session Management
**On Successful Login:**
```php
$_SESSION['loggedIn'] = true;
$_SESSION['username'] = $username;
$_SESSION['user_id'] = $user['id'];
$_SESSION['full_name'] = $user['full_name'];
```

**Security Features:**
- Session ID regenerated after login: `session_regenerate_id(true)`
- HTTP-only cookies (configured in session-handler.php)
- Same-Site cookie policy

### 4. Error Handling
- Empty field validation
- Database connection error handling
- Incorrect password error
- Username not found error

## UI Components

### Form Layout
- **Logo Section**: "Marikina Animal & Welfare" title
- **Login Box**: Centered form with shadow
- **Input Fields**: Username and password with focus effects
- **Submit Button**: Full-width green button
- **Link to Register**: For new users

### Styling
- **Color Theme**: Green primary (#2c7d4e)
- **Background**: Gradient (light green to lighter green)
- **Card Style**: White box with rounded corners and shadow
- **Responsive**: Mobile-friendly layout

## Data Flow
```
User enters login.php
↓
Displays login form
↓
User submits credentials (POST)
↓
Server validates inputs
↓
Database query for user
↓
Password verification using password_verify()
↓
Success: Session created → Redirect to dashboard.php
Failure: Show error message on same page
```

## Security Measures

### 1. Prepared Statements
```php
$stmt = $conn->prepare("SELECT id, password, full_name FROM users WHERE username = ?");
$stmt->bind_param("s", $username);  // "s" = string parameter
```
**Why:** Prevents SQL injection attacks

### 2. Password Hashing
- Uses PHP's `password_verify()` function
- Compares plaintext input against hashed database value
- Never stores plaintext passwords

### 3. Session Regeneration
- Old session ID destroyed after login
- New session ID created
- Prevents session fixation attacks

### 4. Input Sanitization
- `trim()` removes whitespace
- `??` null coalescing prevents undefined keys
- HTML escaping for output (done on display pages)

## Related Pages
- **Redirects To:**
  - Success: `dashboard.php` (main application)
  - Failure: Stays on `login.php` with error message
- **Linked From:**
  - `index.php` (landing page)
  - `register.php` (for existing users)

## Common Issues & Solutions

### Issue 1: "Username not found"
- **Cause:** User enters non-existent username
- **Solution:** Check if username exists in database, direct to register
- **Code Location:** `else` block after query result check

### Issue 2: "Invalid password"
- **Cause:** Password doesn't match hashed value in database
- **Solution:** Ask user to verify password or use password reset
- **Code Location:** `password_verify()` check failure

### Issue 3: Database connection error
- **Cause:** MySQL server not running, credentials wrong, or permissions issue
- **Solution:** Verify database credentials, check MySQL service
- **Debug:** Use `die()` to display `$conn->connect_error`

## Testing User Account
**Default Test User:**
```
Username: testuser
Role: Veterinarian
(See register.php for account creation details)
```

## Defense/Capstone Points
- **Security Demonstrated:**
  - Prepared statements (SQL injection prevention)
  - Password hashing verification
  - Session ID regeneration
  - HTTP-only secure cookies

- **Learning Objectives:**
  - User authentication flow
  - Secure password handling
  - Session management best practices
  - Error handling and user feedback

## Technical Stack
- **PHP**: Form processing, database queries, session handling
- **MySQL**: User credential storage
- **HTML**: Form structure
- **CSS**: Styling and responsive layout
- **No JavaScript**: All logic server-side

## Notes for Defense
- This page is **critical security component**
- Demonstrates understanding of authentication best practices
- Shows SQL injection prevention techniques
- Implements "secure by default" principles

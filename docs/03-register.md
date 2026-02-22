# REGISTER.PHP - User Registration System

## Overview
Allows new users (specifically veterinarians) to create accounts in the system. Uses password hashing, prepared statements, and validates username uniqueness.

## Purpose
- Allow new veterinarians to register for the system
- Create secure user accounts with hashed passwords
- Prevent duplicate usernames
- Handle registration errors gracefully

## Database Connection
**Connection Details:**
```
Host: localhost
User: root (NOTE: Uses root instead of marikina_user - potential security issue)
Password: (empty)
Database: marikina_db
```

**Tables Used:**
- `users` table
  - `id` (Primary Key, Auto-increment)
  - `username` (Unique)
  - `full_name`
  - `password` (Hashed)
  - `role` (Always set to 'Veterinarian')

## Key Features

### 1. Registration Form
**Method:** POST Request
**Form Fields:**
- `username` - Desired username (must be unique)
- `full_name` - Full name of the user
- `password` - Password (plaintext input, hashed on server)
- `register` - Submit button identifier

### 2. Data Processing
```php
$username  = trim($_POST['username'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$password  = $_POST['password'] ?? '';
$role      = 'Veterinarian';  // Fixed role for all registrations

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
```

### 3. Account Creation Flow
```
User enters registration form
↓
Submits data (POST)
↓
Validate: Check if fields are not empty
↓
Hash password using PASSWORD_HASH()
↓
Prepare SQL INSERT statement
↓
Execute insert with parameter binding
↓
Success: Show success message with login link
Failure: Show error message (username exists or DB error)
```

### 4. Error Handling
- **Duplicate Username:** Error code 1062 (MySQL constraint violation)
- **Empty Fields:** Validation error message
- **General DB Errors:** Display error details

### 5. Status Messages
```php
// Success message with link
"Account created successfully! <a href='login.php'>Mag-login na dito</a>"

// Duplicate username
"Username [username] ay ginagamit na. Gumamit ng ibang username."

// Other errors
"May error sa pagrehistro: [error details]"
```

## UI Components

### Form Layout
- **Title:** "Register – Marikina Animal & Welfare"
- **Form Fields:**
  - Username input
  - Full Name input
  - Password input
  - Submit button
- **Link to Login:** For existing users

### Styling
- **Color Theme:** Green primary (#2c7d4e)
- **Background:** Gradient (light green)
- **Form Container:** Centered white box with shadow
- **Status Messages:**
  - Success: Light green background
  - Error: Light red background

### Responsive Design
- Mobile-friendly
- Touch-friendly input fields
- Flexible button sizing

## Data Flow Example

### Successful Registration:
```
1. User fills form:
   - Username: "vet_santos"
   - Full Name: "Dr. Maria Santos"
   - Password: "secure123"

2. Form submitted (POST)

3. Server processing:
   - Validates fields are not empty ✓
   - Hashes password: "$2y$10$..."
   - Sets role: "Veterinarian"
   - Inserts into users table ✓

4. Result: Success message displayed
   "Account created successfully! <a href='login.php'>Mag-login na dito</a>"

5. User can now click link to login.php
```

### Failed Registration (Duplicate Username):
```
1. User attempts to register with existing username

2. MySQL detects constraint violation (errno 1062)

3. Error message shown:
   "Username [vet_santos] ay ginagamit na. Gumamit ng ibang username."

4. User must choose different username and retry
```

## Security Measures

### 1. Password Hashing
```php
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
```
- Uses PHP's default hashing algorithm (bcrypt)
- One-way hashing - cannot be reversed
- Salted automatically by PASSWORD_DEFAULT

### 2. Prepared Statements
```php
$stmt = $conn->prepare("INSERT INTO users (username, full_name, password, role) 
                       VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $full_name, $hashed_password, $role);
```
- Prevents SQL injection
- Parameters bound separately from query

### 3. Input Sanitization
- `trim()` removes whitespace from edges
- Null coalescing `??` prevents undefined keys
- Empty field validation before database operations

### 4. Duplicate Prevention
- Database constraint on `username` (UNIQUE)
- Automatic MySQL enforcement
- User-friendly error message for duplicates

## Related Pages
- **Linked From:**
  - `login.php` (for new users)
  - `index.php` (landing page)
- **Redirects To:**
  - `login.php` (after success or from login page link)

## Common Issues & Solutions

### Issue 1: "Username already exists"
- **Cause:** Username not unique in database
- **Solution:** Display form again, suggest different username
- **Code:** Checks `$conn->errno == 1062`

### Issue 2: Database connection fails
- **Cause:** MySQL server down, wrong credentials, or database not created
- **Solution:** Verify MySQL is running, check credentials
- **Debug:** `die()` shows connection error

### Issue 3: Empty fields not validated
- **Cause:** JavaScript disabled or form modified
- **Solution:** Server-side validation catches it
- **Code:** `if (empty($username) || empty($full_name) || empty($password))`

## Potential Issues to Address

⚠️ **Security Concern:** 
- Uses `root` database user instead of `marikina_user`
- Should be consistent across all files
- `root` has excessive permissions

⚠️ **Form Validation:**
- Minimal password strength requirements
- No email validation
- No phone number field for contact

## Testing Registration

### Test Case 1: Successful Registration
```
Username: testvet2024
Full Name: Test Veterinarian
Password: TestPass123
Result: Should see success message with login link
```

### Test Case 2: Duplicate Username
```
Username: testuser (if already exists)
Full Name: Another Name
Password: TestPass123
Result: Should see "username already in use" error
```

### Test Case 3: Empty Fields
```
Username: (empty)
Full Name: Test Name
Password: TestPass123
Result: Should see "fill in all fields" error message
```

## Defense/Capstone Points
- **Security Demonstrated:**
  - Password hashing implementation
  - Prepared statements for SQL injection prevention
  - Unique constraint validation
  
- **Learning Objectives:**
  - User account creation workflow
  - Database constraint handling
  - Error messaging and user feedback
  - Form validation and sanitization

## Technical Stack
- **PHP**: Form processing, database operations, password hashing
- **MySQL**: User storage, UNIQUE constraint on username
- **HTML**: Form structure
- **CSS**: Styling and responsive layout

## Notes for Defense
- This page implements fundamental user management
- Demonstrates password security best practices
- Shows error handling for database constraints
- Could mention possible improvements (email verification, password strength requirements)

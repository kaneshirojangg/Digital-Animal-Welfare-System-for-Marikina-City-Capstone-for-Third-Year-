# INDEX.PHP - Landing Page & System Overview

## Overview
The landing page serves as the **public-facing entry point** to the Marikina City Animal & Welfare Management System. It provides information about the system, its purpose, and guides users to login or register.

## Purpose
- Display system information and features to visitors
- Provide navigation to login page
- Present overview of animal welfare initiatives
- Mobile-responsive welcome interface

## Database Connection
**No database connection required** - This is a pure HTML/CSS page with static content.

## Key Features

### 1. Navigation Bar
- Brand logo: "Marikina Animal & Welfare"
- Login button that redirects to `login.php`
- Sticky positioning (stays at top while scrolling)

### 2. Header Section
- Main title: "Marikina City Community & Animal Welfare Management System"
- Tagline describing the system's purpose
- Gradient background (green theme)

### 3. Content Sections
- **About Us**: Explains the system's purpose and benefits
- **Purpose Statement**: Clarifies challenges being addressed (fragmented records, incident delays, data inaccuracy)
- **Key Information**: Features and capabilities of the system

## Data Flow
```
User visits index.php
↓
Displays public landing page
↓
User clicks "Log In" button → Redirects to login.php
↓
OR User navigates directly to src/index.php from root
```

## Styling Details
- **Color Scheme**: Green primary color (#2c7d4e), white backgrounds
- **Fonts**: 'Playfair Display' for headings, 'Inter' for body text
- **Responsive Design**: Mobile-friendly with media queries for screens ≤ 768px
- **Grid System**: CSS Grid for responsive layouts

## Technical Implementation
- **HTML5** semantic markup
- **CSS3** with CSS variables for theming
- **No JavaScript** required
- **No PHP backend logic** needed

## User Access
- **Public Access**: ✅ Available to all users (no login required)
- **Role-Based**: ❌ No role restrictions
- **Authentication**: ❌ No authentication needed

## Related Files
- Redirects to: `login.php`
- Includes no other PHP files
- Linked from: Root folder `index.php` (redirect wrapper)

## Navigation Map
```
index.php (Landing Page)
├── Login Link → login.php
└── Static Content Only
```

## Common Scenarios

### Scenario 1: New User Visits System
1. User arrives at landing page
2. Reads "About Us" section
3. Clicks "Log In" button
4. Redirects to login.php

### Scenario 2: Existing User Returns
1. User arrives at landing page
2. Clicks "Log In" button
3. Enters credentials
4. Redirected to dashboard

## Defense/Capstone Points
- **What it demonstrates**: System introduction and UX flow design
- **Key learning**: HTML/CSS responsive design without JavaScript
- **Challenge solved**: Providing accessible entry point to system
- **Design pattern**: MVC separation (view-only component)

## Notes for Team
- This page is presentation layer only
- All dynamic features start after login
- Good example of clean, semantic HTML5 code
- Mobile-responsive using CSS media queries

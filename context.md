# WILDSPACE PROJECT CONTEXT

## PROJECT TYPE
WildSpace is a PHP-based web application for managing study space reservations.

It is built for academic use (Information Management 1 / CSIT226) and uses a PostgreSQL database hosted on Supabase.

---

## CORE PURPOSE
WildSpace helps students efficiently reserve study spaces online instead of manually checking availability.

The system supports:
- Student reservation requests
- Admin-controlled reservation management
- Approval workflow for bookings

Current development focus is **Admin-side functionality and reservation control system**.

---

## SYSTEM ARCHITECTURE OVERVIEW

### Client Side (Frontend)
- HTML (structure)
- CSS (styling)
- JavaScript (interactivity)

### Server Side (Backend)
- PHP (core logic, authentication, CRUD operations)

### Database
- Supabase PostgreSQL
- Stores:
  - Users
  - Reservations
  - Admin actions
  - Reservation status logs

### Local Development Environment
- XAMPP (Apache server + PHP runtime)

### Version Control
- Git
- GitHub

---

## MAIN MODULES

### 1. Authentication Module
Handles user identity and session control.

Features:
- User registration
- User login
- Institutional email validation
- Password validation
- Password hashing (`password_hash`)
- Password verification (`password_verify`)
- Session-based login system

---

### 2. Reservation Module
Handles booking of study spaces.

Features:
- Create reservation
- View reservation records
- Update reservation status
- Cancel or delete reservation
- Store reservation details:
  - Date
  - Capacity
  - Status (Pending / Approved / Rejected / Cancelled)

---

### 3. Admin Module
Controls all reservation management operations.

Features:
- Admin login authentication
- Admin dashboard interface
- View all reservation requests
- Create reservations on behalf of students
- Approve or reject reservations
- Delete reservation records
- Track `admin_id` for audit logging (who approved/rejected)

---

## BUSINESS LOGIC FLOW

1. User registers → account stored in database
2. User logs in → session created
3. User submits reservation request → status = "Pending"
4. Admin logs in → views pending requests
5. Admin action:
   - Approve → status = "Approved"
   - Reject → status = "Rejected"
6. System stores admin ID for tracking decisions

---

## SECURITY IMPLEMENTATION

- Passwords are hashed using `password_hash()`
- Login verification uses `password_verify()`
- Session-based authentication prevents unauthorized access
- Email validation ensures institutional accounts only

---

## PROJECT STRUCTURE (LOGICAL)

Although file structure may vary, the system is organized conceptually as:

- `/actions` → backend form handlers (login, register, reservation actions)
- `/admin` → admin dashboard and admin-only pages
- `/user` → student-facing pages
- `/database` → database connection and queries
- `/assets` → CSS, JS, images
- `/includes` → reusable PHP components (headers, auth checks)

---

## IMPORTANT NOTES FOR AI ASSISTANCE

- This project is PHP procedural/structured (not full MVC framework)
- Authentication relies on PHP sessions
- Database operations are handled via Supabase PostgreSQL
- Admin and user roles are separated logically
- Core focus is CRUD + authentication + reservation workflow
- Maintain compatibility with XAMPP environment

---
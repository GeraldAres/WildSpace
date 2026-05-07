# WildSpace Project README

WildSpace is a PHP-based web application for managing study space reservations. The system allows users to register, log in, book reservations, and allows administrators to manage reservation requests through an admin dashboard.

This project was developed for **Information Management 1 / CSIT226** with database integration using **Supabase PostgreSQL**.

---

## Project Overview

WildSpace is designed to help students reserve study spaces more efficiently. Instead of manually searching for available study areas, users can submit reservation requests through the website.

For the current development phase, the focus is on the admin side of the system. Administrators can create reservations for students, view all reservation requests, approve or reject pending reservations, and delete reservation records.

---

## Features

### Authentication

- User registration
- User login
- Institutional email validation
- Password validation
- Password hashing using PHP `password_hash()`
- Login verification using PHP `password_verify()`
- Session-based authentication

### Reservation Features

- Create reservation
- View reservation records
- Update reservation status
- Cancel or delete reservation
- Store reservation date and capacity
- Track reservation status:
  - Pending
  - Approved
  - Rejected
  - Cancelled

### Admin Features

- Admin login
- Admin dashboard
- View all reservations
- Create reservation for a student
- Approve reservation requests
- Reject reservation requests
- Delete reservations
- Store the `admin_id` of the admin who approved or rejected a reservation

---

## Technology Stack

### Frontend

- HTML
- CSS
- JavaScript

### Backend

- PHP

### Database

- Supabase PostgreSQL

### Local Server

- XAMPP Apache

### Version Control

- Git
- GitHub

# Secure PHP Login System

A simple PHP login system built with security-focused practices using XAMPP, Apache, MySQL, and PDO.

## Features

- Parameterized SQL queries (prevents SQL injection)
- Password hashing with `password_hash()` and `password_verify()` using BCRYPT
- CSRF protection with tokens and `hash_equals()`
- IP-based rate limiting
- Session regeneration after login
- CSRF token rotation
- Safe output with `htmlspecialchars()`
- PDO exception handling

## Technologies

- PHP
- MySQL
- PDO
- XAMPP

## Database Tables

### `userinfo`

```sql
CREATE TABLE userinfo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255),
    password VARCHAR(255)
);
```

### `login_attempts`

```sql
CREATE TABLE login_attempts (
    ip_address VARCHAR(45) PRIMARY KEY,
    attempts INT NOT NULL DEFAULT 1,
    last_attempt DATETIME NOT NULL
);
```

## Security Notes

This project demonstrates basic web authentication security concepts, but it is **not production-ready**.

Known limitations include:

- IP-based rate limiting can be bypassed with proxies/VPNs
- No account-based lockout
- HTTPS must be configured separately
- Default local XAMPP credentials should never be used in production
- Additional session hardening is recommended

## Setup

1. Install XAMPP
2. Start Apache and MySQL
3. Create the `passcheck` database
4. Create the required tables
5. Place the PHP files into the XAMPP `htdocs` directory
6. Open the project in your browser

## Purpose

This project was created to better understand:

- Login authentication
- Session handling
- CSRF protection
- Password hashing
- Rate limiting
- Basic web application security

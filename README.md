# Student Team Finder

Student Team Finder is a PHP/MySQL web platform that helps students discover projects, send join requests, and manage teams. The application ships with three roles—Admin, Project Owner, and Project Applicant—each with dedicated dashboards, workflows, and validation layers.

## Table of Contents

1. Overview
2. Features
3. Tech Stack
4. Project Structure
5. Getting Started
6. Environment Variables
7. Database Schema Checklist
8. Available Controllers & Endpoints
9. Security & Hardening Notes
10. Roadmap

## 1. Overview

- Owners create project listings, set skill requirements, and review incoming join requests.
- Applicants browse/search projects, send contextual requests, and track statuses in real time.
- Admins manage users, monitor platform health, and keep profiles/projects up to date.
- The UI uses AJAX polling for live stats (admin, owner, applicant dashboards) and includes OTP-based password recovery via PHPMailer.

## 2. Features

### Authentication & Accounts

- Registration with server-side validation and role selection.
- Login redirects to role-specific dashboards.
- Forgot password flow with email OTP (expires in 15 minutes) and enforced one-time usage.
- Profile editing with optional avatar upload and password change.

### Admin

- Dashboard cards: total/active/inactive users and projects.
- User CRUD (create, edit role/status, delete, reset password).
- Real-time stats refresh via `fetch_admin_stats.php`.

### Project Owner

- Create/edit/delete projects with image upload (5 MB max, JPG/PNG/GIF).
- Accept/reject join requests and auto-close projects when `max_members` reached.
- Member management (remove members, view contact info).
- Dashboard JSON endpoint `fetch_owner_stats.php` (total/active/closed projects + requests).

### Project Applicant

- Browse/search shared project listings (`views/common`).
- Submit join requests with message length enforcement (20–500 chars).
- Leave projects and track accepted/pending/rejected stats via `fetch_applicant_stats.php`.

### Notifications & Messaging

- Event-based notifications stored in `notifications` table.
- APIs to mark single/all notifications as read and delete them.
- Polling-based notification count indicator.

## 3. Tech Stack

- PHP 8+ (procedural MVC-style organization: controllers/models/views)
- MySQL (MySQLi prepared statements)
- Composer dependencies (PHPMailer)
- Frontend: HTML, CSS, vanilla JS with AJAX polling
- Web server: Apache (XAMPP recommended)

## 4. Project Structure

```text
project/
├─ controllers/         # Business logic & HTTP handlers
├─ models/              # Database access helpers
├─ views/               # UI for admin/common/owner/applicant
├─ resources/           # Uploaded images
├─ vendor/              # Composer dependencies
├─ .env.example         # Sample environment configuration
├─ composer.json
└─ index.php            # Redirects to login
```

## 5. Getting Started

### Prerequisites

- PHP 8+, Composer, MySQL 5.7/8+, Apache/XAMPP

### Installation

1. Clone the repository into `htdocs` (or preferred web root).
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy `.env.example` to `.env` and set DB + SMTP credentials.
4. Create a MySQL database named `student_team_finder` (see Schema Checklist).
5. Start Apache & MySQL, then visit `http://localhost/project/`.

### Running OTP Emails Locally

- Use an app password when connecting to Gmail SMTP.
- Ensure less-secure-apps or app passwords are enabled for your provider.
- Check `otp_logs.txt` for delivery diagnostics.

## 6. Environment Variables

The project now loads secrets from `.env` (already git-ignored). Key variables:

```ini
DB_HOST=localhost
DB_PORT=3306
DB_NAME=student_team_finder
DB_USER=root
DB_PASS=your_db_password

SMTP_HOST=smtp.gmail.com
SMTP_PORT=465
SMTP_AUTH=true
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_ENCRYPTION=ssl   # ssl or tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=TeamConnect
```

## 7. Database Schema Checklist

Provide or import SQL that defines the following tables/columns (minimum set expected by the code):

- `users`: `user_id`, `name`, `email`, `password`, `role`, `gender`, `status`, `profile_image`, `created_at`
- `projects`: `project_id`, `owner_id`, `title`, `description`, `required_skills`, `max_members`, `status`, `cover_image`, `created_at`
- `project_applications`: `application_id`, `applicant_id`, `project_id`, `status`, `message`, `applied_at`
- `project_members`: `member_id`, `project_id`, `user_id`, `joined_at`
- `notifications`: `notification_id`, `user_id`, `message`, `is_read`, `created_at`
- `password_resets`: `id`, `email`, `otp`, `expires_at`, `is_used`, `created_at`

## 8. Available Controllers & Endpoints

- Authentication: `authControl.php`, `registerControl.php`, `forgotPasswordControl.php`
- User management: `addUserControl.php`, `updateUserControl.php`, `deleteUserControl.php`, `updateProfileControl.php`
- Projects: `createProjectControl.php`, `updateProjectControl.php`, `deleteProjectControl.php`, `fetch_projects.php`
- Join requests: `joinProjectControl.php`, `acceptJoinRequestControl.php`, `rejectJoinRequestControl.php`, `leaveProjectControl.php`, `removeMemberControl.php`
- Notifications: `getNotificationCountControl.php`, `markNotificationControl.php`, `markAllNotificationsControl.php`, `deleteNotificationControl.php`
- Dashboards/API: `fetch_admin_stats.php`, `fetch_owner_stats.php`, `fetch_applicant_stats.php`

## 9. Security & Hardening Notes

- Passwords hashed via `password_hash()` / `password_verify()`.
- Prepared statements used for DB access.
- Server-side validation in controllers (length, email, role, etc.).
- Recommendations:
  - Move SMTP credentials to secrets manager in production.
  - Add CSRF tokens for all forms.
  - Rate-limit login/OTP endpoints.
  - Add logging and monitoring for admin operations.

## 10. Roadmap

- Provide SQL migrations/seeder scripts.
- Add PHPUnit/Laravel-style automated tests.
- Replace AJAX polling with WebSockets/SSE for notifications and dashboards.
- Add REST API documentation (OpenAPI/Swagger) for easier integration.
- Containerize with Docker for portable deployments.

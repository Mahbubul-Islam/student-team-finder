# Implemented Features

## T-1: Implement User Registration and login Page

This task involves creating a registration form component with full name, email, gender, password fields, including validation and error handling and a login form component with email and password.
**Status: Implemented**

## T-2: Implement forgot password

This task involves creating forgot pass form and verity otp form and email sending functionality for password reset.

**Status: Implemented**

## T-3: Implement admin dashboard

This task involves creating an admin dashboard with statistics cards (total users, active users, inactive users, total projects), user management table with CRUD operations (create, read, update, delete), edit user form with all fields including profile image upload (5MB max, PNG/JPG) and optional password change, add user form where admin can create users including other admins, delete functionality with confirmation dialog, default profile image, AJAX auto-refresh every 5 seconds for real-time updates, and responsive navbar and footer components.

**Status: Implemented**

## T-4: Implement admin profile

This task involves creating an admin profile page displaying user information with profile picture , showing all profile details (name, email, role, gender, status) in a clean card layout, edit profile button that redirects to the user edit form.

**Status: Implemented**

## T-5: Implement Common Pages and Project Management

This task involves creating a common folder structure for shared pages accessible to all user roles, implementing home page using AJAX/JSON, creating profile page for all users with role-based edit routing, implementing editProfile form for users to edit their own profile, creating project details page displaying full project information, implementing edit project, adding delete project functionality.

**Status: Implemented**

## T-6: Implement Notification, Real-Time Dashboard Updates and Server-Side Validation

This task involves implementing real-time dashboard updates for project owner using AJAX polling every 5 seconds, creating fetch (fetch_owner_stats.php, fetch_applicant_stats.php, fetch_admin_stats.php) returning JSON data, adding member count to project listings, fixing view button functionality and button logic for active/closed projects, removing success/error alerts while keeping confirmation dialogs for delete/reject/leave operations, adding active/closed project counts to admin dashboard with icons and styling, fixing back button to use history.back(), implementing separate session keys for project vs user messages to fix message display issues, removing HTML required attributes from all forms to demonstrate server-side validation, adding success message displays to admin dashboard, profile, and login pages, and implementing navbar brand link to home page, also implement notification based on user actions.

**Status: Implemented**

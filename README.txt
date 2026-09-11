================================================================================
                    CAREERBRIDGE PROJECT - README
================================================================================

Project Name: CareerBridge — A Role-Based Recruitment & Application Tracking Platform
Your Part: Common Features + Employer Features
Created: August 2026

================================================================================
                        TABLE OF CONTENTS
================================================================================
1. Project Overview
2. Your Responsibility (What You Built)
3. Technologies Used
4. Folder Structure Explanation
5. Database Setup (Step by Step)
6. Running the Project
7. Testing Your Features
8. File-by-File Explanation
9. Common Issues & Solutions
10. Integration with Teammates

================================================================================
                        1. PROJECT OVERVIEW
================================================================================
CareerBridge is a role-based recruitment marketplace with 4 user roles:
- Employer (posts jobs, manages company profile)
- Job Seeker (searches jobs, applies, bookmarks)
- Recruiter (reviews applications, updates status)
- Admin (manages categories, oversees platform)

Your team assigned you: Common Features + Employer Features

================================================================================
                    2. YOUR RESPONSIBILITY (What You Built)
================================================================================

A. COMMON FEATURES (Available to All Users):
   ✓ User Registration (with role selection)
   ✓ Login System (session-based authentication)
   ✓ Logout
   ✓ Change Password (with current password verification)
   ✓ View/Edit Profile
   ✓ Upload Profile Picture
   ✓ Delete Account
   ✓ Role-based Dashboard Routing

B. EMPLOYER FEATURES:
   ✓ Employer Dashboard (shows all posted jobs with applicant count)
   ✓ Post New Job (title, category, description, requirements, salary, location, type, deadline)
   ✓ Edit Job Post
   ✓ Close/Reopen Job (AJAX toggle between active/closed status)
   ✓ View Job Details (with company information displayed)
   ✓ Manage Company Profile (name, industry, description, website)
   ✓ Upload Company Logo

================================================================================
                        3. TECHNOLOGIES USED
================================================================================
- Frontend: HTML5, CSS3, JavaScript (Vanilla JS for AJAX)
- Backend: PHP (procedural + OOP with MVC pattern)
- Database: MySQL (via phpMyAdmin)
- Server: Apache (via XAMPP)
- IDE: VS Code

================================================================================
                    4. FOLDER STRUCTURE EXPLANATION
================================================================================

project-files/
│
├── index.php                    # Router (redirects to appropriate dashboard)
├── database_setup.txt           # Database creation guide
│
├── controller/                  # Handles form submissions & logic
│   ├── login-handler.php
│   ├── register-handler.php
│   ├── logout-handler.php
│   ├── profile-update-handler.php
│   ├── password-change-handler.php
│   ├── profile-pic-upload-handler.php
│   ├── create-job-handler.php
│   ├── edit-job-handler.php
│   └── toggle-job-status.php   # AJAX endpoint
│
├── model/                       # Database interaction classes
│   ├── User.php                 # User CRUD operations
│   └── Job.php                  # Job CRUD operations
│
├── view/                        # HTML pages
│   ├── login.php
│   ├── register.php
│   ├── profile.php
│   ├── employer-dashboard.php
│   ├── create-job.php
│   ├── edit-job.php
│   ├── view-job.php
│   ├── company-profile.php
│   ├── seeker-dashboard.php     # Placeholder for teammates
│   ├── recruiter-dashboard.php  # Placeholder for teammates
│   ├── admin-dashboard.php      # Placeholder for teammates
│   └── layout/
│       └── header.php           # Navigation bar
│
├── db/
│   └── db_connection.php        # Database connection class
│
├── images/                      # Uploaded profile pictures & company logos
├── resumes/                     # Uploaded resume PDFs

================================================================================
                    5. DATABASE SETUP (Step by Step)
================================================================================

STEP 1: Start XAMPP
- Open XAMPP Control Panel
- Click "Start" for Apache
- Click "Start" for MySQL
- Both should turn green

STEP 2: Open phpMyAdmin
- Open browser
- Go to: http://localhost/phpmyadmin/

STEP 3: Create Database
- Click "New" or "Databases" tab
- Database name: career_bridge
- Collation: utf8mb4_general_ci
- Click "Create"

STEP 4: Create Tables
- Click on "career_bridge" database in left sidebar
- Click "SQL" tab at the top
- Open the file: database_setup.txt
- Copy the entire SQL script from that file
- Paste into the SQL text area
- Click "Go" button

STEP 5: Verify
- You should see 4 tables created:
  * users
  * jobs
  * applications
  * saved_jobs

STEP 6: Insert Sample Data (Optional)
- The database_setup.txt file includes sample INSERT queries
- Copy and run them to create test accounts

================================================================================
                        6. RUNNING THE PROJECT
================================================================================

STEP 1: Place Files in XAMPP
- Copy the entire "project-files" folder
- Paste it into: C:\xampp\htdocs\
- Rename it if you want (e.g., "careerbridge")

STEP 2: Access in Browser
- Make sure XAMPP Apache & MySQL are running
- Open browser
- Go to: http://localhost/project-files/
  (or http://localhost/careerbridge/ if you renamed it)

STEP 3: You Should See
- The login page
- If not, check if index.php exists in the root

================================================================================
                    7. TESTING YOUR FEATURES
================================================================================

A. TEST REGISTRATION & LOGIN:
   1. Go to http://localhost/project-files/
   2. Click "Register here"
   3. Fill form:
      - Username: testemployer
      - Email: test@company.com
      - Password: test123
      - Role: Employer (Post Jobs)
   4. Click "Register"
   5. Login with testemployer / test123
   6. You should land on Employer Dashboard

B. TEST COMPANY PROFILE:
   1. After login, click "Company Profile" in navigation
   2. Fill in company details
   3. Upload a company logo (JPG/PNG)
   4. Click "Save"

C. TEST JOB POSTING:
   1. Click "Post New Job" button on dashboard
   2. Fill all fields (all are required)
   3. Set deadline to a future date
   4. Click "Post Job"
   5. You should see it on your dashboard

D. TEST JOB EDITING:
   1. On dashboard, click "Edit" next to a job
   2. Modify any field
   3. Click "Update Job"

E. TEST AJAX STATUS TOGGLE:
   1. On dashboard, click the green "Active" badge
   2. Without page reload, it should turn red "Closed"
   3. Click again to toggle back to "Active"

F. TEST PASSWORD CHANGE:
   1. Click "Profile" in navigation
   2. Scroll to "Change Password" section
   3. Enter current password
   4. Enter new password
   5. Click "Update Password"
   6. Logout and login with new password

G. TEST PROFILE PICTURE:
   1. Go to Profile page
   2. Upload an image (JPG/PNG)
   3. Picture should update

================================================================================
                    8. FILE-BY-FILE EXPLANATION
================================================================================

CORE FILES:
-----------
index.php
  - Router that checks if user is logged in
  - Redirects to appropriate dashboard based on role
  - Entry point of the application

db/db_connection.php
  - Creates connection to MySQL database
  - Used by all Model classes

MODELS (model/ folder):
----------------------
User.php
  - registerUser(): Creates new user account
  - loginCheck(): Verifies username & password
  - getUserById(): Fetches user data
  - updateProfile(): Updates email & role-specific fields
  - changePassword(): Updates password after verifying current
  - updateProfilePic(): Updates profile_pic path
  - updateCompanyLogo(): Updates company_logo path
  - deleteUser(): Deletes user account

Job.php
  - createJob(): Inserts new job post
  - updateJob(): Updates existing job
  - toggleStatus(): Changes job status (active/closed)
  - getJobDetails(): Fetches single job with company info (JOIN)
  - getEmployerJobs(): Fetches all jobs by employer with applicant count
  - getActiveJobs(): Fetches all active jobs for seekers

CONTROLLERS (controller/ folder):
---------------------------------
login-handler.php
  - Receives POST from login.php
  - Calls User->loginCheck()
  - Sets session variables (user_id, username, email, role, etc.)
  - Redirects to index.php (router handles dashboard)

register-handler.php
  - Receives POST from register.php
  - Validates role selection
  - Calls User->registerUser()
  - Redirects to login page with success message

logout-handler.php
  - Destroys session
  - Redirects to homepage

profile-update-handler.php
  - Updates email and role-specific fields
  - Handles account deletion

password-change-handler.php
  - Verifies current password
  - Updates to new password

profile-pic-upload-handler.php
  - Handles 3 types of uploads:
    1. Profile picture (default)
    2. Company logo (upload_type=logo)
    3. Resume PDF (upload_type=resume)
  - Validates file type & size
  - Moves file to images/ or resumes/ folder
  - Updates database

create-job-handler.php
  - Receives POST from create-job.php
  - Calls Job->createJob()
  - Redirects to employer dashboard

edit-job-handler.php
  - Verifies ownership of job
  - Calls Job->updateJob()
  - Redirects to dashboard

toggle-job-status.php
  - AJAX endpoint (returns JSON)
  - Receives job_id and new status
  - Verifies ownership
  - Calls Job->toggleStatus()
  - Returns {"success": true} or {"success": false}

VIEWS (view/ folder):
--------------------
login.php
  - Styled login form
  - Checks if already logged in (redirects if yes)
  - Shows error messages from session

register.php
  - Registration form with role dropdown
  - 4 roles: employer, seeker, recruiter, admin

profile.php
  - Universal profile page for all roles
  - Shows different fields based on role
  - Employer: company fields
  - Seeker: headline, resume
  - Common: email, password change, profile pic

employer-dashboard.php
  - Shows table of all jobs posted by this employer
  - Displays: ID, Title, Category, Deadline, Applicant Count, Status
  - Status badge is clickable (AJAX toggle)
  - JavaScript fetch() for AJAX call

create-job.php
  - Form with all job fields
  - Dropdown for job_type
  - Date picker for deadline

edit-job.php
  - Pre-fills form with existing job data
  - Hidden input for job_id

view-job.php
  - Displays full job details
  - Shows company information at bottom
  - Includes company logo

company-profile.php
  - Focused page for employer company details
  - Alternative to using profile.php

layout/header.php
  - Navigation bar
  - Shows different links based on role
  - Includes logout link

OTHER DASHBOARDS:
----------------
seeker-dashboard.php
recruiter-dashboard.php
admin-dashboard.php
  - Placeholder pages
  - Your teammates will build these

================================================================================
                    9. COMMON ISSUES & SOLUTIONS
================================================================================

ISSUE 1: "Connection failed" error
SOLUTION: Make sure MySQL is running in XAMPP. Database name should be
          'career_bridge' (not 'wtm' or anything else).

ISSUE 2: Login page shows but cannot login
SOLUTION: Check if users table exists in database. Run the SQL script from
          database_setup.txt again.

ISSUE 3: Images not uploading
SOLUTION: Make sure images/ and resumes/ folders exist in project root.
          Check folder permissions (should be writable).

ISSUE 4: AJAX toggle not working
SOLUTION: Open browser console (F12) and check for JavaScript errors.
          Verify toggle-job-status.php path is correct.

ISSUE 5: "headers already sent" error
SOLUTION: Make sure there is NO output (echo, whitespace, HTML) before
          header() calls in controller files.

ISSUE 6: Uploaded images show as broken
SOLUTION: Check the path in database. It should be relative like:
          images/filename.jpg (not absolute path)

ISSUE 7: Session not persisting (keeps logging out)
SOLUTION: Make sure session_start() is at the very top of every page.

================================================================================
                    10. INTEGRATION WITH TEAMMATES
================================================================================

YOUR CODE IS READY FOR INTEGRATION!

What your teammates need to know:
----------------------------------

1. SESSION VARIABLES AVAILABLE:
   $_SESSION['user_id']        // User's database ID
   $_SESSION['username']       // Username
   $_SESSION['email']          // Email
   $_SESSION['role']           // 'employer', 'seeker', 'recruiter', 'admin'
   $_SESSION['profile_pic']    // Path to profile picture

   For Employers:
   $_SESSION['company_name']
   $_SESSION['company_logo']

   For Seekers:
   $_SESSION['headline']
   $_SESSION['resume_path']

2. DATABASE TABLES:
   - users: All user accounts
   - jobs: All job postings
   - applications: Job applications (for Seeker/Recruiter features)
   - saved_jobs: Bookmarked jobs (for Seeker features)

3. MODEL CLASSES TO USE:
   - User.php: For any user-related queries
   - Job.php: For any job-related queries
   - They can create: Application.php, SavedJob.php

4. PROTECTED ROUTES:
   All view pages should check:
   if (!isset($_SESSION['username'])) {
       header("Location: login.php");
       exit();
   }

5. ROLE-BASED ACCESS:
   if ($_SESSION['role'] != 'seeker') {
       // Not authorized
   }

================================================================================
                            END OF README
================================================================================

Good luck with your project presentation!

For questions, refer to:
- database_setup.txt (database schema details)
- Your sir's sample code (theory4/mvc folder)
- PHP Manual: https://www.php.net/manual/en/

Remember to test everything before the demo!

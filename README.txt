CAREERBRIDGE PROJECT

Project description:
CareerBridge is a role-based recruitment system for 4 users: Employer, Job Seeker, Recruiter, and Admin. It lets employers post and manage jobs, seekers search/apply/save jobs, recruiters review applications, and admins manage categories and platform data.

Roles and main files:

1) Common files for all users
- index.php                  -> main entry point and role-based redirect
- db/db_connection.php       -> shared database connection class
- model/User.php             -> login, register, profile, password, profile picture, and user-related operations
- controller/login-handler.php
- controller/register-handler.php
- controller/logout-handler.php
- controller/profile-update-handler.php
- controller/password-change-handler.php
- controller/profile-pic-upload-handler.php
- view/login.php
- view/register.php
- view/profile.php
- view/layout/header.php     -> shared navigation
- images/                    -> profile/company images
- resumes/                   -> resume uploads

2) Employer files
- view/employer-dashboard.php
- view/create-job.php
- view/edit-job.php
- view/company-profile.php
- view/view-job.php
- controller/create-job-handler.php
- controller/edit-job-handler.php
- controller/toggle-job-status.php
- model/Job.php              -> job creation, editing, status toggle, employer job listing

3) Job Seeker files
- view/seeker-dashboard.php
- view/search-jobs.php
- view/saved-jobs.php
- view/my-applications.php
- view/apply-job.php
- model/JobSeeker.php        -> saved jobs, applications, seeker dashboard data
- controller/submit-application.php
- controller/toggle-bookmark.php

4) Recruiter files
- view/recruiter-dashboard.php
- controller/admin-job-status-handler.php
- controller/search-jobs-ajax.php
- model/Job.php or JobSeeker.php can be reused for application-related queries

5) Admin files
- view/admin-dashboard.php
- view/manage-categories.php
- view/platform-analytics.php
- model/Category.php         -> category list, create, update, delete, usage checks
- controller/category-create-handler.php
- controller/category-update-handler.php
- controller/category-delete-handler.php

Other important files:
- model/Category.php         -> admin category management
- controller/search-jobs-ajax.php -> AJAX search/filter support
- uploads/resumes/           -> uploaded resume storage

Notes:
- All roles share the same login/register/profile system.
- The database name used by the app is career_bridge.
- Run the SQL in sql.txt (or database_setup.txt) first to create the tables.

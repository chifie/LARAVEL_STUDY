# School SMS

A lightweight School Management System built with plain PHP, MySQL, and Bootstrap.  
It supports authentication, role-based access, academic records, attendance, exams, results, payments, announcements, notifications, and backups.

## Features

- Authentication with forced first-time password change
- Role-based access for Super Admin, Teacher, and Student
- Academic years and terms
- Classes, subjects, teachers, and students
- Class subject assignment
- Enrollments and attendance
- Exams, results, and grading
- Payments and fee structures
- Announcements and notifications
- Activity logs and backup jobs
- Shared layout components for admin, auth, teacher, and student pages

## Requirements

- PHP 7.4 or newer
- MySQL / MariaDB
- XAMPP, WAMP, Laragon, or a similar local stack
- Apache with `mod_rewrite` enabled

## Folder Structure

```text
school-sms/
├── app/
│   ├── bootstrap.php
│   ├── config/
│   │   ├── app.php
│   │   └── database.php
│   ├── controllers/
│   │   ├── AnnouncementController.php
│   │   ├── AttendanceController.php
│   │   ├── AuthController.php
│   │   ├── ClassController.php
│   │   ├── ExamController.php
│   │   ├── NotificationController.php
│   │   ├── PaymentController.php
│   │   ├── ReportController.php
│   │   ├── StudentController.php
│   │   ├── SubjectController.php
│   │   └── SuperAdminController.php
│   ├── core/
│   │   ├── Auth.php
│   │   ├── Csrf.php
│   │   ├── Database.php
│   │   ├── Helpers.php
│   │   ├── Response.php
│   │   ├── Session.php
│   │   └── Validator.php
│   ├── middleware/
│   │   ├── require_login.php
│   │   ├── require_password_change.php
│   │   └── require_role.php
│   ├── models/
│   │   ├── AcademicYear.php
│   │   ├── ActivityLog.php
│   │   ├── Announcement.php
│   │   ├── Attendance.php
│   │   ├── ClassSubject.php
│   │   ├── Enrollment.php
│   │   ├── Exam.php
│   │   ├── FeeStructure.php
│   │   ├── Notification.php
│   │   ├── Payment.php
│   │   ├── Result.php
│   │   ├── SchoolClass.php
│   │   ├── Student.php
│   │   ├── Subject.php
│   │   ├── Teacher.php
│   │   ├── Term.php
│   │   └── User.php
│   ├── services/
│   │   └── BackupService.php
│   └── views/
│       ├── admin/
│       ├── announcements/
│       ├── auth/
│       ├── layouts/
│       ├── notifications/
│       ├── student/
│       └── teacher/
├── database/
│   ├── schema.sql
│   ├── seeds.sql
│   ├── phase8.sql
│   └── phase10.sql
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── logout.php
├── scripts/
│   ├── backup_database.php
│   └── create_admin.php
├── database.sql
├── .gitignore
└── README.md
```

## Installation

1. Copy the project into your web server root, for example:
   `C:\xampp 7.4\htdocs\school-sms`

2. Start Apache and MySQL.

3. Create the database:
   ```sql
   CREATE DATABASE school_sms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Import the schema:
   ```powershell
   Get-Content .\database\schema.sql | & "C:\xampp 7.4\mysql\bin\mysql.exe" -u root school_sms
   ```

   Then import the later phases if needed:
   ```powershell
   Get-Content .\database\phase8.sql | & "C:\xampp 7.4\mysql\bin\mysql.exe" -u root school_sms
   Get-Content .\database\phase10.sql | & "C:\xampp 7.4\mysql\bin\mysql.exe" -u root school_sms
   ```

5. Update database credentials in:
   - `app/config/database.php`
   - any environment/config file used by your project

6. Create the initial super admin:
   ```powershell
   php scripts/create_admin.php
   ```

## Running the Application

Open the project in the browser through the public folder, for example:

```text
http://localhost/school-sms/public
```

If your Apache virtual host points to `public/`, then you can open the project root domain directly.

## Default Login

The admin creator script prints the login details after creation.  
Use those credentials on first login and change the password immediately.

## Database Notes

- `school_sms` is the database name used throughout the project.
- The system uses foreign keys heavily, so import the schema in the correct order.
- If you run a phase SQL file twice, you may get duplicate column or table warnings. That usually means the migration already ran.

## Shared Layouts

The project is organized around reusable templates:

- `app/views/layouts/auth.php`
- `app/views/layouts/admin.php`

These keep the admin, login, teacher, and student pages consistent.

## Backup

Database backup support is provided through:

- `scripts/backup_database.php`
- `app/services/BackupService.php`

Backups should be stored outside the public web root.

## Troubleshooting

### Unknown column errors

If a report page says a column like `ay.year_name` is missing, check whether the query joined the `academic_years` table with the alias `ay`.

Example fix:

```sql
LEFT JOIN academic_years ay ON ay.id = e.academic_year_id
```

### Class not found errors

If PHP reports a missing class, confirm the file is loaded in `app/bootstrap.php`.

### Database connection errors

Confirm the MySQL port, host, username, password, and database name match the active XAMPP instance.

### Mixed XAMPP installs

If you have multiple XAMPP versions installed, always use the full path to the exact PHP and MySQL binaries for this project.

## Development Workflow

```powershell
git add .
git commit -m "Update School SMS"
git push
```

## License

Private project unless a license is added later.

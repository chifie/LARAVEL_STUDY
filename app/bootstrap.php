<?php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/app/core/Helpers.php';
load_env(BASE_PATH . '/.env');

$GLOBALS['APP_CONFIG'] = require BASE_PATH . '/app/config/app.php';
$GLOBALS['DB_CONFIG'] = require BASE_PATH . '/app/config/database.php';

date_default_timezone_set(app_config('timezone', 'Africa/Dar_es_Salaam'));

require_once BASE_PATH . '/app/core/Session.php';
require_once BASE_PATH . '/app/core/Database.php';
require_once BASE_PATH . '/app/core/Response.php';
require_once BASE_PATH . '/app/core/Csrf.php';
require_once BASE_PATH . '/app/core/Validator.php';
require_once BASE_PATH . '/app/core/Auth.php';

Session::start(app_config('session', []));

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/Teacher.php';
require_once BASE_PATH . '/app/models/Student.php';
require_once BASE_PATH . '/app/models/AcademicYear.php';
require_once BASE_PATH . '/app/models/Term.php';
require_once BASE_PATH . '/app/models/SchoolClass.php';
require_once BASE_PATH . '/app/models/Subject.php';
require_once BASE_PATH . '/app/models/ClassSubject.php';
require_once BASE_PATH . '/app/models/Enrollment.php';
require_once BASE_PATH . '/app/models/Attendance.php';
require_once BASE_PATH . '/app/models/Exam.php';
require_once BASE_PATH . '/app/models/Result.php';
require_once BASE_PATH . '/app/models/FeeStructure.php';
require_once BASE_PATH . '/app/models/Payment.php';
require_once BASE_PATH . '/app/models/Announcement.php';
require_once BASE_PATH . '/app/models/Notification.php';
require_once BASE_PATH . '/app/models/ActivityLog.php';

require_once BASE_PATH . '/app/services/BackupService.php';

require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/SuperAdminController.php';
require_once BASE_PATH . '/app/controllers/ClassController.php';
require_once BASE_PATH . '/app/controllers/SubjectController.php';
require_once BASE_PATH . '/app/controllers/StudentController.php';
require_once BASE_PATH . '/app/controllers/AttendanceController.php';
require_once BASE_PATH . '/app/controllers/ExamController.php';
require_once BASE_PATH . '/app/controllers/PaymentController.php';
require_once BASE_PATH . '/app/controllers/AnnouncementController.php';
require_once BASE_PATH . '/app/controllers/NotificationController.php';
require_once BASE_PATH . '/app/controllers/ReportController.php';
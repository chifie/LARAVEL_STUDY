<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/bootstrap.php';

$authController = new AuthController();
$superAdminController = new SuperAdminController();
$classController = new ClassController();
$subjectController = new SubjectController();
$studentController = new StudentController();
$attendanceController = new AttendanceController();
$examController = new ExamController();
$paymentController = new PaymentController();
$announcementController = new AnnouncementController();
$notificationController = new NotificationController();
$reportController = new ReportController();

$page = isset($_GET['page']) ? trim((string) $_GET['page']) : '';

if ($page === 'login' && is_post()) {
    $authController->login();
}

if ($page === 'change-password' && is_post()) {
    $authController->changePassword();
}

if (!Auth::check()) {
    if ($page !== 'login') {
        Response::redirect(Auth::loginUrl());
    }

    $authController->showLogin();
    exit;
}

if (Auth::mustChangePassword() && $page !== 'change-password') {
    Response::redirect(Auth::changePasswordUrl());
}

switch ($page) {
    case '':
    case 'login':
        Response::redirect(Auth::dashboardUrl());
        break;

    case 'change-password':
        $authController->showChangePassword();
        break;

    case 'admin.dashboard':
        $superAdminController->dashboard();
        break;

    case 'admin.users':
        $superAdminController->users();
        break;

    case 'admin.users.create-teacher':
        if (is_post()) {
            $superAdminController->storeTeacher();
        }
        $superAdminController->showCreateTeacher();
        break;

    case 'admin.users.create-student':
        if (is_post()) {
            $superAdminController->storeStudent();
        }
        $superAdminController->showCreateStudent();
        break;

    case 'admin.users.toggle-status':
        $superAdminController->toggleUserStatus();
        break;

    case 'admin.structure.years':
        if (is_post()) {
            $classController->storeYear();
        }
        $classController->years();
        break;

    case 'admin.structure.terms':
        if (is_post()) {
            $classController->storeTerm();
        }
        $classController->terms();
        break;

    case 'admin.structure.classes':
        if (is_post()) {
            $classController->storeClass();
        }
        $classController->classes();
        break;

    case 'admin.structure.classes.toggle-status':
        $classController->toggleClassStatus();
        break;

    case 'admin.structure.subjects':
        if (is_post()) {
            $subjectController->store();
        }
        $subjectController->index();
        break;

    case 'admin.structure.subjects.toggle-status':
        $subjectController->toggleStatus();
        break;

    case 'admin.structure.assignments':
        if (is_post()) {
            $classController->storeAssignment();
        }
        $classController->assignments();
        break;

    case 'admin.students':
        $studentController->index();
        break;

    case 'admin.students.enroll':
        if (is_post()) {
            $studentController->storeEnrollment();
        }
        $studentController->showEnrollForm();
        break;

    case 'admin.students.promote':
        if (is_post()) {
            $studentController->storePromotion();
        }
        $studentController->showPromoteForm();
        break;

    case 'admin.students.profile':
        if (is_post()) {
            $studentController->updateProfile();
        }
        $studentController->profile();
        break;

    case 'admin.students.history':
        $studentController->history();
        break;

    case 'admin.attendance':
        if (is_post()) {
            $attendanceController->store();
        }
        $attendanceController->index();
        break;

    case 'admin.attendance.daily':
        $attendanceController->daily();
        break;

    case 'admin.attendance.summary':
        $attendanceController->summary();
        break;

    case 'teacher.attendance':
        if (is_post()) {
            $attendanceController->store();
        }
        $attendanceController->index();
        break;

    case 'admin.exams':
        $examController->index();
        break;

    case 'admin.exams.create':
        if (is_post()) {
            $examController->store();
        }
        $examController->create();
        break;

    case 'admin.exams.marks':
        if (is_post()) {
            $examController->storeMarks();
        }
        $examController->marks();
        break;

    case 'admin.exams.results':
        $examController->results();
        break;

    case 'teacher.exams':
        $examController->index();
        break;

    case 'teacher.exams.create':
        if (is_post()) {
            $examController->store();
        }
        $examController->create();
        break;

    case 'teacher.exams.marks':
        if (is_post()) {
            $examController->storeMarks();
        }
        $examController->marks();
        break;

    case 'teacher.exams.results':
        $examController->results();
        break;

    case 'student.my-results':
        $examController->studentResults();
        break;

    case 'admin.payments':
        if (is_post()) {
            if (isset($_POST['payment_action']) && $_POST['payment_action'] === 'fee') {
                $paymentController->storeFee();
            } elseif (isset($_POST['payment_action']) && $_POST['payment_action'] === 'payment') {
                $paymentController->storePayment();
            }
        }
        $paymentController->index();
        break;

    case 'admin.payments.report':
        $paymentController->report();
        break;

    case 'student.my-payments':
        $paymentController->studentMyPayments();
        break;

    case 'admin.announcements':
        if (is_post() && isset($_POST['announcement_action']) && $_POST['announcement_action'] === 'publish') {
            $announcementController->publish();
        } elseif (is_post()) {
            $announcementController->store();
        }
        $announcementController->index();
        break;

    case 'admin.announcements.create':
        if (is_post()) {
            $announcementController->store();
        }
        $announcementController->create();
        break;

    case 'admin.announcements.publish':
        if (is_post()) {
            $announcementController->publish();
        }
        Response::redirect(url('?page=admin.announcements'));
        break;

    case 'teacher.announcements':
        $announcementController->feed();
        break;

    case 'student.announcements':
        $announcementController->feed();
        break;

    case 'admin.notifications':
    case 'teacher.notifications':
    case 'student.notifications':
        $notificationController->index();
        break;

    case 'notification.read':
        if (is_post()) {
            $notificationController->markRead();
        }
        Response::redirect($notificationController->redirectBack());
        break;

    case 'notification.read-all':
        if (is_post()) {
            $notificationController->markAllRead();
        }
        Response::redirect($notificationController->redirectBack());
        break;

    case 'admin.reports':
        $reportController->index();
        break;

    case 'admin.reports.academic':
        $reportController->academic();
        break;

    case 'admin.reports.attendance':
        $reportController->attendance();
        break;

    case 'admin.reports.logs':
        $reportController->logs();
        break;

    case 'admin.reports.backups':
        if (is_post()) {
            $reportController->createBackup();
        }
        $reportController->backups();
        break;

    case 'admin.reports.backups.download':
        $reportController->downloadBackup();
        break;

    case 'admin.reports.payments':
        Response::redirect(url('?page=admin.payments.report'));
        break;

    case 'teacher.dashboard':
        Auth::requireRole(['teacher']);
        require app_path('views/teacher/dashboard.php');
        break;

    case 'student.dashboard':
        Auth::requireRole(['student']);
        require app_path('views/student/dashboard.php');
        break;

    case 'student.profile':
        if (is_post()) {
            $studentController->updateProfile();
        }
        $studentController->profile();
        break;

    case 'student.history':
        $studentController->history();
        break;

    default:
        Response::redirect(Auth::dashboardUrl());
}
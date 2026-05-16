<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Teacher Dashboard';
$pageSubtitle = 'Track your teaching tasks, attendance, exams, and announcements from here.';
$pageActions = implode('', [
    '<a href="' . e(url('?page=teacher.attendance')) . '" class="btn btn-outline-primary rounded-4"><i class="fa-solid fa-clipboard-check me-2"></i>Attendance</a>',
    '<a href="' . e(url('?page=teacher.exams')) . '" class="btn btn-outline-dark rounded-4"><i class="fa-solid fa-file-pen me-2"></i>Exams</a>',
    '<a href="' . e(url('?page=teacher.notifications')) . '" class="btn btn-outline-warning rounded-4"><i class="fa-regular fa-bell me-2"></i>Notifications</a>',
]);

$user = Auth::user();
$unreadNotifications = isset($unreadNotifications) ? (int) $unreadNotifications : (int) Notification::unreadCountForUser(Auth::id());

ob_start();
?>
<div class="row g-3 g-xl-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 p-xl-5">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-4 align-items-start">
                    <div>
                        <span class="badge text-bg-primary rounded-pill mb-3">Teacher Portal</span>
                        <h2 class="fw-bold mb-2">Welcome, <?php echo e($user['full_name']); ?></h2>
                        <p class="text-muted mb-0">
                            Use this dashboard to manage attendance, exams, and class communication. Your notifications and shortcuts are right here.
                        </p>
                    </div>

                    <div class="text-md-end">
                        <div class="display-6 fw-bold mb-1"><?php echo number_format($unreadNotifications); ?></div>
                        <div class="text-muted">Unread notifications</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <a href="<?php echo e(url('?page=teacher.attendance')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">Attendance</div>
                                <div class="small text-muted">Mark class attendance quickly.</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <a href="<?php echo e(url('?page=teacher.exams')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">Exams & Results</div>
                                <div class="small text-muted">Create exams and enter marks.</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <a href="<?php echo e(url('?page=teacher.announcements')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">Announcements</div>
                                <div class="small text-muted">Share class updates and notices.</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <a href="<?php echo e(url('?page=teacher.notifications')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">Notifications</div>
                                <div class="small text-muted">Keep track of new alerts.</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Unread Notifications',
            'value' => number_format($unreadNotifications),
            'hint' => 'Needs your attention',
            'icon' => 'fa-regular fa-bell',
            'iconTone' => 'warning',
        ]); ?>

        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h3 class="h6 fw-semibold mb-3">Quick tips</h3>
                <ul class="mb-0 text-muted small ps-3">
                    <li>Use attendance first, then exams and results.</li>
                    <li>Keep announcements short and clear.</li>
                    <li>Check notifications regularly so nothing slips through like a quiet student in assembly.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require app_path('views/layouts/admin.php');

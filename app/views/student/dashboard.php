<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Student Dashboard';
$pageSubtitle = 'Check your results, payments, announcements, and notifications.';
$pageActions = implode('', [
    '<a href="' . e(url('?page=student.profile')) . '" class="btn btn-outline-primary rounded-4"><i class="fa-solid fa-id-card me-2"></i>Profile</a>',
    '<a href="' . e(url('?page=student.my-results')) . '" class="btn btn-outline-success rounded-4"><i class="fa-solid fa-square-poll-vertical me-2"></i>Results</a>',
    '<a href="' . e(url('?page=student.my-payments')) . '" class="btn btn-outline-warning rounded-4"><i class="fa-solid fa-coins me-2"></i>Payments</a>',
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
                        <span class="badge text-bg-primary rounded-pill mb-3">Student Portal</span>
                        <h2 class="fw-bold mb-2">Welcome, <?php echo e($user['full_name']); ?></h2>
                        <p class="text-muted mb-0">
                            Keep up with your announcements, results, and payments from one place.
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
                        <a href="<?php echo e(url('?page=student.my-results')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">My Results</div>
                                <div class="small text-muted">View exam results.</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <a href="<?php echo e(url('?page=student.my-payments')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">My Payments</div>
                                <div class="small text-muted">Check balances and receipts.</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <a href="<?php echo e(url('?page=student.announcements')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">Announcements</div>
                                <div class="small text-muted">Read school updates.</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <a href="<?php echo e(url('?page=student.notifications')); ?>" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-1">Notifications</div>
                                <div class="small text-muted">Stay on top of alerts.</div>
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
            'hint' => 'Messages waiting for you',
            'icon' => 'fa-regular fa-bell',
            'iconTone' => 'warning',
        ]); ?>

        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h3 class="h6 fw-semibold mb-3">Quick tips</h3>
                <ul class="mb-0 text-muted small ps-3">
                    <li>Check results after each exam period.</li>
                    <li>Keep payments updated to avoid surprises.</li>
                    <li>Open notifications often; silence is not the same as empty.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require app_path('views/layouts/admin.php');

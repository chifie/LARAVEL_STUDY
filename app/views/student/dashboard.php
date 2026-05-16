<?php
$user = Auth::user();
$appName = app_config('app_name', 'School SMS');
$unreadNotifications = Notification::unreadCountForUser(Auth::id());
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h4 mb-1">Student Dashboard</h1>
                    <p class="text-muted mb-0">Welcome, <?php echo e($user['full_name']); ?></p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?php echo e(url('?page=student.profile')); ?>" class="btn btn-outline-primary">My Profile</a>
                    <a href="<?php echo e(url('?page=student.history')); ?>" class="btn btn-outline-dark">My History</a>
                    <a href="<?php echo e(url('?page=student.my-results')); ?>" class="btn btn-outline-success">My Results</a>
                    <a href="<?php echo e(url('?page=student.my-payments')); ?>" class="btn btn-outline-warning">My Payments</a>
                    <a href="<?php echo e(url('?page=student.announcements')); ?>" class="btn btn-outline-secondary">Announcements</a>
                    <a href="<?php echo e(url('?page=student.notifications')); ?>" class="btn btn-outline-warning">
                        Notifications <?php if ($unreadNotifications > 0): ?><span class="badge text-bg-danger"><?php echo (int) $unreadNotifications; ?></span><?php endif; ?>
                    </a>
                    <form method="post" action="<?php echo e(url('logout.php')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-outline-danger">Logout</button>
                    </form>
                </div>
            </div>
            <hr>
            <p class="mb-0">Your student dashboard will later show announcements and attendance too.</p>
        </div>
    </div>
</div>
</body>
</html>
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
    <title><?php echo e($appName); ?> - Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h4 mb-1">Teacher Dashboard</h1>
                    <p class="text-muted mb-0">Welcome, <?php echo e($user['full_name']); ?></p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?php echo e(url('?page=teacher.attendance')); ?>" class="btn btn-outline-primary">Attendance</a>
                    <a href="<?php echo e(url('?page=teacher.exams')); ?>" class="btn btn-outline-dark">Exams & Results</a>
                    <a href="<?php echo e(url('?page=teacher.announcements')); ?>" class="btn btn-outline-secondary">Announcements</a>
                    <a href="<?php echo e(url('?page=teacher.notifications')); ?>" class="btn btn-outline-warning">
                        Notifications <?php if ($unreadNotifications > 0): ?><span class="badge text-bg-danger"><?php echo (int) $unreadNotifications; ?></span><?php endif; ?>
                    </a>
                    <a href="<?php echo e(url('?page=student.profile')); ?>" class="btn btn-outline-secondary">My Profile</a>
                    <form method="post" action="<?php echo e(url('logout.php')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-outline-danger">Logout</button>
                    </form>
                </div>
            </div>
            <hr>
            <p class="mb-0">Your teacher dashboard will later show assigned classes, subjects, results, and reports.</p>
        </div>
    </div>
</div>
</body>
</html>
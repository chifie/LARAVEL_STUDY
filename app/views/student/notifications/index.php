<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Student Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1100px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Notifications</h1>
            <p class="text-muted mb-0">Messages for your student account.</p>
        </div>
        <div class="d-flex gap-2">
            <form method="post" action="<?php echo e(url('?page=notification.read-all')); ?>" class="d-inline">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline-primary">Mark All Read</button>
            </form>
            <a href="<?php echo e(url('?page=student.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Unread</div>
                <div class="h3 mb-0"><?php echo (int) $unreadCount; ?></div>
            </div></div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Account</div>
                <div class="h5 mb-0"><?php echo e($user['full_name']); ?></div>
            </div></div>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($notifications as $notification): ?>
            <div class="col-12">
                <div class="card shadow-sm <?php echo ((int) $notification['is_read'] === 0) ? 'border-primary' : ''; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h2 class="h6 mb-0"><?php echo e($notification['title']); ?></h2>
                                    <?php if ((int) $notification['is_read'] === 0): ?>
                                        <span class="badge text-bg-primary">Unread</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Read</span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-2"><?php echo nl2br(e($notification['message'])); ?></p>
                                <div class="small text-muted"><?php echo e($notification['created_at']); ?></div>
                            </div>

                            <?php if ((int) $notification['is_read'] === 0): ?>
                                <form method="post" action="<?php echo e(url('?page=notification.read')); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="notification_id" value="<?php echo (int) $notification['id']; ?>">
                                    <button class="btn btn-sm btn-outline-primary">Mark Read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($notifications)): ?>
            <div class="col-12">
                <div class="alert alert-info mb-0">No notifications yet.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.announcements.create')); ?>" class="btn btn-light btn-sm">Create Announcement</a>
            <a href="<?php echo e(url('?page=admin.notifications')); ?>" class="btn btn-light btn-sm">Notifications</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 1400px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Announcements</h1>
            <p class="text-muted mb-0">Create drafts or publish messages to the school, teachers, students, or one class.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Audience</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Creator</th>
                        <th>Published</th>
                        <th>Notifications</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $row): ?>
                        <tr>
                            <td>
                                <?php echo e($row['title']); ?>
                                <div class="small text-muted"><?php echo e(mb_strimwidth($row['message'], 0, 80, '...')); ?></div>
                            </td>
                            <td><span class="badge text-bg-secondary"><?php echo e($row['audience']); ?></span></td>
                            <td><?php echo e($row['class_name'] ?? '—'); ?></td>
                            <td>
                                <?php if ((int) $row['is_published'] === 1): ?>
                                    <span class="badge text-bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge text-bg-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($row['creator_name'] ?? '—'); ?></td>
                            <td><?php echo e($row['published_at'] ?? '—'); ?></td>
                            <td><?php echo (int) $row['notification_count']; ?></td>
                            <td class="text-end">
                                <?php if ((int) $row['is_published'] === 0): ?>
                                    <form method="post" action="<?php echo e(url('?page=admin.announcements.publish')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="announcement_id" value="<?php echo (int) $row['id']; ?>">
                                        <input type="hidden" name="announcement_action" value="publish">
                                        <button class="btn btn-sm btn-outline-primary">Publish</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Already published</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No announcements yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
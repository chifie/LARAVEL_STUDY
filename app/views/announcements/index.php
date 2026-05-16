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
<div class="container py-4" style="max-width: 1100px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Announcements</h1>
            <p class="text-muted mb-0">Messages for your account.</p>
        </div>
        <a href="<?php echo e(Auth::dashboardUrl()); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <?php foreach ($announcements as $row): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h2 class="h5 mb-1"><?php echo e($row['title']); ?></h2>
                                <div class="small text-muted mb-2">
                                    By <?php echo e($row['creator_name'] ?? '—'); ?>
                                    | <?php echo e($row['published_at'] ?? $row['created_at']); ?>
                                    | Audience: <?php echo e($row['audience']); ?>
                                    <?php if (!empty($row['class_name'])): ?>
                                        | Class: <?php echo e($row['class_name']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ((int) $row['is_published'] === 1): ?>
                                <span class="badge text-bg-success">Published</span>
                            <?php endif; ?>
                        </div>
                        <p class="mb-0"><?php echo nl2br(e($row['message'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($announcements)): ?>
            <div class="col-12">
                <div class="alert alert-info mb-0">No announcements available.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Backups</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Backups</h1>
            <p class="text-muted mb-0">Create and download SQL backup files.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.reports')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="<?php echo e(url('?page=admin.reports.backups')); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-primary">Create New Backup</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>File</th>
                        <th>Size</th>
                        <th>Modified</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                        <tr>
                            <td><?php echo e($backup['name']); ?></td>
                            <td><?php echo number_format(((float) $backup['size']) / 1024, 2); ?> KB</td>
                            <td><?php echo e($backup['modified_at']); ?></td>
                            <td class="text-end">
                                <a href="<?php echo e(url('?page=admin.reports.backups.download&file=' . urlencode($backup['name']))); ?>" class="btn btn-sm btn-outline-primary">Download</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($backups)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No backup files found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
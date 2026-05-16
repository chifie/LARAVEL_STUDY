<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Reports & Logs</h1>
            <p class="text-muted mb-0">Final control layer for school operations.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Academic Report</h2>
                    <p class="text-muted">Student performance by class, term, and year.</p>
                    <a href="<?php echo e(url('?page=admin.reports.academic')); ?>" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Attendance Report</h2>
                    <p class="text-muted">Attendance summary by class and date range.</p>
                    <a href="<?php echo e(url('?page=admin.reports.attendance')); ?>" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Payment Report</h2>
                    <p class="text-muted">Finance report by year, term, and class.</p>
                    <a href="<?php echo e(url('?page=admin.payments.report')); ?>" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Activity Logs</h2>
                    <p class="text-muted">Track who did what and when.</p>
                    <a href="<?php echo e(url('?page=admin.reports.logs')); ?>" class="btn btn-dark">Open</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Backups</h2>
                    <p class="text-muted">Create SQL backups and download them.</p>
                    <a href="<?php echo e(url('?page=admin.reports.backups')); ?>" class="btn btn-dark">Open</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
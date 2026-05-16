<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - My History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1100px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">My History</h1>
            <p class="text-muted mb-0">Your school enrollment history.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=student.profile')); ?>" class="btn btn-outline-dark">My Profile</a>
            <a href="<?php echo e(url('?page=student.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Name:</strong> <?php echo e($student['full_name']); ?></div>
                <div class="col-md-4"><strong>Admission No:</strong> <?php echo e($student['admission_number']); ?></div>
                <div class="col-md-4"><strong>Current Class:</strong> <?php echo e($student['class_name'] ?? '—'); ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Year</th>
                        <th>Term</th>
                        <th>Class</th>
                        <th>Grade</th>
                        <th>Stream</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td><?php echo e($row['year_name']); ?></td>
                            <td><?php echo e($row['term_name']); ?></td>
                            <td><?php echo e($row['class_name']); ?></td>
                            <td><?php echo e($row['grade_level'] ?? '—'); ?></td>
                            <td><?php echo e($row['stream'] ?? '—'); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo e($row['status']); ?></span></td>
                            <td><?php echo e($row['enrollment_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No history found yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
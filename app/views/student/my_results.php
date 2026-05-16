<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - My Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">My Results</h1>
            <p class="text-muted mb-0">Your exam results and grades.</p>
        </div>
        <a href="<?php echo e(url('?page=student.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
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
                        <th>Exam</th>
                        <th>Subject</th>
                        <th>Class</th>
                        <th>Year</th>
                        <th>Term</th>
                        <th>Marks</th>
                        <th>Total</th>
                        <th>Grade</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo e($row['exam_name']); ?></td>
                            <td><?php echo e($row['subject_name']); ?> (<?php echo e($row['subject_code']); ?>)</td>
                            <td><?php echo e($row['class_name']); ?></td>
                            <td><?php echo e($row['year_name']); ?></td>
                            <td><?php echo e($row['term_name']); ?></td>
                            <td><?php echo number_format((float) $row['marks_obtained'], 2); ?></td>
                            <td><?php echo number_format((float) $row['total_marks'], 2); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo e($row['grade']); ?></span></td>
                            <td><?php echo e($row['remarks']); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No results available yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
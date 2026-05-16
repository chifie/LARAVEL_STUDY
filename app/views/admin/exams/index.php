<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Exams</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.exams.create')); ?>" class="btn btn-light btn-sm">Create Exam</a>
            <a href="<?php echo e(url('?page=admin.exams.results')); ?>" class="btn btn-light btn-sm">Results</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Exams and Results</h1>
            <p class="text-muted mb-0">Create exams, enter marks, and view results.</p>
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
                        <th>Exam</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Year</th>
                        <th>Term</th>
                        <th>Date</th>
                        <th>Total Marks</th>
                        <th>Created By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td><?php echo e($exam['exam_name']); ?></td>
                            <td><?php echo e($exam['class_name']); ?></td>
                            <td><?php echo e($exam['subject_name']); ?> (<?php echo e($exam['subject_code']); ?>)</td>
                            <td><?php echo e($exam['year_name']); ?></td>
                            <td><?php echo e($exam['term_name']); ?></td>
                            <td><?php echo e($exam['exam_date'] ?? '—'); ?></td>
                            <td><?php echo e($exam['total_marks']); ?></td>
                            <td><?php echo e($exam['creator_name'] ?? '—'); ?></td>
                            <td class="text-end">
                                <a href="<?php echo e(url('?page=admin.exams.marks&exam_id=' . (int) $exam['id'])); ?>" class="btn btn-sm btn-outline-primary">Enter Marks</a>
                                <a href="<?php echo e(url('?page=admin.exams.results&exam_id=' . (int) $exam['id'])); ?>" class="btn btn-sm btn-outline-dark">View Results</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($exams)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No exams created yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
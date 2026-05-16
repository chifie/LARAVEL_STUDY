<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Exam Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Exam Results</h1>
            <p class="text-muted mb-0">
                <?php echo e($exam['exam_name']); ?> | <?php echo e($exam['class_name']); ?> | <?php echo e($exam['subject_name']); ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.exams.marks&exam_id=' . (int) $exam['id'])); ?>" class="btn btn-outline-primary">Enter Marks</a>
            <a href="<?php echo e(url('?page=admin.exams')); ?>" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Students</div><div class="h3 mb-0"><?php echo (int) $summary['total_students']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Average</div><div class="h3 mb-0"><?php echo number_format((float) $average, 2); ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Highest</div><div class="h3 mb-0"><?php echo number_format((float) $summary['highest_marks'], 2); ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Lowest</div><div class="h3 mb-0"><?php echo number_format((float) $summary['lowest_marks'], 2); ?></div></div></div></div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 text-center">
                <div class="col-2"><strong>A</strong><br><?php echo (int) $summary['grade_a']; ?></div>
                <div class="col-2"><strong>B</strong><br><?php echo (int) $summary['grade_b']; ?></div>
                <div class="col-2"><strong>C</strong><br><?php echo (int) $summary['grade_c']; ?></div>
                <div class="col-2"><strong>D</strong><br><?php echo (int) $summary['grade_d']; ?></div>
                <div class="col-2"><strong>E</strong><br><?php echo (int) $summary['grade_e']; ?></div>
                <div class="col-2"><strong>F</strong><br><?php echo (int) $summary['grade_f']; ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Remark</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo e($row['full_name']); ?></td>
                            <td><?php echo e($row['admission_number']); ?></td>
                            <td><?php echo number_format((float) $row['marks_obtained'], 2); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo e($row['grade']); ?></span></td>
                            <td><?php echo e($row['remarks']); ?></td>
                            <td><?php echo e($exam['creator_name'] ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No results recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Enter Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Enter Marks</h1>
            <p class="text-muted mb-0">
                <?php echo e($exam['exam_name']); ?> | <?php echo e($exam['class_name']); ?> | <?php echo e($exam['subject_name']); ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=teacher.exams.results&exam_id=' . (int) $exam['id'])); ?>" class="btn btn-outline-dark">View Results</a>
            <a href="<?php echo e(url('?page=teacher.exams')); ?>" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>Year:</strong> <?php echo e($exam['year_name']); ?></div>
                <div class="col-md-3"><strong>Term:</strong> <?php echo e($exam['term_name']); ?></div>
                <div class="col-md-3"><strong>Date:</strong> <?php echo e($exam['exam_date'] ?? '—'); ?></div>
                <div class="col-md-3"><strong>Total Marks:</strong> <?php echo e($exam['total_marks']); ?></div>
            </div>
        </div>
    </div>

    <?php if (empty($students)): ?>
        <div class="alert alert-info">No active students found in this class.</div>
    <?php else: ?>
        <form method="post" action="<?php echo e(url('?page=teacher.exams.marks&exam_id=' . (int) $exam['id'])); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="exam_id" value="<?php echo (int) $exam['id']; ?>">

            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Admission No.</th>
                                <th>Marks Obtained</th>
                                <th>Current Grade</th>
                                <th>Current Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <?php
                                    $sid = (int) $student['student_id'];
                                    $existing = $resultsByStudent[$sid] ?? null;
                                ?>
                                <tr>
                                    <td><?php echo e($student['full_name']); ?></td>
                                    <td><?php echo e($student['admission_number']); ?></td>
                                    <td style="max-width: 180px;">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="<?php echo e($exam['total_marks']); ?>"
                                            name="marks[<?php echo $sid; ?>]"
                                            class="form-control form-control-sm"
                                            value="<?php echo e($existing['marks_obtained'] ?? ''); ?>"
                                            placeholder="Enter marks"
                                        >
                                    </td>
                                    <td><span class="badge text-bg-secondary"><?php echo e($existing['grade'] ?? '—'); ?></span></td>
                                    <td><?php echo e($existing['remarks'] ?? '—'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Save Marks</button>
                <a href="<?php echo e(url('?page=teacher.exams.results&exam_id=' . (int) $exam['id'])); ?>" class="btn btn-outline-dark">View Results</a>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
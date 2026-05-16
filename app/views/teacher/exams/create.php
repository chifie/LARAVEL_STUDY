<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Create Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1100px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Create Exam</h1>
            <p class="text-muted mb-0">Choose one of your assigned class-subject combinations.</p>
        </div>
        <a href="<?php echo e(url('?page=teacher.exams')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo e(url('?page=teacher.exams.create')); ?>">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Class / Subject Assignment</label>
                        <select name="assignment_id" class="form-select" required>
                            <option value="">Select assignment</option>
                            <?php foreach ($assignments as $assignment): ?>
                                <option value="<?php echo (int) $assignment['id']; ?>" <?php echo old('assignment_id') == $assignment['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($assignment['year_name']); ?> | <?php echo e($assignment['term_name']); ?> | <?php echo e($assignment['class_name']); ?> | <?php echo e($assignment['subject_name']); ?> (<?php echo e($assignment['subject_code']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Exam Name</label>
                        <input type="text" name="exam_name" class="form-control" value="<?php echo e(old('exam_name')); ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Exam Date</label>
                        <input type="date" name="exam_date" class="form-control" value="<?php echo e(old('exam_date')); ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Total Marks</label>
                        <input type="number" name="total_marks" class="form-control" step="0.01" min="1" value="<?php echo e(old('total_marks', '100')); ?>" required>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Save Exam</button>
                    <a href="<?php echo e(url('?page=teacher.exams')); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
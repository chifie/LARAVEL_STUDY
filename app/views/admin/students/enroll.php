<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Enroll Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
    </div>
</nav>

<div class="container py-4" style="max-width: 1050px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Enroll Student</h1>
            <p class="text-muted mb-0">Create a class enrollment record and update the student’s current class.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.students')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if ($selectedStudent): ?>
        <div class="alert alert-info">
            Selected student: <strong><?php echo e($selectedStudent['full_name']); ?></strong>
            <?php if (!empty($selectedStudent['class_name'])): ?>
                | Current class: <?php echo e($selectedStudent['class_name']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo e(url('?page=admin.students.enroll')); ?>">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select student</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo (int) $student['id']; ?>" <?php echo old('student_id') == $student['id'] || ($selectedStudent && (int) $selectedStudent['id'] === (int) $student['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($student['full_name']); ?> (<?php echo e($student['admission_number']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo (int) $class['id']; ?>" <?php echo old('class_id') == $class['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($class['class_name']); ?> (<?php echo e($class['year_name']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">Select year</option>
                            <?php foreach ($years as $year): ?>
                                <option value="<?php echo (int) $year['id']; ?>" <?php echo old('academic_year_id') == $year['id'] || ($currentYear && (int) $currentYear['id'] === (int) $year['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($year['year_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Term</label>
                        <select name="term_id" class="form-select" required>
                            <option value="">Select term</option>
                            <?php foreach ($terms as $term): ?>
                                <option value="<?php echo (int) $term['id']; ?>" <?php echo old('term_id') == $term['id'] || ($currentTerm && (int) $currentTerm['id'] === (int) $term['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($term['term_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Enrollment Date</label>
                        <input type="date" name="enrollment_date" class="form-control" value="<?php echo e(old('enrollment_date', date('Y-m-d'))); ?>" required>
                    </div>

                    <input type="hidden" name="status" value="active">
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Enrollment</button>
                    <a href="<?php echo e(url('?page=admin.students')); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
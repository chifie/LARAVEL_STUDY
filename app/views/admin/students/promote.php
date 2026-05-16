<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Promote Student</title>
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
            <h1 class="h4 mb-1">Promote Student</h1>
            <p class="text-muted mb-0">Move a student to the next class while keeping history safe.</p>
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
            <strong><?php echo e($selectedStudent['full_name']); ?></strong>
            <?php if (!empty($selectedStudent['class_name'])): ?>
                | Current class: <?php echo e($selectedStudent['class_name']); ?>
            <?php else: ?>
                | Current class: none
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="<?php echo e(url('?page=admin.students.promote')); ?>">
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
                        <label class="form-label">New Class</label>
                        <select name="new_class_id" class="form-select" required>
                            <option value="">Select new class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo (int) $class['id']; ?>" <?php echo old('new_class_id') == $class['id'] ? 'selected' : ''; ?>>
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
                            <?php foreach ($currentYear ? Term::allByYear((int) $currentYear['id']) : [] as $term): ?>
                                <option value="<?php echo (int) $term['id']; ?>" <?php echo old('term_id') == $term['id'] || ($currentTerm && (int) $currentTerm['id'] === (int) $term['id']) ? 'selected' : ''; ?>>
                                    <?php echo e($term['term_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Promotion Date</label>
                        <input type="date" name="promotion_date" class="form-control" value="<?php echo e(old('promotion_date', date('Y-m-d'))); ?>" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Promote Student</button>
                    <a href="<?php echo e(url('?page=admin.students')); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>Recent Enrollment History</strong>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Year</th>
                        <th>Term</th>
                        <th>Class</th>
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
                            <td><span class="badge text-bg-secondary"><?php echo e($row['status']); ?></span></td>
                            <td><?php echo e($row['enrollment_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No history found yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
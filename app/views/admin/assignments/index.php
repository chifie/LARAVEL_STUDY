<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
$currentYearId = $currentYear ? (int) $currentYear['id'] : 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Teacher Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.structure.years')); ?>" class="btn btn-light btn-sm">Academic Years</a>
            <a href="<?php echo e(url('?page=admin.structure.terms')); ?>" class="btn btn-light btn-sm">Terms</a>
            <a href="<?php echo e(url('?page=admin.structure.classes')); ?>" class="btn btn-light btn-sm">Classes</a>
            <a href="<?php echo e(url('?page=admin.structure.subjects')); ?>" class="btn btn-light btn-sm">Subjects</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Teacher Assignments</h1>
            <p class="text-muted mb-0">Assign a teacher to a class and subject for a term.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if (!$currentYear): ?>
        <div class="alert alert-warning">
            Please create and set a current academic year first. Assignments depend on the selected year and term.
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Assign Teacher</h2>
                    <form method="post" action="<?php echo e(url('?page=admin.structure.assignments')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                <option value="">Select academic year</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?php echo (int) $year['id']; ?>" <?php echo old('academic_year_id') == $year['id'] || $currentYearId === (int) $year['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($year['year_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Term</label>
                            <select name="term_id" class="form-select" required>
                                <option value="">Select term</option>
                                <?php foreach ($terms as $term): ?>
                                    <option value="<?php echo (int) $term['id']; ?>" <?php echo old('term_id') == $term['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($term['term_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">The list shows terms for the current academic year.</div>
                        </div>

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">Select subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo (int) $subject['id']; ?>" <?php echo old('subject_id') == $subject['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($subject['subject_name']); ?> (<?php echo e($subject['subject_code']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">No teacher yet</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo (int) $teacher['id']; ?>" <?php echo old('teacher_id') == $teacher['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($teacher['full_name']); ?> (<?php echo e($teacher['staff_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button class="btn btn-primary">Save Assignment</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Year</th>
                                <th>Term</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $row): ?>
                                <tr>
                                    <td><?php echo e($row['year_name']); ?></td>
                                    <td><?php echo e($row['term_name']); ?></td>
                                    <td><?php echo e($row['class_name']); ?></td>
                                    <td><?php echo e($row['subject_name']); ?> (<?php echo e($row['subject_code']); ?>)</td>
                                    <td><?php echo e($row['teacher_name'] ?? '—'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($currentYear): ?>
                <div class="alert alert-info mt-3 mb-0">
                    Current academic year for assignments: <strong><?php echo e($currentYear['year_name']); ?></strong>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.structure.years')); ?>" class="btn btn-light btn-sm">Academic Years</a>
            <a href="<?php echo e(url('?page=admin.structure.terms')); ?>" class="btn btn-light btn-sm">Terms</a>
            <a href="<?php echo e(url('?page=admin.structure.subjects')); ?>" class="btn btn-light btn-sm">Subjects</a>
            <a href="<?php echo e(url('?page=admin.structure.assignments')); ?>" class="btn btn-light btn-sm">Assignments</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Classes</h1>
            <p class="text-muted mb-0">Create classes and optionally assign a class teacher.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Create Class</h2>
                    <form method="post" action="<?php echo e(url('?page=admin.structure.classes')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label">Class Name</label>
                            <input type="text" name="class_name" class="form-control" placeholder="Form One A" value="<?php echo e(old('class_name')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Grade Level</label>
                            <input type="text" name="grade_level" class="form-control" placeholder="Form One" value="<?php echo e(old('grade_level')); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stream</label>
                            <input type="text" name="stream" class="form-control" placeholder="A" value="<?php echo e(old('stream')); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select academic year</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?php echo (int) $year['id']; ?>" <?php echo old('academic_year_id') == $year['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($year['year_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" min="1" value="<?php echo e(old('capacity')); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Class Teacher</label>
                            <select name="class_teacher_id" class="form-select">
                                <option value="">No class teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo (int) $teacher['id']; ?>" <?php echo old('class_teacher_id') == $teacher['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($teacher['full_name']); ?> (<?php echo e($teacher['staff_number']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button class="btn btn-primary">Save Class</button>
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
                                <th>Class</th>
                                <th>Grade</th>
                                <th>Stream</th>
                                <th>Year</th>
                                <th>Teacher</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo e($class['class_name']); ?></td>
                                    <td><?php echo e($class['grade_level'] ?? '—'); ?></td>
                                    <td><?php echo e($class['stream'] ?? '—'); ?></td>
                                    <td><?php echo e($class['year_name']); ?></td>
                                    <td><?php echo e($class['teacher_name'] ?? '—'); ?></td>
                                    <td>
                                        <?php if ($class['status'] === 'active'): ?>
                                            <span class="badge text-bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="<?php echo e(url('?page=admin.structure.classes.toggle-status')); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="class_id" value="<?php echo (int) $class['id']; ?>">
                                            <button class="btn btn-sm <?php echo ($class['status'] === 'active') ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                                <?php echo ($class['status'] === 'active') ? 'Deactivate' : 'Activate'; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
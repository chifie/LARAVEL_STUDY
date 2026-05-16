<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.students.enroll')); ?>" class="btn btn-light btn-sm">Enroll Student</a>
            <a href="<?php echo e(url('?page=admin.students.promote')); ?>" class="btn btn-light btn-sm">Promote Student</a>
            <a href="<?php echo e(url('?page=admin.structure.classes')); ?>" class="btn btn-light btn-sm">Classes</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Student Operations</h1>
            <p class="text-muted mb-0">Enroll, promote, view profile, and see history.</p>
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
                        <th>Name</th>
                        <th>Admission No.</th>
                        <th>Email</th>
                        <th>Current Class</th>
                        <th>Status</th>
                        <th>Account</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo e($student['full_name']); ?></td>
                            <td><?php echo e($student['admission_number']); ?></td>
                            <td><?php echo e($student['email']); ?></td>
                            <td>
                                <?php echo e($student['class_name'] ?? '—'); ?>
                                <?php if (!empty($student['year_name'])): ?>
                                    <div class="small text-muted"><?php echo e($student['year_name']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge text-bg-secondary"><?php echo e($student['status']); ?></span>
                            </td>
                            <td>
                                <?php if ((int) $student['is_active'] === 1): ?>
                                    <span class="badge text-bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo e(url('?page=admin.students.enroll&student_id=' . (int) $student['id'])); ?>" class="btn btn-sm btn-outline-primary">Enroll</a>
                                <a href="<?php echo e(url('?page=admin.students.promote&student_id=' . (int) $student['id'])); ?>" class="btn btn-sm btn-outline-success">Promote</a>
                                <a href="<?php echo e(url('?page=admin.students.profile&student_id=' . (int) $student['id'])); ?>" class="btn btn-sm btn-outline-secondary">Profile</a>
                                <a href="<?php echo e(url('?page=admin.students.history&student_id=' . (int) $student['id'])); ?>" class="btn btn-sm btn-outline-dark">History</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
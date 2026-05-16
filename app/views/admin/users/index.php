<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <form method="post" action="<?php echo e(url('logout.php')); ?>" class="d-inline">
            <?php echo csrf_field(); ?>
            <button class="btn btn-light btn-sm">Logout</button>
        </form>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">User List</h1>
            <p class="text-muted mb-0">Activate or deactivate teacher and student accounts.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.users.create-teacher')); ?>" class="btn btn-primary">Create Teacher</a>
            <a href="<?php echo e(url('?page=admin.users.create-student')); ?>" class="btn btn-success">Create Student</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Password Change</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <td><?php echo e($row['full_name']); ?></td>
                            <td><?php echo e($row['email']); ?></td>
                            <td><?php echo e($row['phone'] ?? '—'); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo e($row['role']); ?></span></td>
                            <td>
                                <?php if ((int) $row['must_change_password'] === 1): ?>
                                    <span class="badge text-bg-warning">Yes</span>
                                <?php else: ?>
                                    <span class="badge text-bg-success">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int) $row['is_active'] === 1): ?>
                                    <span class="badge text-bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($row['created_at']); ?></td>
                            <td class="text-end">
                                <form method="post" action="<?php echo e(url('?page=admin.users.toggle-status')); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="user_id" value="<?php echo (int) $row['id']; ?>">
                                    <button
                                        type="submit"
                                        class="btn btn-sm <?php echo ((int) $row['is_active'] === 1) ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                        <?php echo ((int) $row['id'] === (int) Auth::id()) ? 'disabled' : ''; ?>
                                    >
                                        <?php echo ((int) $row['is_active'] === 1) ? 'Deactivate' : 'Activate'; ?>
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
</body>
</html>
<?php
$user = Auth::user();
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
        .stat-card { border: 0; border-radius: 1rem; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06); }
        .nav-pill { border-radius: 999px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="text-white-50 small">Welcome, <?php echo e($user['full_name']); ?></span>
            <form method="post" action="<?php echo e(url('logout.php')); ?>" class="d-inline">
                <?php echo csrf_field(); ?>
                <button class="btn btn-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Super Admin Dashboard</h1>
        <p class="text-muted mb-0">Manage teachers, students, and the school structure from here.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Users</div>
                    <div class="display-6 fw-semibold"><?php echo (int) $stats['users']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Teachers</div>
                    <div class="display-6 fw-semibold"><?php echo (int) $stats['teachers']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Students</div>
                    <div class="display-6 fw-semibold"><?php echo (int) $stats['students']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">Active Users</div>
                    <div class="display-6 fw-semibold"><?php echo (int) $stats['active_users']; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card stat-card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
    <a href="<?php echo e(url('?page=admin.users.create-teacher')); ?>" class="btn btn-primary nav-pill">Create Teacher</a>
    <a href="<?php echo e(url('?page=admin.users.create-student')); ?>" class="btn btn-success nav-pill">Create Student</a>
    <a href="<?php echo e(url('?page=admin.users')); ?>" class="btn btn-outline-secondary nav-pill">User List</a>
    <a href="<?php echo e(url('?page=admin.students')); ?>" class="btn btn-outline-primary nav-pill">Students</a>
    <a href="<?php echo e(url('?page=admin.attendance')); ?>" class="btn btn-outline-primary nav-pill">Attendance</a>
    <a href="<?php echo e(url('?page=admin.exams')); ?>" class="btn btn-outline-primary nav-pill">Exams & Results</a>
    <a href="<?php echo e(url('?page=admin.payments')); ?>" class="btn btn-outline-primary nav-pill">Payments</a>
    <a href="<?php echo e(url('?page=admin.announcements')); ?>" class="btn btn-outline-primary nav-pill">Announcements</a>
    <a href="<?php echo e(url('?page=admin.notifications')); ?>" class="btn btn-outline-primary nav-pill">Notifications</a>
    <a href="<?php echo e(url('?page=admin.structure.years')); ?>" class="btn btn-outline-primary nav-pill">Academic Years</a>
    <a href="<?php echo e(url('?page=admin.structure.terms')); ?>" class="btn btn-outline-primary nav-pill">Terms</a>
    <a href="<?php echo e(url('?page=admin.structure.classes')); ?>" class="btn btn-outline-primary nav-pill">Classes</a>
    <a href="<?php echo e(url('?page=admin.structure.subjects')); ?>" class="btn btn-outline-primary nav-pill">Subjects</a>
    <a href="<?php echo e(url('?page=admin.structure.assignments')); ?>" class="btn btn-outline-primary nav-pill">Assignments</a>
    <a href="<?php echo e(url('?page=admin.reports')); ?>" class="btn btn-outline-primary nav-pill">Reports & Logs</a>
</div>
        </div>
    </div>

    <div class="card stat-card">
        <div class="card-header bg-white">
            <strong>Latest Users</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Password State</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestUsers as $row): ?>
                            <tr>
                                <td><?php echo e($row['full_name']); ?></td>
                                <td><?php echo e($row['email']); ?></td>
                                <td><?php echo e($row['role']); ?></td>
                                <td>
                                    <?php if ((int) $row['is_active'] === 1): ?>
                                        <span class="badge text-bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ((int) $row['must_change_password'] === 1): ?>
                                        <span class="badge text-bg-warning">Must Change</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Changed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($row['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
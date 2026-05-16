<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1600px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Activity Logs</h1>
            <p class="text-muted mb-0">Track important user actions in the system.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.reports')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?php echo e(url('?page=admin.reports.logs')); ?>" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin.reports.logs">
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">All users</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo (int) $u['id']; ?>" <?php echo isset($_GET['user_id']) && (int) $_GET['user_id'] === (int) $u['id'] ? 'selected' : ''; ?>>
                                <?php echo e($u['full_name']); ?> (<?php echo e($u['role']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All actions</option>
                        <?php foreach ($actions as $action): ?>
                            <option value="<?php echo e($action); ?>" <?php echo isset($_GET['action']) && $_GET['action'] === $action ? 'selected' : ''; ?>>
                                <?php echo e($action); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo e($_GET['from_date'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo e($_GET['to_date'] ?? ''); ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-12">
                    <a href="<?php echo e(url('?page=admin.reports.logs')); ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Entity ID</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo e($log['created_at']); ?></td>
                            <td>
                                <?php echo e($log['user_name'] ?? 'System'); ?>
                                <div class="small text-muted"><?php echo e($log['user_email'] ?? '—'); ?></div>
                            </td>
                            <td><span class="badge text-bg-secondary"><?php echo e($log['action']); ?></span></td>
                            <td><?php echo e($log['entity_type'] ?? '—'); ?></td>
                            <td><?php echo e($log['entity_id'] ?? '—'); ?></td>
                            <td><?php echo e($log['description']); ?></td>
                            <td><?php echo e($log['ip_address'] ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
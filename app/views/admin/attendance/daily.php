<?php
$appName = app_config('app_name', 'School SMS');
$selectedClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
$selectedDate = isset($_GET['date']) ? trim((string) $_GET['date']) : date('Y-m-d');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Daily Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.attendance')); ?>" class="btn btn-light btn-sm">Mark Attendance</a>
            <a href="<?php echo e(url('?page=admin.attendance.summary')); ?>" class="btn btn-light btn-sm">Summary</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 1200px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Daily Attendance View</h1>
            <p class="text-muted mb-0">See one class attendance list for one date.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.attendance')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?php echo e(url('?page=admin.attendance.daily')); ?>" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin.attendance.daily">
                <div class="col-md-5">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select" required>
                        <option value="">Select class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo (int) $class['id']; ?>" <?php echo $selectedClassId == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo e($class['class_name']); ?> (<?php echo e($class['year_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo e($selectedDate); ?>" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Load Report</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Present</div><div class="h3 mb-0"><?php echo (int) $counts['present_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Absent</div><div class="h3 mb-0"><?php echo (int) $counts['absent_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Late</div><div class="h3 mb-0"><?php echo (int) $counts['late_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Excused</div><div class="h3 mb-0"><?php echo (int) $counts['excused_count']; ?></div></div></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>
                <?php echo $class ? e($class['class_name']) : 'No class selected'; ?>
                <?php if ($class): ?>
                    <span class="text-muted">| <?php echo e($selectedDate); ?></span>
                <?php endif; ?>
            </strong>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Marked At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?php echo e($row['full_name']); ?></td>
                            <td><?php echo e($row['admission_number']); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo e($row['status']); ?></span></td>
                            <td><?php echo e($row['remarks'] ?? '—'); ?></td>
                            <td><?php echo e($row['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No attendance records found for this date.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
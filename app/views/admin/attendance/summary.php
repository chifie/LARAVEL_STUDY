<?php
$appName = app_config('app_name', 'School SMS');
$selectedClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
$fromDate = isset($_GET['from_date']) ? trim((string) $_GET['from_date']) : date('Y-m-01');
$toDate = isset($_GET['to_date']) ? trim((string) $_GET['to_date']) : date('Y-m-d');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Attendance Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.attendance')); ?>" class="btn btn-light btn-sm">Mark Attendance</a>
            <a href="<?php echo e(url('?page=admin.attendance.daily')); ?>" class="btn btn-light btn-sm">Daily View</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 1200px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Attendance Summary</h1>
            <p class="text-muted mb-0">See attendance counts across a date range.</p>
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
            <form method="get" action="<?php echo e(url('?page=admin.attendance.summary')); ?>" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin.attendance.summary">
                <div class="col-md-4">
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
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo e($fromDate); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo e($toDate); ?>" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Load</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>
                <?php echo $class ? e($class['class_name']) : 'No class selected'; ?>
            </strong>
            <span class="text-muted"><?php echo e($fromDate); ?> to <?php echo e($toDate); ?></span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Late</th>
                        <th>Excused</th>
                        <th>Total Marked Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summary as $row): ?>
                        <tr>
                            <td><?php echo e($row['full_name']); ?></td>
                            <td><?php echo e($row['admission_number']); ?></td>
                            <td><?php echo (int) $row['present_count']; ?></td>
                            <td><?php echo (int) $row['absent_count']; ?></td>
                            <td><?php echo (int) $row['late_count']; ?></td>
                            <td><?php echo (int) $row['excused_count']; ?></td>
                            <td><?php echo (int) $row['total_days']; ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($summary)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No summary data found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
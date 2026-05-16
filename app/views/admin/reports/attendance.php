<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Attendance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1500px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Attendance Report</h1>
            <p class="text-muted mb-0">Attendance summary by class and date range.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.reports')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?php echo e(url('?page=admin.reports.attendance')); ?>" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin.reports.attendance">
                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All years</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?php echo (int) $year['id']; ?>" <?php echo $filters['academic_year_id'] == $year['id'] ? 'selected' : ''; ?>>
                                <?php echo e($year['year_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo (int) $class['id']; ?>" <?php echo $filters['class_id'] == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo e($class['class_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo e($filters['from_date']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo e($filters['to_date']); ?>" required>
                </div>
                <div class="col-md-12 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="<?php echo e(url('?page=admin.reports.attendance')); ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Students</div><div class="h3 mb-0"><?php echo (int) $summary['student_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Present</div><div class="h3 mb-0"><?php echo (int) $summary['present_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Absent</div><div class="h3 mb-0"><?php echo (int) $summary['absent_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Late / Excused</div><div class="h3 mb-0"><?php echo (int) $summary['late_count']; ?> / <?php echo (int) $summary['excused_count']; ?></div></div></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Class</th>
                        <th>Year</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Late</th>
                        <th>Excused</th>
                        <th>Total Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td><?php echo e($row['full_name']); ?></td>
                            <td><?php echo e($row['admission_number']); ?></td>
                            <td><?php echo e($row['class_name'] ?? '—'); ?></td>
                            <td><?php echo e($row['year_name'] ?? '—'); ?></td>
                            <td><?php echo (int) $row['present_count']; ?></td>
                            <td><?php echo (int) $row['absent_count']; ?></td>
                            <td><?php echo (int) $row['late_count']; ?></td>
                            <td><?php echo (int) $row['excused_count']; ?></td>
                            <td><?php echo (int) $row['total_days']; ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No attendance data found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
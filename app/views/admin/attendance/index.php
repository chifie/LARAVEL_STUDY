<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
$selectedClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
$selectedDate = isset($_GET['date']) ? trim((string) $_GET['date']) : date('Y-m-d');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.attendance.daily')); ?>" class="btn btn-light btn-sm">Daily View</a>
            <a href="<?php echo e(url('?page=admin.attendance.summary')); ?>" class="btn btn-light btn-sm">Summary</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 1200px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Mark Attendance</h1>
            <p class="text-muted mb-0">Choose a class and date, then mark each student.</p>
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
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="get" action="<?php echo e(url('?page=admin.attendance')); ?>">
                        <input type="hidden" name="page" value="admin.attendance">
                        <div class="mb-3">
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
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo e($selectedDate); ?>" required>
                        </div>
                        <button class="btn btn-outline-primary w-100">Load Register</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h6 text-muted">Daily Counts</h2>
                    <div class="d-flex justify-content-between"><span>Present</span><strong><?php echo (int) $counts['present_count']; ?></strong></div>
                    <div class="d-flex justify-content-between"><span>Absent</span><strong><?php echo (int) $counts['absent_count']; ?></strong></div>
                    <div class="d-flex justify-content-between"><span>Late</span><strong><?php echo (int) $counts['late_count']; ?></strong></div>
                    <div class="d-flex justify-content-between"><span>Excused</span><strong><?php echo (int) $counts['excused_count']; ?></strong></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Total records</span><strong><?php echo (int) $counts['total_count']; ?></strong></div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>
                        <?php echo $class ? e($class['class_name']) : 'No class selected'; ?>
                        <?php if ($class): ?>
                            <span class="text-muted">| <?php echo e($selectedDate); ?></span>
                        <?php endif; ?>
                    </strong>
                </div>
                <div class="card-body">
                    <?php if (!$class): ?>
                        <div class="alert alert-warning mb-0">Select a class to load the attendance register.</div>
                    <?php elseif (empty($students)): ?>
                        <div class="alert alert-info mb-0">No active students found in this class.</div>
                    <?php else: ?>
                        <form method="post" action="<?php echo e(url('?page=admin.attendance')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="class_id" value="<?php echo (int) $selectedClassId; ?>">
                            <input type="hidden" name="attendance_date" value="<?php echo e($selectedDate); ?>">

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Admission No.</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <?php
                                                $sid = (int) $student['student_id'];
                                                $existing = $records[$sid] ?? null;
                                                $currentStatus = $existing['status'] ?? 'present';
                                                $currentRemark = $existing['remarks'] ?? '';
                                            ?>
                                            <tr>
                                                <td><?php echo e($student['full_name']); ?></td>
                                                <td><?php echo e($student['admission_number']); ?></td>
                                                <td style="min-width: 180px;">
                                                    <select name="status[<?php echo $sid; ?>]" class="form-select form-select-sm">
                                                        <option value="present" <?php echo $currentStatus === 'present' ? 'selected' : ''; ?>>Present</option>
                                                        <option value="absent" <?php echo $currentStatus === 'absent' ? 'selected' : ''; ?>>Absent</option>
                                                        <option value="late" <?php echo $currentStatus === 'late' ? 'selected' : ''; ?>>Late</option>
                                                        <option value="excused" <?php echo $currentStatus === 'excused' ? 'selected' : ''; ?>>Excused</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="remarks[<?php echo $sid; ?>]" class="form-control form-control-sm" value="<?php echo e($currentRemark); ?>" placeholder="Optional note">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <button class="btn btn-primary">Save Attendance</button>
                                <a href="<?php echo e(url('?page=admin.attendance.daily&class_id=' . (int) $selectedClassId . '&date=' . urlencode($selectedDate))); ?>" class="btn btn-outline-secondary">View Daily Report</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
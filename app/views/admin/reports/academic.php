<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Academic Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1500px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Academic Report</h1>
            <p class="text-muted mb-0">Student performance across exams.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.reports')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?php echo e(url('?page=admin.reports.academic')); ?>" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin.reports.academic">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label class="form-label">Term</label>
                    <select name="term_id" class="form-select">
                        <option value="">All terms</option>
                        <?php foreach ($terms as $term): ?>
                            <option value="<?php echo (int) $term['id']; ?>" <?php echo $filters['term_id'] == $term['id'] ? 'selected' : ''; ?>>
                                <?php echo e($term['term_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-12 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="<?php echo e(url('?page=admin.reports.academic')); ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Students</div><div class="h3 mb-0"><?php echo (int) $summary['student_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Average %</div><div class="h3 mb-0"><?php echo number_format((float) $summary['average_percentage'], 2); ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Pass</div><div class="h3 mb-0"><?php echo (int) $summary['pass_count']; ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Fail</div><div class="h3 mb-0"><?php echo (int) $summary['fail_count']; ?></div></div></div></div>
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
                        <th>Exams</th>
                        <th>Total Obtained</th>
                        <th>Total Possible</th>
                        <th>Overall %</th>
                        <th>Grade</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td><?php echo e($row['full_name']); ?></td>
                            <td><?php echo e($row['admission_number']); ?></td>
                            <td><?php echo e($row['class_name'] ?? '—'); ?></td>
                            <td><?php echo e($row['year_name'] ?? '—'); ?></td>
                            <td><?php echo (int) $row['exam_count']; ?></td>
                            <td><?php echo number_format((float) $row['total_obtained'], 2); ?></td>
                            <td><?php echo number_format((float) $row['total_possible'], 2); ?></td>
                            <td><?php echo number_format((float) $row['overall_percentage'], 2); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo e($row['overall_grade']); ?></span></td>
                            <td><?php echo e($row['overall_remark']); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No academic data found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
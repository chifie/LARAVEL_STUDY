<?php
$appName = app_config('app_name', 'School SMS');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - My Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1400px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">My Payments</h1>
            <p class="text-muted mb-0">Your fee payment history and balance.</p>
        </div>
        <a href="<?php echo e(url('?page=student.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Name:</strong> <?php echo e($student['full_name']); ?></div>
                <div class="col-md-4"><strong>Admission No:</strong> <?php echo e($student['admission_number']); ?></div>
                <div class="col-md-4"><strong>Current Class:</strong> <?php echo e($student['class_name'] ?? '—'); ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Payments</div>
                <div class="h3 mb-0"><?php echo (int) $summary['payment_count']; ?></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Total Due</div>
                <div class="h3 mb-0"><?php echo number_format((float) $summary['total_due'], 2); ?></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Total Paid</div>
                <div class="h3 mb-0"><?php echo number_format((float) $summary['total_paid'], 2); ?></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Balance</div>
                <div class="h3 mb-0"><?php echo number_format((float) $summary['total_balance'], 2); ?></div>
            </div></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fee</th>
                        <th>Class</th>
                        <th>Year</th>
                        <th>Term</th>
                        <th>Due</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Date</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td><?php echo e($row['fee_name'] ?? '—'); ?></td>
                            <td><?php echo e($row['class_name'] ?? '—'); ?></td>
                            <td><?php echo e($row['year_name']); ?></td>
                            <td><?php echo e($row['term_name']); ?></td>
                            <td><?php echo number_format((float) $row['amount_due'], 2); ?></td>
                            <td><?php echo number_format((float) $row['amount_paid'], 2); ?></td>
                            <td><?php echo number_format((float) $row['balance'], 2); ?></td>
                            <td><?php echo e($row['payment_date']); ?></td>
                            <td><?php echo e($row['receipt_number'] ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No payments recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
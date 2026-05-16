<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.payments.report')); ?>" class="btn btn-light btn-sm">Reports</a>
            <a href="<?php echo e(url('?page=admin.students')); ?>" class="btn btn-light btn-sm">Students</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 1400px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Payments</h1>
            <p class="text-muted mb-0">Set fees, record payments, and track balances.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Payments</div>
                <div class="h3 mb-0"><?php echo (int) $overview['payment_count']; ?></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Total Due</div>
                <div class="h3 mb-0"><?php echo number_format((float) $overview['total_due'], 2); ?></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Total Paid</div>
                <div class="h3 mb-0"><?php echo number_format((float) $overview['total_paid'], 2); ?></div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Balance</div>
                <div class="h3 mb-0"><?php echo number_format((float) $overview['total_balance'], 2); ?></div>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Fee Setup</h2>
                    <form method="post" action="<?php echo e(url('?page=admin.payments')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="payment_action" value="fee">

                        <div class="mb-3">
                            <label class="form-label">Fee Name</label>
                            <input type="text" name="fee_name" class="form-control" value="<?php echo e(old('fee_name')); ?>" placeholder="School Fees" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Academic Year</label>
                                <select name="academic_year_id" class="form-select" required>
                                    <option value="">Select</option>
                                    <?php foreach ($years as $year): ?>
                                        <option value="<?php echo (int) $year['id']; ?>" <?php echo old('academic_year_id') == $year['id'] ? 'selected' : ''; ?>>
                                            <?php echo e($year['year_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Term</label>
                                <select name="term_id" class="form-select" required>
                                    <option value="">Select</option>
                                    <?php foreach ($terms as $term): ?>
                                        <option value="<?php echo (int) $term['id']; ?>" <?php echo old('term_id') == $term['id'] ? 'selected' : ''; ?>>
                                            <?php echo e($term['term_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Class</label>
                                <select name="class_id" class="form-select" required>
                                    <option value="">Select</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo (int) $class['id']; ?>" <?php echo old('class_id') == $class['id'] ? 'selected' : ''; ?>>
                                            <?php echo e($class['class_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label class="form-label">Amount Due</label>
                                <input type="number" name="amount_due" class="form-control" step="0.01" min="0" value="<?php echo e(old('amount_due')); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="<?php echo e(old('due_date')); ?>">
                            </div>
                        </div>

                        <button class="btn btn-primary mt-3">Save Fee Setup</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Record Payment</h2>
                    <form method="post" action="<?php echo e(url('?page=admin.payments')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="payment_action" value="payment">

                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Select student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo (int) $student['student_id']; ?>" <?php echo old('student_id') == $student['student_id'] ? 'selected' : ''; ?>>
                                        <?php echo e($student['full_name']); ?> (<?php echo e($student['admission_number']); ?>)
                                        <?php if (!empty($student['class_name'])): ?>
                                            - <?php echo e($student['class_name']); ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fee Structure</label>
                            <select name="fee_structure_id" class="form-select" required>
                                <option value="">Select fee structure</option>
                                <?php foreach ($feeOptions as $fee): ?>
                                    <option value="<?php echo (int) $fee['id']; ?>" <?php echo old('fee_structure_id') == $fee['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($fee['year_name']); ?> | <?php echo e($fee['term_name']); ?> | <?php echo e($fee['class_name']); ?> | <?php echo e($fee['fee_name']); ?> | Due: <?php echo number_format((float) $fee['amount_due'], 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount Paid</label>
                                <input type="number" name="amount_paid" class="form-control" step="0.01" min="0.01" value="<?php echo e(old('amount_paid')); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" value="<?php echo e(old('payment_date', date('Y-m-d'))); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash" <?php echo old('payment_method') === 'cash' ? 'selected' : ''; ?>>Cash</option>
                                    <option value="bank" <?php echo old('payment_method') === 'bank' ? 'selected' : ''; ?>>Bank</option>
                                    <option value="mobile_money" <?php echo old('payment_method') === 'mobile_money' ? 'selected' : ''; ?>>Mobile Money</option>
                                    <option value="other" <?php echo old('payment_method') === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Receipt Number</label>
                                <input type="text" name="receipt_number" class="form-control" value="<?php echo e(old('receipt_number')); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes')); ?></textarea>
                            </div>
                        </div>

                        <button class="btn btn-success mt-3">Save Payment</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong>Fee Structures</strong>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fee Name</th>
                                <th>Class</th>
                                <th>Year</th>
                                <th>Term</th>
                                <th>Amount Due</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feeStructures as $fee): ?>
                                <tr>
                                    <td><?php echo e($fee['fee_name']); ?></td>
                                    <td><?php echo e($fee['class_name']); ?></td>
                                    <td><?php echo e($fee['year_name']); ?></td>
                                    <td><?php echo e($fee['term_name']); ?></td>
                                    <td><?php echo number_format((float) $fee['amount_due'], 2); ?></td>
                                    <td><?php echo e($fee['due_date'] ?? '—'); ?></td>
                                    <td>
                                        <?php if ($fee['status'] === 'active'): ?>
                                            <span class="badge text-bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($feeStructures)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No fee structures created yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Recent Payments</strong>
                    <a href="<?php echo e(url('?page=admin.payments.report')); ?>" class="btn btn-sm btn-outline-primary">View Reports</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Fee</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Balance</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td>
                                        <?php echo e($payment['student_name']); ?>
                                        <div class="small text-muted"><?php echo e($payment['admission_number']); ?></div>
                                    </td>
                                    <td>
                                        <?php echo e($payment['fee_name'] ?? '—'); ?>
                                        <div class="small text-muted"><?php echo e($payment['class_name'] ?? ''); ?></div>
                                    </td>
                                    <td><?php echo number_format((float) $payment['amount_paid'], 2); ?></td>
                                    <td><?php echo number_format((float) $payment['amount_due'], 2); ?></td>
                                    <td><?php echo number_format((float) $payment['balance'], 2); ?></td>
                                    <td><?php echo e($payment['payment_date']); ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($recentPayments)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No payments recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Payments';
$pageSubtitle = 'Set fees, record payments, and track balances in one place.';
$pageActions = implode('', [
    '<a href="' . e(url('?page=admin.payments.report')) . '" class="btn btn-outline-primary rounded-4"><i class="fa-solid fa-chart-column me-2"></i>Reports</a>',
    '<a href="' . e(url('?page=admin.students')) . '" class="btn btn-outline-secondary rounded-4"><i class="fa-solid fa-users me-2"></i>Students</a>',
]);

$overview = $overview ?? [];
$students = $students ?? [];
$classes = $classes ?? [];
$years = $years ?? [];
$terms = $terms ?? [];
$feeOptions = $feeOptions ?? [];
$feeStructures = $feeStructures ?? [];
$recentPayments = $recentPayments ?? [];
$success = $success ?? null;
$error = $error ?? null;

ob_start();
?>
<div class="row g-3 g-xl-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Payments',
            'value' => number_format((int) ($overview['payment_count'] ?? 0)),
            'hint' => 'Recorded payment entries',
            'icon' => 'fa-solid fa-receipt',
            'iconTone' => 'primary',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Total Due',
            'value' => number_format((float) ($overview['total_due'] ?? 0), 2),
            'hint' => 'Total fee obligations',
            'icon' => 'fa-solid fa-circle-dollar-to-slot',
            'iconTone' => 'danger',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Total Paid',
            'value' => number_format((float) ($overview['total_paid'] ?? 0), 2),
            'hint' => 'Cash already received',
            'icon' => 'fa-solid fa-hand-holding-dollar',
            'iconTone' => 'success',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Balance',
            'value' => number_format((float) ($overview['total_balance'] ?? 0), 2),
            'hint' => 'Outstanding amount',
            'icon' => 'fa-solid fa-scale-balanced',
            'iconTone' => 'warning',
        ]); ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <?php
        ob_start();
        ?>
        <form method="post" action="<?php echo e(url('?page=admin.payments')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="payment_action" value="fee">

            <div class="mb-3">
                <label class="form-label">Fee Name</label>
                <input type="text" name="fee_name" class="form-control rounded-4" value="<?php echo e(old('fee_name')); ?>" placeholder="School Fees" required>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select rounded-4" required>
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
                    <select name="term_id" class="form-select rounded-4" required>
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
                    <select name="class_id" class="form-select rounded-4" required>
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
                    <input type="number" name="amount_due" class="form-control rounded-4" step="0.01" min="0" value="<?php echo e(old('amount_due')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control rounded-4" value="<?php echo e(old('due_date')); ?>">
                </div>
            </div>

            <button class="btn btn-primary rounded-4 mt-3 w-100">
                <i class="fa-solid fa-floppy-disk me-2"></i>
                Save Fee Setup
            </button>
        </form>
        <?php
        $feeFormHtml = ob_get_clean();
        echo render_view('layouts.components.form_card', [
            'title' => 'Fee Setup',
            'description' => 'Define what each class should pay for the selected year and term.',
            'bodyHtml' => $feeFormHtml,
        ]);
        ?>

        <div class="mt-4">
            <?php
            ob_start();
            ?>
            <form method="post" action="<?php echo e(url('?page=admin.payments')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="payment_action" value="payment">

                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select rounded-4" required>
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
                    <select name="fee_structure_id" class="form-select rounded-4" required>
                        <option value="">Select fee structure</option>
                        <?php foreach ($feeOptions as $fee): ?>
                            <option value="<?php echo (int) $fee['id']; ?>" <?php echo old('fee_structure_id') == $fee['id'] ? 'selected' : ''; ?>>
                                <?php echo e($fee['fee_name']); ?> | <?php echo e($fee['year_name']); ?> | <?php echo e($fee['term_name']); ?> | <?php echo e($fee['class_name']); ?> | Due: <?php echo number_format((float) $fee['amount_due'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Amount Paid</label>
                        <input type="number" name="amount_paid" class="form-control rounded-4" step="0.01" min="0.01" value="<?php echo e(old('amount_paid')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control rounded-4" value="<?php echo e(old('payment_date', date('Y-m-d'))); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select rounded-4" required>
                            <option value="cash" <?php echo old('payment_method') === 'cash' ? 'selected' : ''; ?>>Cash</option>
                            <option value="bank" <?php echo old('payment_method') === 'bank' ? 'selected' : ''; ?>>Bank</option>
                            <option value="mobile_money" <?php echo old('payment_method') === 'mobile_money' ? 'selected' : ''; ?>>Mobile Money</option>
                            <option value="other" <?php echo old('payment_method') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" name="receipt_number" class="form-control rounded-4" value="<?php echo e(old('receipt_number')); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control rounded-4" rows="3"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>

                <button class="btn btn-success rounded-4 mt-3 w-100">
                    <i class="fa-solid fa-circle-plus me-2"></i>
                    Save Payment
                </button>
            </form>
            <?php
            $paymentFormHtml = ob_get_clean();
            echo render_view('layouts.components.form_card', [
                'title' => 'Record Payment',
                'description' => 'Capture student payment details and instantly update the balance.',
                'bodyHtml' => $paymentFormHtml,
            ]);
            ?>
        </div>
    </div>

    <div class="col-lg-7">
        <?php
        ob_start();
        ?>
        <table class="table table-hover align-middle mb-0" data-enhanced-table="fee-structures" data-page-size="8">
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
                    <tr data-table-row="1">
                        <td><?php echo e($fee['fee_name']); ?></td>
                        <td><?php echo e($fee['class_name']); ?></td>
                        <td><?php echo e($fee['year_name']); ?></td>
                        <td><?php echo e($fee['term_name']); ?></td>
                        <td><?php echo number_format((float) $fee['amount_due'], 2); ?></td>
                        <td><?php echo e($fee['due_date'] ?? '—'); ?></td>
                        <td>
                            <?php if (($fee['status'] ?? 'inactive') === 'active'): ?>
                                <span class="badge text-bg-success rounded-pill">Active</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary rounded-pill">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($feeStructures)): ?>
                    <tr data-table-empty="1">
                        <td colspan="7" class="text-center text-muted py-4">No fee structures created yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        $feeTableHtml = ob_get_clean();
        echo render_view('layouts.components.table', [
            'tableId' => 'fee-structures',
            'title' => 'Fee Structures',
            'description' => 'Defined charges for classes, terms, and academic years.',
            'toolbarActions' => '',
            'searchPlaceholder' => 'Search fee structures...',
            'pageSize' => 8,
            'tableHtml' => $feeTableHtml,
        ]);
        ?>

        <div class="mt-4">
            <?php
            ob_start();
            ?>
            <table class="table table-hover align-middle mb-0" data-enhanced-table="recent-payments" data-page-size="8">
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
                        <tr data-table-row="1">
                            <td>
                                <div class="fw-semibold"><?php echo e($payment['student_name']); ?></div>
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
                        <tr data-table-empty="1">
                            <td colspan="6" class="text-center text-muted py-4">No payments recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php
            $recentPaymentsHtml = ob_get_clean();
            echo render_view('layouts.components.table', [
                'tableId' => 'recent-payments',
                'title' => 'Recent Payments',
                'description' => 'Latest payment entries with amount, balance, and payment date.',
                'toolbarActions' => '<a href="' . e(url('?page=admin.payments.report')) . '" class="btn btn-outline-primary btn-sm rounded-4">View Reports</a>',
                'searchPlaceholder' => 'Search payments...',
                'pageSize' => 8,
                'tableHtml' => $recentPaymentsHtml,
            ]);
            ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require app_path('views/layouts/admin.php');

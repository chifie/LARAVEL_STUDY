<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Super Admin Dashboard';
$pageSubtitle = 'Manage the school setup, accounts, payments, and reports from one place.';
$pageActions = implode('', [
    '<a href="' . e(url('?page=admin.users.create-teacher')) . '" class="btn btn-primary rounded-4"><i class="fa-solid fa-user-plus me-2"></i>Add Teacher</a>',
    '<a href="' . e(url('?page=admin.users.create-student')) . '" class="btn btn-success rounded-4"><i class="fa-solid fa-user-graduate me-2"></i>Add Student</a>',
    '<a href="' . e(url('?page=admin.payments')) . '" class="btn btn-outline-primary rounded-4"><i class="fa-solid fa-coins me-2"></i>Payments</a>',
]);

$stats = $stats ?? [];
$latestUsers = $latestUsers ?? [];
$user = Auth::user();

ob_start();
?>
<div class="row g-3 g-xl-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Total Users',
            'value' => number_format((int) ($stats['users'] ?? 0)),
            'hint' => 'All active and inactive accounts',
            'icon' => 'fa-solid fa-users',
            'iconTone' => 'primary',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Teachers',
            'value' => number_format((int) ($stats['teachers'] ?? 0)),
            'hint' => 'Teaching staff registered',
            'icon' => 'fa-solid fa-person-chalkboard',
            'iconTone' => 'success',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Students',
            'value' => number_format((int) ($stats['students'] ?? 0)),
            'hint' => 'Learner accounts registered',
            'icon' => 'fa-solid fa-user-graduate',
            'iconTone' => 'warning',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Active Users',
            'value' => number_format((int) ($stats['active_users'] ?? 0)),
            'hint' => 'Currently enabled accounts',
            'icon' => 'fa-solid fa-circle-check',
            'iconTone' => 'info',
        ]); ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <?php
        ob_start();
        ?>
        <table class="table table-hover align-middle mb-0" data-enhanced-table="recent-users" data-page-size="5">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Password</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestUsers as $row): ?>
                    <tr data-table-row="1">
                        <td>
                            <div class="fw-semibold"><?php echo e($row['full_name']); ?></div>
                            <div class="small text-muted">#<?php echo (int) $row['id']; ?></div>
                        </td>
                        <td><?php echo e($row['email']); ?></td>
                        <td><span class="badge text-bg-secondary rounded-pill"><?php echo e(role_label_text($row['role'])); ?></span></td>
                        <td>
                            <?php if ((int) $row['is_active'] === 1): ?>
                                <span class="badge text-bg-success rounded-pill">Active</span>
                            <?php else: ?>
                                <span class="badge text-bg-danger rounded-pill">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int) $row['must_change_password'] === 1): ?>
                                <span class="badge text-bg-warning rounded-pill">Must change</span>
                            <?php else: ?>
                                <span class="badge text-bg-success rounded-pill">Changed</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($row['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($latestUsers)): ?>
                    <tr data-table-empty="1">
                        <td colspan="6" class="text-center text-muted py-4">No users found yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        $tableHtml = ob_get_clean();
        echo render_view('layouts.components.table', [
            'tableId' => 'recent-users',
            'title' => 'Latest Users',
            'description' => 'A quick look at newly created accounts and their status.',
            'toolbarActions' => '<a href="' . e(url('?page=admin.users')) . '" class="btn btn-outline-secondary btn-sm rounded-4">Manage Users</a>',
            'searchPlaceholder' => 'Search users...',
            'pageSize' => 5,
            'tableHtml' => $tableHtml,
        ]);
        ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require app_path('views/layouts/admin.php');

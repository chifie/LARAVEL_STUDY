<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Users';
$pageSubtitle = 'Activate or deactivate teacher and student accounts.';
$pageActions = implode('', [
    '<a href="' . e(url('?page=admin.users.create-teacher')) . '" class="btn btn-primary rounded-4"><i class="fa-solid fa-user-plus me-2"></i>Create Teacher</a>',
    '<a href="' . e(url('?page=admin.users.create-student')) . '" class="btn btn-success rounded-4"><i class="fa-solid fa-user-graduate me-2"></i>Create Student</a>',
]);

$users = $users ?? [];
$success = $success ?? null;
$error = $error ?? null;

$totalUsers = count($users);
$activeUsers = 0;
$inactiveUsers = 0;
$teachers = 0;
$students = 0;

foreach ($users as $row) {
    if ((int) ($row['is_active'] ?? 0) === 1) {
        $activeUsers++;
    } else {
        $inactiveUsers++;
    }

    if (($row['role'] ?? '') === 'teacher') {
        $teachers++;
    }

    if (($row['role'] ?? '') === 'student') {
        $students++;
    }
}

ob_start();
?>
<div class="row g-3 g-xl-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Total Users',
            'value' => number_format($totalUsers),
            'hint' => 'All accounts in the system',
            'icon' => 'fa-solid fa-users',
            'iconTone' => 'primary',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Teachers',
            'value' => number_format($teachers),
            'hint' => 'Teacher accounts',
            'icon' => 'fa-solid fa-person-chalkboard',
            'iconTone' => 'success',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Students',
            'value' => number_format($students),
            'hint' => 'Student accounts',
            'icon' => 'fa-solid fa-user-graduate',
            'iconTone' => 'warning',
        ]); ?>
    </div>
    <div class="col-md-6 col-xl-3">
        <?php echo render_view('layouts.components.stat_card', [
            'label' => 'Active',
            'value' => number_format($activeUsers),
            'hint' => $inactiveUsers . ' inactive accounts',
            'icon' => 'fa-solid fa-circle-check',
            'iconTone' => 'info',
        ]); ?>
    </div>
</div>

<?php
ob_start();
?>
<table class="table table-hover align-middle mb-0" data-enhanced-table="users-table" data-page-size="10">
    <thead class="table-light">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Password</th>
            <th>Status</th>
            <th>Created</th>
            <th class="text-end">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $row): ?>
            <tr data-table-row="1">
                <td>
                    <div class="fw-semibold"><?php echo e($row['full_name']); ?></div>
                    <div class="small text-muted">#<?php echo (int) $row['id']; ?></div>
                </td>
                <td><?php echo e($row['email']); ?></td>
                <td><?php echo e($row['phone'] ?: '—'); ?></td>
                <td><span class="badge text-bg-secondary rounded-pill"><?php echo e(role_label_text($row['role'])); ?></span></td>
                <td>
                    <?php if ((int) $row['must_change_password'] === 1): ?>
                        <span class="badge text-bg-warning rounded-pill">Must change</span>
                    <?php else: ?>
                        <span class="badge text-bg-success rounded-pill">Changed</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ((int) $row['is_active'] === 1): ?>
                        <span class="badge text-bg-success rounded-pill">Active</span>
                    <?php else: ?>
                        <span class="badge text-bg-danger rounded-pill">Inactive</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($row['created_at']); ?></td>
                <td class="text-end">
                    <form method="post" action="<?php echo e(url('?page=admin.users.toggle-status')); ?>" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="user_id" value="<?php echo (int) $row['id']; ?>">
                        <button
                            type="submit"
                            class="btn btn-sm <?php echo ((int) $row['is_active'] === 1) ? 'btn-outline-danger' : 'btn-outline-success'; ?> rounded-4"
                            <?php echo ((int) $row['id'] === (int) Auth::id()) ? 'disabled' : ''; ?>
                        >
                            <?php echo ((int) $row['is_active'] === 1) ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($users)): ?>
            <tr data-table-empty="1">
                <td colspan="8" class="text-center text-muted py-4">No users found yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
$tableHtml = ob_get_clean();
echo render_view('layouts.components.table', [
    'tableId' => 'users-table',
    'title' => 'User Directory',
    'description' => 'Search, filter, and manage teacher and student accounts.',
    'toolbarActions' => '',
    'searchPlaceholder' => 'Search name, email, role...',
    'pageSize' => 10,
    'tableHtml' => $tableHtml,
]);
?>
<?php
$content = ob_get_clean();
require app_path('views/layouts/admin.php');

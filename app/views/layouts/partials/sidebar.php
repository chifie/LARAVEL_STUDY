<?php
$appName = app_config('app_name', 'School SMS');
$role = $role ?? (Auth::role() ?? 'super_admin');
$user = $user ?? Auth::user();
$activePage = $activePage ?? current_page();
$roleLabel = role_label_text($role);

$adminMenu = [
    ['label' => 'Dashboard', 'page' => 'admin.dashboard', 'icon' => 'fa-solid fa-gauge-high'],
    ['label' => 'Users', 'page' => 'admin.users', 'icon' => 'fa-solid fa-users', 'prefix' => true],
    ['label' => 'Academic Years', 'page' => 'admin.structure.years', 'icon' => 'fa-regular fa-calendar'],
    ['label' => 'Terms', 'page' => 'admin.structure.terms', 'icon' => 'fa-regular fa-calendar-check'],
    ['label' => 'Classes', 'page' => 'admin.structure.classes', 'icon' => 'fa-solid fa-school', 'prefix' => true],
    ['label' => 'Subjects', 'page' => 'admin.structure.subjects', 'icon' => 'fa-solid fa-book', 'prefix' => true],
    ['label' => 'Assignments', 'page' => 'admin.structure.assignments', 'icon' => 'fa-solid fa-clipboard-list'],
    ['label' => 'Students', 'page' => 'admin.students', 'icon' => 'fa-solid fa-user-graduate', 'prefix' => true],
    ['label' => 'Attendance', 'page' => 'admin.attendance', 'icon' => 'fa-solid fa-clipboard-check', 'prefix' => true],
    ['label' => 'Exams', 'page' => 'admin.exams', 'icon' => 'fa-solid fa-file-pen', 'prefix' => true],
    ['label' => 'Payments', 'page' => 'admin.payments', 'icon' => 'fa-solid fa-coins', 'prefix' => true],
    ['label' => 'Announcements', 'page' => 'admin.announcements', 'icon' => 'fa-solid fa-bullhorn', 'prefix' => true],
    ['label' => 'Notifications', 'page' => 'admin.notifications', 'icon' => 'fa-regular fa-bell', 'prefix' => true],
    ['label' => 'Reports', 'page' => 'admin.reports', 'icon' => 'fa-solid fa-chart-column', 'prefix' => true],
];

$teacherMenu = [
    ['label' => 'Dashboard', 'page' => 'teacher.dashboard', 'icon' => 'fa-solid fa-gauge-high'],
    ['label' => 'Attendance', 'page' => 'teacher.attendance', 'icon' => 'fa-solid fa-clipboard-check', 'prefix' => true],
    ['label' => 'Exams & Results', 'page' => 'teacher.exams', 'icon' => 'fa-solid fa-file-pen', 'prefix' => true],
    ['label' => 'Announcements', 'page' => 'teacher.announcements', 'icon' => 'fa-solid fa-bullhorn', 'prefix' => true],
    ['label' => 'Notifications', 'page' => 'teacher.notifications', 'icon' => 'fa-regular fa-bell', 'prefix' => true],
];

$studentMenu = [
    ['label' => 'Dashboard', 'page' => 'student.dashboard', 'icon' => 'fa-solid fa-gauge-high'],
    ['label' => 'Profile', 'page' => 'student.profile', 'icon' => 'fa-solid fa-id-card', 'prefix' => true],
    ['label' => 'History', 'page' => 'student.history', 'icon' => 'fa-solid fa-clock-rotate-left', 'prefix' => true],
    ['label' => 'Results', 'page' => 'student.my-results', 'icon' => 'fa-solid fa-square-poll-vertical', 'prefix' => true],
    ['label' => 'Payments', 'page' => 'student.my-payments', 'icon' => 'fa-solid fa-coins', 'prefix' => true],
    ['label' => 'Announcements', 'page' => 'student.announcements', 'icon' => 'fa-solid fa-bullhorn', 'prefix' => true],
    ['label' => 'Notifications', 'page' => 'student.notifications', 'icon' => 'fa-regular fa-bell', 'prefix' => true],
];

$menu = $role === 'teacher' ? $teacherMenu : ($role === 'student' ? $studentMenu : $adminMenu);
?>
<div class="d-flex flex-column h-100 bg-body-tertiary border-end" style="width: 290px;">
    <div class="px-4 py-4 border-bottom bg-white">
        <a href="<?php echo e(Auth::dashboardUrl()); ?>" class="text-decoration-none text-dark d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-inline-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                <i class="fa-solid fa-graduation-cap fs-4"></i>
            </div>
            <div class="lh-sm">
                <div class="fw-bold"><?php echo e($appName); ?></div>
                <div class="small text-muted"><?php echo e($roleLabel); ?> Portal</div>
            </div>
        </a>
    </div>

    <div class="px-4 py-3 border-bottom bg-white">
        <div class="small text-uppercase text-muted fw-semibold">Signed in as</div>
        <div class="fw-semibold text-truncate"><?php echo e($user['full_name'] ?? 'User'); ?></div>
        <div class="small text-muted text-truncate"><?php echo e($user['email'] ?? ''); ?></div>
    </div>

    <div class="flex-grow-1 overflow-auto p-3">
        <div class="nav nav-pills flex-column gap-1">
            <?php foreach ($menu as $item): ?>
                <?php
                    $isActive = !empty($item['prefix'])
                        ? route_starts_with($item['page'], $activePage)
                        : route_is($item['page'], $activePage);
                ?>
                <a href="<?php echo e(url('?page=' . $item['page'])); ?>"
                   class="nav-link rounded-4 px-3 py-2 <?php echo $isActive ? 'active' : 'text-dark'; ?>">
                    <i class="<?php echo e($item['icon']); ?> me-2"></i>
                    <?php echo e($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="p-3 border-top bg-white">
        <form method="post" action="<?php echo e(url('logout.php')); ?>" class="d-grid">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline-danger rounded-4">
                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>
                Logout
            </button>
        </form>
    </div>
</div>

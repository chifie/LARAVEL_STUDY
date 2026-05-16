<?php
$appName = app_config('app_name', 'School SMS');
$user = $user ?? Auth::user();
$pageTitle = $pageTitle ?? $appName;
$pageSubtitle = $pageSubtitle ?? '';
$activePage = $activePage ?? current_page();
$role = $role ?? (Auth::role() ?? 'super_admin');
?>
<header class="bg-white border-bottom sticky-top">
    <div class="px-3 px-xl-4 py-3 d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary rounded-4 d-lg-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div>
                <div class="text-uppercase small text-muted fw-semibold"><?php echo e($appName); ?></div>
                <div class="fw-semibold"><?php echo e($pageTitle); ?></div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
            <span class="badge text-bg-light border rounded-pill d-none d-md-inline-flex">
                <?php echo e(role_label_text($role)); ?>
            </span>

            <div class="dropdown">
                <button class="btn btn-light border rounded-4 dropdown-toggle" data-bs-toggle="dropdown" type="button">
                    <span class="me-1"><?php echo e($user['full_name'] ?? 'User'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-4 border-0">
                    <li class="px-3 py-2">
                        <div class="small text-muted">Signed in</div>
                        <div class="fw-semibold text-truncate"><?php echo e($user['email'] ?? ''); ?></div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(Auth::dashboardUrl()); ?>">
                            <i class="fa-solid fa-house me-2"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(url('logout.php')); ?>">
                            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

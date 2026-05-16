<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/bootstrap.php';

if (!Auth::check()) {
    Response::redirect(Auth::loginUrl());
}

$action = trim((string) ($_GET['action'] ?? $_POST['action'] ?? ''));

if (is_post()) {
    if (!Csrf::validate($_POST['_token'] ?? '')) {
        Session::flash('error', 'Invalid logout request.');
        Response::redirect(Auth::dashboardUrl());
    }

    if ($action === 'cancel') {
        Response::redirect(Auth::dashboardUrl());
    }

    if ($action === 'confirm') {
        Auth::logout();
        Response::redirect(Auth::loginUrl());
    }

    Session::flash('error', 'Please confirm logout first.');
    Response::redirect(url('logout.php?action=confirm'));
}

$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Confirm Logout';
$brandTitle = $appName;
$brandLead = 'You are about to end the current session.';
$brandNote = 'Logout is final until you sign in again.';
$brandFeatures = [
    [
        'icon' => 'fa-solid fa-user-check',
        'title' => 'Signed in as ' . (Auth::user()['full_name'] ?? 'User'),
        'text' => 'The active session belongs to your account.',
    ],
    [
        'icon' => 'fa-solid fa-shield-halved',
        'title' => 'Session protection',
        'text' => 'Confirmation helps prevent accidental logouts.',
    ],
];

$error = flash('error');

ob_start();
?>
<div class="text-center">
    <span class="badge text-bg-danger rounded-pill mb-3">Session Action</span>
    <h2 class="h3 fw-bold mb-2">Confirm logout</h2>
    <p class="text-muted mb-4">
        Do you really want to log out now? The session will end immediately after confirmation.
    </p>

    <?php if ($error): ?>
        <div class="alert alert-danger shadow-sm rounded-4 border-0 text-start">
            <?php echo e($error); ?>
        </div>
    <?php endif; ?>

    <div class="d-flex gap-2 justify-content-center flex-wrap mt-4">
        <a href="<?php echo e(Auth::dashboardUrl()); ?>" class="btn btn-outline-secondary btn-lg rounded-4">
            <i class="fa-solid fa-xmark me-2"></i>
            Cancel
        </a>

        <form method="post" action="<?php echo e(url('logout.php')); ?>" class="d-inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="confirm">
            <button type="submit" class="btn btn-danger btn-lg rounded-4">
                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>
                Yes, log me out
            </button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require app_path('views/layouts/auth.php');

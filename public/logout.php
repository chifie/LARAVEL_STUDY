<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/bootstrap.php';

if (!Auth::check()) {
    Response::redirect(Auth::loginUrl());
}

$action = trim((string) ($_GET['action'] ?? $_POST['action'] ?? ''));

/**
 * Confirmed logout request
 */
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

/**
 * Show confirmation page
 */
$appName = app_config('app_name', 'School SMS');
$error = flash('error');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Confirm Logout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 45%, #f8fafc 100%);
        }
        .confirm-card {
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
        }
        .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            font-size: 2rem;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-md-8 col-lg-6">
            <div class="card confirm-card">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="icon-wrap mb-4 mx-auto">
                        <span>!</span>
                    </div>

                    <h1 class="h3 mb-3">Confirm logout</h1>
                    <p class="text-muted mb-4">
                        You are signed in as <strong><?php echo e(Auth::user()['full_name'] ?? 'User'); ?></strong>.
                        Do you really want to log out now?
                    </p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger text-start">
                            <?php echo e($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="<?php echo e(Auth::dashboardUrl()); ?>" class="btn btn-outline-secondary btn-lg">
                            Cancel
                        </a>

                        <form method="post" action="<?php echo e(url('logout.php')); ?>" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="confirm">
                            <button type="submit" class="btn btn-danger btn-lg">
                                Yes, log me out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <p class="text-center text-white-50 mt-3 mb-0 small">
                Session will end immediately after confirmation.
            </p>
        </div>
    </div>
</div>
</body>
</html>
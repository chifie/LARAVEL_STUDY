<?php
$appName = app_config('app_name', 'School SMS');
$user = auth_user();
$errors = flash('errors', []);
$error = flash('error');
$success = flash('success');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 45%, #f8fafc 100%);
        }
        .change-card {
            border: 0;
            border-radius: 1.25rem;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-lg-7 col-xl-6">
            <div class="card change-card">
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4">
                        <h1 class="h3 mb-2"><?php echo e($appName); ?></h1>
                        <p class="text-muted mb-0">
                            Hello <?php echo e($user ? $user['full_name'] : 'User'); ?>, you must change your temporary password before continuing.
                        </p>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo e($success); ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo e($error); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($errors) && is_array($errors)): ?>
                        <div class="alert alert-warning">
                            <strong>Please fix the following:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $message): ?>
                                    <li><?php echo e($message); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo e(url('?page=change-password')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current password</label>
                            <input
                                type="password"
                                name="current_password"
                                id="current_password"
                                class="form-control form-control-lg"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New password</label>
                            <input
                                type="password"
                                name="new_password"
                                id="new_password"
                                class="form-control form-control-lg"
                                minlength="8"
                                required
                            >
                            <div class="form-text">Use at least 8 characters.</div>
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">Confirm new password</label>
                            <input
                                type="password"
                                name="new_password_confirmation"
                                id="new_password_confirmation"
                                class="form-control form-control-lg"
                                minlength="8"
                                required
                            >
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Change Password';
$brandTitle = $appName;
$brandLead = 'Lock in your account by replacing the temporary password with something personal.';
$brandNote = 'This step is required before you can access the dashboard.';
$brandFeatures = [
    [
        'icon' => 'fa-solid fa-shield-halved',
        'title' => 'Account protection',
        'text' => 'Keeps the shared temporary password from living too long in the wild.',
    ],
    [
        'icon' => 'fa-solid fa-user-lock',
        'title' => 'Mandatory first change',
        'text' => 'Every new account must confirm its identity with a password update.',
    ],
];

$errors = flash('errors', []);
$error = flash('error');
$success = flash('success');
$user = auth_user();

ob_start();
?>
<div class="mb-4">
    <h2 class="h3 fw-bold mb-2">Change your password</h2>
    <p class="text-muted mb-0">
        Hello <?php echo e($user ? $user['full_name'] : 'User'); ?>, please set a secure password before continuing.
    </p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success shadow-sm rounded-4 border-0">
        <?php echo e($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger shadow-sm rounded-4 border-0">
        <?php echo e($error); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors) && is_array($errors)): ?>
    <div class="alert alert-warning shadow-sm rounded-4 border-0">
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
        <label for="current_password" class="form-label fw-medium">Current password</label>
        <input type="password" name="current_password" id="current_password" class="form-control form-control-lg rounded-4" required>
    </div>

    <div class="mb-3">
        <label for="new_password" class="form-label fw-medium">New password</label>
        <input type="password" name="new_password" id="new_password" class="form-control form-control-lg rounded-4" minlength="8" required>
    </div>

    <div class="mb-3">
        <label for="new_password_confirmation" class="form-label fw-medium">Confirm new password</label>
        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control form-control-lg rounded-4" minlength="8" required>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-success btn-lg rounded-4">
            <i class="fa-solid fa-key me-2"></i>
            Update Password
        </button>
    </div>
</form>

<div class="mt-4 p-3 bg-body-tertiary border rounded-4">
    <div class="d-flex align-items-start gap-3">
        <i class="fa-solid fa-circle-info text-primary mt-1"></i>
        <div>
            <div class="fw-semibold mb-1">Security note</div>
            <div class="text-muted small">
                Choose a password you will remember, then sign in again with the new credentials.
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require app_path('views/layouts/auth.php');

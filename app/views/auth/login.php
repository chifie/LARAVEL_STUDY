<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Login';
$brandTitle = $appName;
$brandLead = 'Secure login for Super Admin, Teacher, and Student accounts.';
$brandNote = 'Temporary passwords must be changed on first login.';
$brandFeatures = [
    [
        'icon' => 'fa-solid fa-user-shield',
        'title' => 'Role-based access',
        'text' => 'Each user type lands on the right dashboard automatically.',
    ],
    [
        'icon' => 'fa-solid fa-lock',
        'title' => 'Password protection',
        'text' => 'Temporary passwords are forced to change on first sign-in.',
    ],
    [
        'icon' => 'fa-solid fa-bolt',
        'title' => 'Fast and lightweight',
        'text' => 'Plain PHP, MySQL, and Bootstrap with no heavy framework overhead.',
    ],
];

$errors = flash('errors', []);
$error = flash('error');
$success = flash('success');
$oldEmail = old('email');

ob_start();
?>
<div class="mb-4">
    <h2 class="h3 fw-bold mb-2">Sign in</h2>
    <p class="text-muted mb-0">Use the temporary password provided by the Super Admin.</p>
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

<form method="post" action="<?php echo e(url('?page=login')); ?>" novalidate>
    <?php echo csrf_field(); ?>

    <div class="mb-3">
        <label for="email" class="form-label fw-medium">Email address</label>
        <input
            type="email"
            name="email"
            id="email"
            class="form-control form-control-lg rounded-4"
            placeholder="name@example.com"
            value="<?php echo e($oldEmail); ?>"
            autocomplete="email"
            required
        >
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-medium">Password</label>
        <input
            type="password"
            name="password"
            id="password"
            class="form-control form-control-lg rounded-4"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
        >
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg rounded-4">
            <i class="fa-solid fa-right-to-bracket me-2"></i>
            Login
        </button>
    </div>
</form>

<div class="mt-4 p-3 bg-body-tertiary border rounded-4">
    <div class="d-flex align-items-start gap-3">
        <i class="fa-solid fa-circle-info text-primary mt-1"></i>
        <div>
            <div class="fw-semibold mb-1">First login rule</div>
            <div class="text-muted small">
                After signing in with the temporary password, the system will redirect you to the password change screen before dashboard access is granted.
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require app_path('views/layouts/auth.php');
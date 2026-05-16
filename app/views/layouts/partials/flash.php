<?php
$success = $success ?? null;
$error = $error ?? null;
$errors = $errors ?? [];
$notice = $notice ?? null;
$warning = $warning ?? null;
?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="fa-solid fa-circle-check mt-1"></i>
            <div><?php echo e($success); ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation mt-1"></i>
            <div><?php echo e($error); ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($warning)): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-1"></i>
            <div><?php echo e($warning); ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($notice)): ?>
    <div class="alert alert-info alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="fa-solid fa-circle-info mt-1"></i>
            <div><?php echo e($notice); ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($errors) && is_array($errors)): ?>
    <div class="alert alert-warning shadow-sm rounded-4 border-0 mb-4" role="alert">
        <div class="fw-semibold mb-2">Please fix the following:</div>
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $message): ?>
                <li><?php echo e($message); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

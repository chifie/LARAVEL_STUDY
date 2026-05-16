<?php
$label = $label ?? '';
$value = $value ?? '';
$hint = $hint ?? '';
$icon = $icon ?? 'fa-solid fa-chart-simple';
$iconTone = $iconTone ?? 'primary';
$trend = $trend ?? '';
?>
<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <div class="text-uppercase small text-muted fw-semibold mb-2"><?php echo e($label); ?></div>
                <div class="h2 mb-1 fw-bold"><?php echo e($value); ?></div>
                <?php if ($hint !== ''): ?>
                    <div class="small text-muted"><?php echo e($hint); ?></div>
                <?php endif; ?>
                <?php if ($trend !== ''): ?>
                    <div class="small mt-2 text-success fw-semibold"><?php echo e($trend); ?></div>
                <?php endif; ?>
            </div>
            <div class="rounded-4 bg-<?php echo e($iconTone); ?> bg-opacity-10 text-<?php echo e($iconTone); ?> d-inline-flex align-items-center justify-content-center"
                 style="width: 3.25rem; height: 3.25rem;">
                <i class="<?php echo e($icon); ?> fs-4"></i>
            </div>
        </div>
    </div>
</div>

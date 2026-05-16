<?php
$title = $title ?? '';
$description = $description ?? '';
$bodyHtml = $bodyHtml ?? '';
$footerHtml = $footerHtml ?? '';
?>
<div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
    <div class="card-header bg-white border-0 p-4">
        <div class="fw-semibold h6 mb-1"><?php echo e($title); ?></div>
        <?php if ($description !== ''): ?>
            <div class="text-muted small"><?php echo e($description); ?></div>
        <?php endif; ?>
    </div>
    <div class="card-body p-4">
        <?php echo $bodyHtml; ?>
    </div>
    <?php if ($footerHtml !== ''): ?>
        <div class="card-footer bg-white border-0 p-4 pt-0">
            <?php echo $footerHtml; ?>
        </div>
    <?php endif; ?>
</div>

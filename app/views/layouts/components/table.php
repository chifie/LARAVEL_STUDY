<?php
$tableId = $tableId ?? 'table-' . substr(md5((string) microtime(true)), 0, 8);
$title = $title ?? '';
$description = $description ?? '';
$toolbarActions = $toolbarActions ?? '';
$searchPlaceholder = $searchPlaceholder ?? 'Search...';
$tableHtml = $tableHtml ?? '';
$pageSize = isset($pageSize) ? (int) $pageSize : 10;
$showSearch = $showSearch ?? true;
?>
<div class="card border-0 shadow-sm rounded-4 overflow-hidden" data-table-card="<?php echo e($tableId); ?>">
    <div class="card-header bg-white border-0 p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="fw-semibold h6 mb-1"><?php echo e($title); ?></div>
                <?php if ($description !== ''): ?>
                    <div class="text-muted small"><?php echo e($description); ?></div>
                <?php endif; ?>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php if ($showSearch): ?>
                    <div class="input-group input-group-sm" style="min-width: 250px; max-width: 360px;">
                        <span class="input-group-text bg-light border-0">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>
                        <input type="search"
                               class="form-control border-0 bg-light"
                               placeholder="<?php echo e($searchPlaceholder); ?>"
                               data-table-search="<?php echo e($tableId); ?>">
                    </div>
                <?php endif; ?>
                <?php echo $toolbarActions; ?>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <?php echo $tableHtml; ?>
    </div>

    <div class="card-footer bg-white border-0 px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted" data-table-meta="<?php echo e($tableId); ?>"></small>
        <nav data-table-pagination="<?php echo e($tableId); ?>"></nav>
    </div>
</div>

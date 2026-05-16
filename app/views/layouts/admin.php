<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = $pageTitle ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? '';
$pageActions = $pageActions ?? '';
$breadcrumbs = $breadcrumbs ?? [];
$content = $content ?? '';
$activePage = $activePage ?? current_page();
$role = $role ?? (Auth::role() ?? 'super_admin');
$user = $user ?? Auth::user();
$success = $success ?? null;
$error = $error ?? null;
$errors = $errors ?? [];
$warning = $warning ?? null;
$notice = $notice ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title><?php echo e($appName); ?> - <?php echo e($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 290px;
        }

        body {
            background: #f1f5f9;
            color: #0f172a;
        }

        .layout-shell {
            min-height: 100vh;
        }

        .app-main {
            min-width: 0;
        }

        .content-shell {
            padding: 1.25rem;
        }

        .rounded-4 {
            border-radius: 1.25rem !important;
        }

        .nav-pills .nav-link {
            color: #0f172a;
            transition: all .15s ease;
        }

        .nav-pills .nav-link:hover {
            background: rgba(37, 99, 235, 0.08);
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff !important;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.18);
        }

        .page-header {
            margin-bottom: 1.25rem;
        }

        .page-header .badge {
            letter-spacing: .04em;
        }

        .muted-surface {
            background: rgba(255, 255, 255, 0.7);
        }

        .table thead th {
            white-space: nowrap;
        }

        .metric-icon {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .offcanvas-lg {
            background: #fff;
        }

        @media (min-width: 992px) {
            .offcanvas-lg {
                position: sticky;
                top: 0;
                height: 100vh;
                transform: none !important;
                visibility: visible !important;
            }
        }
    </style>
</head>
<body>
<div class="layout-shell d-flex">
    <aside class="offcanvas-lg offcanvas-start border-0 shadow-sm flex-shrink-0" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel" style="width: 290px;">
        <?php echo render_view('layouts.partials.sidebar', [
            'appName' => $appName,
            'role' => $role,
            'user' => $user,
            'activePage' => $activePage,
        ]); ?>
    </aside>

    <div class="app-main flex-grow-1 d-flex flex-column min-vh-100">
        <?php echo render_view('layouts.partials.topbar', [
            'appName' => $appName,
            'pageTitle' => $pageTitle,
            'role' => $role,
            'user' => $user,
            'activePage' => $activePage,
        ]); ?>

        <main class="flex-grow-1">
            <div class="content-shell container-fluid px-3 px-xl-4 py-4">
                <?php echo render_view('layouts.partials.flash', compact('success', 'error', 'errors', 'warning', 'notice')); ?>

                <div class="page-header d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge text-bg-primary rounded-pill px-3 py-2">
                                <i class="fa-solid fa-layer-group me-1"></i>
                                <?php echo e(role_label_text($role)); ?>
                            </span>
                            <?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 small">
                                        <?php foreach ($breadcrumbs as $crumb): ?>
                                            <?php if (!empty($crumb['url'])): ?>
                                                <li class="breadcrumb-item"><a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['label']); ?></a></li>
                                            <?php else: ?>
                                                <li class="breadcrumb-item active" aria-current="page"><?php echo e($crumb['label']); ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ol>
                                </nav>
                            <?php endif; ?>
                        </div>
                        <h1 class="h3 fw-bold mb-1"><?php echo e($pageTitle); ?></h1>
                        <?php if ($pageSubtitle !== ''): ?>
                            <p class="text-muted mb-0"><?php echo e($pageSubtitle); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($pageActions !== ''): ?>
                        <div class="d-flex flex-wrap gap-2 justify-content-xl-end">
                            <?php echo $pageActions; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <?php echo $content; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    function setupEnhancedTable(table) {
        var tableId = table.getAttribute('data-enhanced-table');
        if (!tableId) {
            return;
        }

        var searchInput = document.querySelector('[data-table-search="' + tableId + '"]');
        var pagination = document.querySelector('[data-table-pagination="' + tableId + '"]');
        var meta = document.querySelector('[data-table-meta="' + tableId + '"]');
        var dataRows = Array.prototype.slice.call(table.querySelectorAll('tbody tr[data-table-row="1"]'));
        var emptyRow = table.querySelector('tbody tr[data-table-empty="1"]');
        var pageSize = parseInt(table.getAttribute('data-page-size') || '10', 10);
        var currentPage = 1;
        var filteredRows = dataRows.slice();

        function getQuery() {
            return searchInput ? searchInput.value.trim().toLowerCase() : '';
        }

        function applyFilter() {
            var query = getQuery();
            filteredRows = dataRows.filter(function (row) {
                if (!query) {
                    return true;
                }

                return row.textContent.toLowerCase().indexOf(query) !== -1;
            });

            currentPage = 1;
            render();
        }

        function renderPagination(totalPages) {
            if (!pagination) {
                return;
            }

            if (totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            var html = '';
            var prevDisabled = currentPage === 1 ? 'disabled' : '';
            var nextDisabled = currentPage === totalPages ? 'disabled' : '';

            html += '<div class="btn-group btn-group-sm" role="group">';
            html += '<button type="button" class="btn btn-outline-secondary" data-page-action="prev" ' + prevDisabled + '>Prev</button>';

            var start = Math.max(1, currentPage - 2);
            var end = Math.min(totalPages, currentPage + 2);

            if (start > 1) {
                html += '<button type="button" class="btn btn-outline-secondary" data-page-number="1">1</button>';
                if (start > 2) {
                    html += '<button type="button" class="btn btn-outline-secondary disabled">…</button>';
                }
            }

            for (var i = start; i <= end; i++) {
                var active = i === currentPage ? 'active' : '';
                html += '<button type="button" class="btn btn-outline-secondary ' + active + '" data-page-number="' + i + '">' + i + '</button>';
            }

            if (end < totalPages) {
                if (end < totalPages - 1) {
                    html += '<button type="button" class="btn btn-outline-secondary disabled">…</button>';
                }
                html += '<button type="button" class="btn btn-outline-secondary" data-page-number="' + totalPages + '">' + totalPages + '</button>';
            }

            html += '<button type="button" class="btn btn-outline-secondary" data-page-action="next" ' + nextDisabled + '>Next</button>';
            html += '</div>';

            pagination.innerHTML = html;

            Array.prototype.slice.call(pagination.querySelectorAll('[data-page-number]')).forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var page = parseInt(this.getAttribute('data-page-number') || '1', 10);
                    if (!isNaN(page)) {
                        currentPage = page;
                        render();
                    }
                });
            });

            var prevBtn = pagination.querySelector('[data-page-action="prev"]');
            var nextBtn = pagination.querySelector('[data-page-action="next"]');

            if (prevBtn && !prevBtn.disabled) {
                prevBtn.addEventListener('click', function () {
                    currentPage = Math.max(1, currentPage - 1);
                    render();
                });
            }

            if (nextBtn && !nextBtn.disabled) {
                nextBtn.addEventListener('click', function () {
                    currentPage = Math.min(totalPages, currentPage + 1);
                    render();
                });
            }
        }

        function render() {
            var total = filteredRows.length;
            var totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            dataRows.forEach(function (row) {
                row.style.display = 'none';
            });

            if (emptyRow) {
                emptyRow.style.display = 'none';
            }

            if (total === 0) {
                if (emptyRow) {
                    emptyRow.style.display = '';
                }

                if (meta) {
                    meta.textContent = 'No records found';
                }

                renderPagination(1);
                return;
            }

            var startIndex = (currentPage - 1) * pageSize;
            var endIndex = startIndex + pageSize;
            filteredRows.slice(startIndex, endIndex).forEach(function (row) {
                row.style.display = '';
            });

            if (meta) {
                var from = startIndex + 1;
                var to = Math.min(endIndex, total);
                meta.textContent = 'Showing ' + from + '–' + to + ' of ' + total + ' records';
            }

            renderPagination(totalPages);
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilter);
        }

        render();
    }

    document.querySelectorAll('table[data-enhanced-table]').forEach(setupEnhancedTable);
})();
</script>
</body>
</html>

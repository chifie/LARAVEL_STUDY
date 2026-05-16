<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Academic Years</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.structure.terms')); ?>" class="btn btn-light btn-sm">Terms</a>
            <a href="<?php echo e(url('?page=admin.structure.classes')); ?>" class="btn btn-light btn-sm">Classes</a>
            <a href="<?php echo e(url('?page=admin.structure.subjects')); ?>" class="btn btn-light btn-sm">Subjects</a>
            <a href="<?php echo e(url('?page=admin.structure.assignments')); ?>" class="btn btn-light btn-sm">Assignments</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Academic Years</h1>
            <p class="text-muted mb-0">Create the school years used across the whole system.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.dashboard')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Create Academic Year</h2>
                    <form method="post" action="<?php echo e(url('?page=admin.structure.years')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label">Year Name</label>
                            <input type="text" name="year_name" class="form-control" placeholder="2025/2026" value="<?php echo e(old('year_name')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo e(old('start_date')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo e(old('end_date')); ?>" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_current" id="is_current" <?php echo old('is_current') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_current">Set as current year</label>
                        </div>

                        <button class="btn btn-primary">Save Academic Year</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Year Name</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Current</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($years as $year): ?>
                                <tr>
                                    <td><?php echo e($year['year_name']); ?></td>
                                    <td><?php echo e($year['start_date']); ?></td>
                                    <td><?php echo e($year['end_date']); ?></td>
                                    <td>
                                        <?php if ((int) $year['is_current'] === 1): ?>
                                            <span class="badge text-bg-success">Current</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($currentYear): ?>
                <div class="alert alert-info mt-3 mb-0">
                    Current academic year: <strong><?php echo e($currentYear['year_name']); ?></strong>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
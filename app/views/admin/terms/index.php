<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Terms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
        <div class="d-flex gap-2">
            <a href="<?php echo e(url('?page=admin.structure.years')); ?>" class="btn btn-light btn-sm">Academic Years</a>
            <a href="<?php echo e(url('?page=admin.structure.classes')); ?>" class="btn btn-light btn-sm">Classes</a>
            <a href="<?php echo e(url('?page=admin.structure.subjects')); ?>" class="btn btn-light btn-sm">Subjects</a>
            <a href="<?php echo e(url('?page=admin.structure.assignments')); ?>" class="btn btn-light btn-sm">Assignments</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Terms</h1>
            <p class="text-muted mb-0">Create terms under a selected academic year.</p>
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
                    <h2 class="h5 mb-3">Create Term</h2>
                    <form method="post" action="<?php echo e(url('?page=admin.structure.terms')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select academic year</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?php echo (int) $year['id']; ?>" <?php echo old('academic_year_id') == $year['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($year['year_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Term Name</label>
                            <input type="text" name="term_name" class="form-control" placeholder="First Term" value="<?php echo e(old('term_name')); ?>" required>
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
                            <input class="form-check-input" type="checkbox" name="is_current" id="is_current_term" <?php echo old('is_current') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_current_term">Set as current term for that year</label>
                        </div>

                        <button class="btn btn-primary">Save Term</button>
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
                                <th>Academic Year</th>
                                <th>Term</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Current</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($terms as $term): ?>
                                <tr>
                                    <td><?php echo e($term['year_name']); ?></td>
                                    <td><?php echo e($term['term_name']); ?></td>
                                    <td><?php echo e($term['start_date']); ?></td>
                                    <td><?php echo e($term['end_date']); ?></td>
                                    <td>
                                        <?php if ((int) $term['is_current'] === 1): ?>
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
        </div>
    </div>
</div>
</body>
</html>
<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Create Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Create Announcement</h1>
            <p class="text-muted mb-0">Choose who should receive the message.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.announcements')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo e(url('?page=admin.announcements.create')); ?>">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo e(old('title')); ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="6" required><?php echo e(old('message')); ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Audience</label>
                        <select name="audience" id="audience" class="form-select" required>
                            <option value="all" <?php echo old('audience') === 'all' ? 'selected' : ''; ?>>All Users</option>
                            <option value="teachers" <?php echo old('audience') === 'teachers' ? 'selected' : ''; ?>>Teachers</option>
                            <option value="students" <?php echo old('audience') === 'students' ? 'selected' : ''; ?>>Students</option>
                            <option value="class" <?php echo old('audience') === 'class' ? 'selected' : ''; ?>>One Class</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Class (only for one class)</label>
                        <select name="class_id" class="form-select">
                            <option value="">Select class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo (int) $class['id']; ?>" <?php echo old('class_id') == $class['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($class['class_name']); ?> (<?php echo e($class['year_name']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_published" id="is_published" <?php echo old('is_published') ? 'checked' : 'checked'; ?>>
                            <label class="form-check-label" for="is_published">
                                Publish immediately
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Save Announcement</button>
                    <a href="<?php echo e(url('?page=admin.announcements')); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
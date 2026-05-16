<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Create Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
    </div>
</nav>

<div class="container py-4" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Create Student Account</h1>
            <p class="text-muted mb-0">A temporary password will be generated automatically.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.users')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo e(url('?page=admin.users.create-student')); ?>">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo e(old('full_name')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Admission Number</label>
                        <input type="text" name="admission_number" class="form-control" value="<?php echo e(old('admission_number')); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select</option>
                            <option value="male" <?php echo old('gender') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo old('gender') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo old('gender') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo e(old('date_of_birth')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Admission Date</label>
                        <input type="date" name="admission_date" class="form-control" value="<?php echo e(old('admission_date')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">Assign later</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo (int) $class['id']; ?>" <?php echo old('class_id') == $class['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($class['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian Name</label>
                        <input type="text" name="guardian_name" class="form-control" value="<?php echo e(old('guardian_name')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian Phone</label>
                        <input type="text" name="guardian_phone" class="form-control" value="<?php echo e(old('guardian_phone')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian Email</label>
                        <input type="email" name="guardian_email" class="form-control" value="<?php echo e(old('guardian_email')); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo e(old('address')); ?></textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">Create Student</button>
                    <a href="<?php echo e(url('?page=admin.users')); ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
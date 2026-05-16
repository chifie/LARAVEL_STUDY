<?php
$appName = app_config('app_name', 'School SMS');
$old = Session::get('_old_input', []);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo e(url('?page=admin.dashboard')); ?>"><?php echo e($appName); ?></a>
    </div>
</nav>

<div class="container py-4" style="max-width: 1050px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Student Profile</h1>
            <p class="text-muted mb-0">View and update student personal information.</p>
        </div>
        <a href="<?php echo e(url('?page=admin.students')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="post" action="<?php echo e(url('?page=admin.students.profile&student_id=' . (int) $student['id'])); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo e(old('full_name', $student['full_name'])); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $student['email'])); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $student['phone'])); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select</option>
                                    <option value="male" <?php echo old('gender', $student['gender']) === 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo old('gender', $student['gender']) === 'female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo old('gender', $student['gender']) === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="<?php echo e(old('date_of_birth', $student['date_of_birth'])); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Number</label>
                                <input type="text" class="form-control" value="<?php echo e($student['admission_number']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Current Class</label>
                                <input type="text" class="form-control" value="<?php echo e($student['class_name'] ?? '—'); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guardian Name</label>
                                <input type="text" name="guardian_name" class="form-control" value="<?php echo e(old('guardian_name', $student['guardian_name'])); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guardian Phone</label>
                                <input type="text" name="guardian_phone" class="form-control" value="<?php echo e(old('guardian_phone', $student['guardian_phone'])); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guardian Email</label>
                                <input type="email" name="guardian_email" class="form-control" value="<?php echo e(old('guardian_email', $student['guardian_email'])); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3"><?php echo e(old('address', $student['address'])); ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button class="btn btn-primary">Update Profile</button>
                            <a href="<?php echo e(url('?page=admin.students.history&student_id=' . (int) $student['id'])); ?>" class="btn btn-outline-dark">History</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 text-muted">Account Status</h2>
                    <p class="mb-1"><strong>Role:</strong> Student</p>
                    <p class="mb-1"><strong>Status:</strong> <?php echo e($student['status']); ?></p>
                    <p class="mb-0"><strong>Login Account:</strong> <?php echo ((int) $student['is_active'] === 1) ? 'Active' : 'Inactive'; ?></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-muted">Latest Enrollment</h2>
                    <?php if ($currentEnrollment): ?>
                        <p class="mb-1"><strong>Class:</strong> <?php echo e($currentEnrollment['class_name']); ?></p>
                        <p class="mb-1"><strong>Year:</strong> <?php echo e($currentEnrollment['year_name']); ?></p>
                        <p class="mb-0"><strong>Term:</strong> <?php echo e($currentEnrollment['term_name']); ?></p>
                    <?php else: ?>
                        <p class="text-muted mb-0">No enrollment record found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
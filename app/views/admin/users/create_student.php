<?php
$appName = app_config('app_name', 'School SMS');
$pageTitle = 'Create Student';
$pageSubtitle = 'Register a student account and attach the learner to a class.';
$pageActions = '<a href="' . e(url('?page=admin.users')) . '" class="btn btn-outline-secondary rounded-4"><i class="fa-solid fa-arrow-left me-2"></i>Back to Users</a>';

$classes = $classes ?? [];
$errors = flash('errors', []);
$error = $error ?? flash('error');
$success = $success ?? flash('success');

ob_start();
?>
<form method="post" action="<?php echo e(url('?page=admin.users.create-student')); ?>" class="row g-3">
    <?php echo csrf_field(); ?>

    <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control rounded-4" value="<?php echo e(old('full_name')); ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control rounded-4" value="<?php echo e(old('email')); ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control rounded-4" value="<?php echo e(old('phone')); ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Admission Number</label>
        <input type="text" name="admission_number" class="form-control rounded-4" value="<?php echo e(old('admission_number')); ?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select rounded-4">
            <option value="">Select</option>
            <option value="male" <?php echo old('gender') === 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo old('gender') === 'female' ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?php echo old('gender') === 'other' ? 'selected' : ''; ?>>Other</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control rounded-4" value="<?php echo e(old('date_of_birth')); ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Admission Date</label>
        <input type="date" name="admission_date" class="form-control rounded-4" value="<?php echo e(old('admission_date')); ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Class</label>
        <select name="class_id" class="form-select rounded-4">
            <option value="">Select class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo (int) $class['id']; ?>" <?php echo old('class_id') == $class['id'] ? 'selected' : ''; ?>>
                    <?php echo e($class['class_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Guardian Name</label>
        <input type="text" name="guardian_name" class="form-control rounded-4" value="<?php echo e(old('guardian_name')); ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Guardian Phone</label>
        <input type="text" name="guardian_phone" class="form-control rounded-4" value="<?php echo e(old('guardian_phone')); ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Guardian Email</label>
        <input type="email" name="guardian_email" class="form-control rounded-4" value="<?php echo e(old('guardian_email')); ?>">
    </div>

    <div class="col-12">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control rounded-4" rows="3"><?php echo e(old('address')); ?></textarea>
    </div>

    <div class="col-12">
        <div class="alert alert-info rounded-4 border-0 mb-0">
            A temporary password will be generated automatically and the student must change it after the first login.
        </div>
    </div>

    <div class="col-12 d-grid d-md-flex gap-2 justify-content-md-end">
        <button type="submit" class="btn btn-success rounded-4 px-4">
            <i class="fa-solid fa-user-graduate me-2"></i>
            Create Student
        </button>
    </div>
</form>
<?php
$bodyHtml = ob_get_clean();
$content = render_view('layouts.components.form_card', [
    'title' => 'Student Details',
    'description' => 'Enter the student profile information below.',
    'bodyHtml' => $bodyHtml,
]);

require app_path('views/layouts/admin.php');

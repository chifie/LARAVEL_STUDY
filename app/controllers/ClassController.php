<?php
declare(strict_types=1);

class ClassController
{
    public function years()
    {
        Auth::requireRole(['super_admin']);

        $years = AcademicYear::all();
        $currentYear = AcademicYear::current();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/academic_years/index.php');
    }

    public function storeYear()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.structure.years'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.structure.years'));
        }

        $data = [
            'year_name' => trim((string) ($_POST['year_name'] ?? '')),
            'start_date' => trim((string) ($_POST['start_date'] ?? '')),
            'end_date' => trim((string) ($_POST['end_date'] ?? '')),
            'is_current' => isset($_POST['is_current']) ? 1 : 0,
        ];

        $errors = Validator::validate($data, [
            'year_name' => 'required|min:4|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        if (!empty($errors) && strtotime($data['start_date']) !== false && strtotime($data['end_date']) !== false) {
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $errors['end_date'] = 'End date must be after start date.';
            }
        }

        if (AcademicYear::existsByName($data['year_name'])) {
            $errors['year_name'] = 'This academic year already exists.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.structure.years'));
        }

        try {
            Database::beginTransaction();

            $yearId = AcademicYear::create($data);

            if ((int) $data['is_current'] === 1) {
                AcademicYear::setCurrent($yearId);
            }

            ActivityLog::create(
                Auth::id(),
                'CREATE_ACADEMIC_YEAR',
                'academic_year',
                $yearId,
                'Created academic year ' . $data['year_name']
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', 'Academic year created successfully.');
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }
            Session::flash('error', 'Could not create academic year: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.structure.years'));
    }

    public function terms()
    {
        Auth::requireRole(['super_admin']);

        $years = AcademicYear::all();
        $terms = Term::allWithYears();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/terms/index.php');
    }

    public function storeTerm()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.structure.terms'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.structure.terms'));
        }

        $data = [
            'academic_year_id' => trim((string) ($_POST['academic_year_id'] ?? '')),
            'term_name' => trim((string) ($_POST['term_name'] ?? '')),
            'start_date' => trim((string) ($_POST['start_date'] ?? '')),
            'end_date' => trim((string) ($_POST['end_date'] ?? '')),
            'is_current' => isset($_POST['is_current']) ? 1 : 0,
        ];

        $errors = Validator::validate($data, [
            'academic_year_id' => 'required|integer',
            'term_name' => 'required|min:3|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $year = AcademicYear::find($data['academic_year_id']);
        if (!$year) {
            $errors['academic_year_id'] = 'Selected academic year does not exist.';
        }

        if ($year && Term::existsInYear($data['academic_year_id'], $data['term_name'])) {
            $errors['term_name'] = 'This term already exists for the selected academic year.';
        }

        if (!empty($errors) && strtotime($data['start_date']) !== false && strtotime($data['end_date']) !== false) {
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $errors['end_date'] = 'End date must be after start date.';
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.structure.terms'));
        }

        try {
            Database::beginTransaction();

            $termId = Term::create($data);

            if ((int) $data['is_current'] === 1) {
                Term::setCurrent($termId, $data['academic_year_id']);
            }

            ActivityLog::create(
                Auth::id(),
                'CREATE_TERM',
                'term',
                $termId,
                'Created term ' . $data['term_name']
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', 'Term created successfully.');
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }
            Session::flash('error', 'Could not create term: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.structure.terms'));
    }

    public function classes()
    {
        Auth::requireRole(['super_admin']);

        $classes = SchoolClass::allWithRelations();
        $years = AcademicYear::all();
        $teachers = Teacher::allWithUsers();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/classes/index.php');
    }

    public function storeClass()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.structure.classes'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.structure.classes'));
        }

        $data = [
            'class_name' => trim((string) ($_POST['class_name'] ?? '')),
            'grade_level' => trim((string) ($_POST['grade_level'] ?? '')),
            'stream' => trim((string) ($_POST['stream'] ?? '')),
            'academic_year_id' => trim((string) ($_POST['academic_year_id'] ?? '')),
            'capacity' => trim((string) ($_POST['capacity'] ?? '')),
            'class_teacher_id' => trim((string) ($_POST['class_teacher_id'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'class_name' => 'required|min:1|max:100',
            'grade_level' => 'nullable|max:50',
            'stream' => 'nullable|max:50',
            'academic_year_id' => 'required|integer',
            'capacity' => 'nullable|integer',
            'class_teacher_id' => 'nullable|integer',
        ]);

        $year = AcademicYear::find($data['academic_year_id']);
        if (!$year) {
            $errors['academic_year_id'] = 'Selected academic year does not exist.';
        }

        if (!empty($data['class_teacher_id'])) {
            $teacherExists = Database::query(
                'SELECT COUNT(*) AS total FROM teachers WHERE id = :id',
                [':id' => (int) $data['class_teacher_id']]
            )->fetch(PDO::FETCH_ASSOC);

            if ((int) $teacherExists['total'] === 0) {
                $errors['class_teacher_id'] = 'Selected teacher does not exist.';
            }
        }

        if (SchoolClass::existsInYear($data['class_name'], $data['academic_year_id'], $data['stream'])) {
            $errors['class_name'] = 'This class already exists for the selected academic year and stream.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.structure.classes'));
        }

        try {
            $classId = SchoolClass::create($data);

            ActivityLog::create(
                Auth::id(),
                'CREATE_CLASS',
                'class',
                $classId,
                'Created class ' . $data['class_name']
            );

            Session::forget('_old_input');
            Session::flash('success', 'Class created successfully.');
        } catch (Throwable $e) {
            Session::flash('error', 'Could not create class: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.structure.classes'));
    }

    public function toggleClassStatus()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.structure.classes'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.structure.classes'));
        }

        $classId = (int) ($_POST['class_id'] ?? 0);

        if (SchoolClass::toggleStatus($classId)) {
            ActivityLog::create(
                Auth::id(),
                'TOGGLE_CLASS_STATUS',
                'class',
                $classId,
                'Changed class status'
            );
            Session::flash('success', 'Class status updated.');
        } else {
            Session::flash('error', 'Could not update class status.');
        }

        Response::redirect(url('?page=admin.structure.classes'));
    }

    public function assignments()
    {
        Auth::requireRole(['super_admin']);

        $assignments = ClassSubject::allWithRelations();
        $classes = SchoolClass::allWithRelations();
        $subjects = Subject::all();
        $teachers = Teacher::allWithUsers();
        $years = AcademicYear::all();

        $currentYear = AcademicYear::current();
        $terms = $currentYear ? Term::allByYear($currentYear['id']) : [];

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/assignments/index.php');
    }

    public function storeAssignment()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.structure.assignments'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.structure.assignments'));
        }

        $data = [
            'class_id' => trim((string) ($_POST['class_id'] ?? '')),
            'subject_id' => trim((string) ($_POST['subject_id'] ?? '')),
            'teacher_id' => trim((string) ($_POST['teacher_id'] ?? '')),
            'academic_year_id' => trim((string) ($_POST['academic_year_id'] ?? '')),
            'term_id' => trim((string) ($_POST['term_id'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'class_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'teacher_id' => 'nullable|integer',
            'academic_year_id' => 'required|integer',
            'term_id' => 'required|integer',
        ]);

        $class = SchoolClass::find($data['class_id']);
        $subject = Subject::find($data['subject_id']);
        $year = AcademicYear::find($data['academic_year_id']);
        $term = Term::find($data['term_id']);

        if (!$class) {
            $errors['class_id'] = 'Selected class does not exist.';
        }

        if (!$subject) {
            $errors['subject_id'] = 'Selected subject does not exist.';
        }

        if (!$year) {
            $errors['academic_year_id'] = 'Selected academic year does not exist.';
        }

        if (!$term) {
            $errors['term_id'] = 'Selected term does not exist.';
        }

        if ($term && $year && (int) $term['academic_year_id'] !== (int) $year['id']) {
            $errors['term_id'] = 'The selected term does not belong to the selected academic year.';
        }

        if (!empty($data['teacher_id'])) {
            $teacherExists = Database::query(
                'SELECT COUNT(*) AS total FROM teachers WHERE id = :id',
                [':id' => (int) $data['teacher_id']]
            )->fetch(PDO::FETCH_ASSOC);

            if ((int) $teacherExists['total'] === 0) {
                $errors['teacher_id'] = 'Selected teacher does not exist.';
            }
        }

        if (!$errors && ClassSubject::exists($data['class_id'], $data['subject_id'], $data['academic_year_id'], $data['term_id'])) {
            $errors['subject_id'] = 'This class/subject assignment already exists for the selected year and term.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.structure.assignments'));
        }

        try {
            $assignmentId = ClassSubject::create($data);

            ActivityLog::create(
                Auth::id(),
                'CREATE_ASSIGNMENT',
                'class_subject',
                $assignmentId,
                'Assigned subject to class'
            );

            Session::forget('_old_input');
            Session::flash('success', 'Teacher assignment saved successfully.');
        } catch (Throwable $e) {
            Session::flash('error', 'Could not save assignment: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.structure.assignments'));
    }
}
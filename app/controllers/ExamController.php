<?php
declare(strict_types=1);

class ExamController
{
    public function index()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        $user = Auth::user();
        $exams = Exam::allWithRelations($user);
        $success = flash('success');
        $error = flash('error');

        if (Auth::hasRole('teacher')) {
            require app_path('views/teacher/exams/index.php');
            return;
        }

        require app_path('views/admin/exams/index.php');
    }

    public function create()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        $assignments = Exam::allForExamCreation();
        $success = flash('success');
        $error = flash('error');

        if (Auth::hasRole('teacher')) {
            require app_path('views/teacher/exams/create.php');
            return;
        }

        require app_path('views/admin/exams/create.php');
    }

    public function store()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        if (!is_post()) {
            Response::redirect(Auth::hasRole('teacher') ? url('?page=teacher.exams.create') : url('?page=admin.exams.create'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect($this->createUrl());
        }

        $data = [
            'assignment_id' => trim((string) ($_POST['assignment_id'] ?? '')),
            'exam_name' => trim((string) ($_POST['exam_name'] ?? '')),
            'exam_date' => trim((string) ($_POST['exam_date'] ?? '')),
            'total_marks' => trim((string) ($_POST['total_marks'] ?? '100')),
        ];

        $errors = Validator::validate($data, [
            'assignment_id' => 'required|integer',
            'exam_name' => 'required|min:3|max:150',
            'exam_date' => 'nullable|date',
            'total_marks' => 'required|numeric',
        ]);

        $assignment = ClassSubject::find($data['assignment_id']);
        if (!$assignment) {
            $errors['assignment_id'] = 'Selected class-subject assignment does not exist.';
        }

        if ($assignment && Auth::hasRole('teacher')) {
            $teacher = Teacher::findByUserId(Auth::id());
            if (!$teacher || (int) $assignment['teacher_id'] !== (int) $teacher['id']) {
                $errors['assignment_id'] = 'You are not allowed to create exams for this assignment.';
            }
        }

        if ($assignment && !$this->assignmentIsActive($assignment)) {
            $errors['assignment_id'] = 'The selected assignment is not valid.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect($this->createUrl());
        }

        try {
            $examId = Exam::create([
                'exam_name' => $data['exam_name'],
                'academic_year_id' => $assignment['academic_year_id'],
                'term_id' => $assignment['term_id'],
                'class_id' => $assignment['class_id'],
                'subject_id' => $assignment['subject_id'],
                'exam_date' => $data['exam_date'] ?: null,
                'total_marks' => $data['total_marks'],
                'created_by' => Auth::id(),
            ]);

            ActivityLog::create(
                Auth::id(),
                'CREATE_EXAM',
                'exam',
                $examId,
                'Created exam ' . $data['exam_name']
            );

            Session::forget('_old_input');
            Session::flash('success', 'Exam created successfully.');
        } catch (Throwable $e) {
            Session::flash('error', 'Could not create exam: ' . $e->getMessage());
        }

        Response::redirect($this->indexUrl());
    }

    public function marks()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        $examId = isset($_GET['exam_id']) ? (int) $_GET['exam_id'] : 0;
        $exam = $examId > 0 ? Exam::find($examId) : null;

        if (!$exam) {
            Session::flash('error', 'Exam not found.');
            Response::redirect($this->indexUrl());
        }

        if (!Exam::canAccessExam($exam, Auth::user())) {
            Response::forbidden('You are not allowed to access this exam.');
        }

        $students = Exam::studentsByExam($examId);
        $results = Result::byExam($examId);
        $resultsByStudent = [];
        foreach ($results as $row) {
            $resultsByStudent[(int) $row['student_id']] = $row;
        }

        $success = flash('success');
        $error = flash('error');

        if (Auth::hasRole('teacher')) {
            require app_path('views/teacher/exams/marks.php');
            return;
        }

        require app_path('views/admin/exams/marks.php');
    }

    public function storeMarks()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        if (!is_post()) {
            Response::redirect($this->indexUrl());
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect($this->indexUrl());
        }

        $examId = (int) ($_POST['exam_id'] ?? 0);
        $exam = $examId > 0 ? Exam::find($examId) : null;

        if (!$exam) {
            Session::flash('error', 'Exam not found.');
            Response::redirect($this->indexUrl());
        }

        if (!Exam::canAccessExam($exam, Auth::user())) {
            Response::forbidden('You are not allowed to access this exam.');
        }

        $marks = isset($_POST['marks']) && is_array($_POST['marks']) ? $_POST['marks'] : [];

        try {
            $saved = Result::saveBulk($examId, Auth::id(), $marks);

            ActivityLog::create(
                Auth::id(),
                'SAVE_RESULTS',
                'exam_result',
                $examId,
                'Saved results for exam ID ' . $examId . ' with ' . $saved . ' records'
            );

            Session::flash('success', 'Marks saved successfully.');
        } catch (Throwable $e) {
            Session::flash('error', 'Could not save marks: ' . $e->getMessage());
        }

        Response::redirect($this->marksUrl($examId));
    }

    public function results()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        $examId = isset($_GET['exam_id']) ? (int) $_GET['exam_id'] : 0;
        $exam = $examId > 0 ? Exam::find($examId) : null;

        if (!$exam) {
            Session::flash('error', 'Exam not found.');
            Response::redirect($this->indexUrl());
        }

        if (!Exam::canAccessExam($exam, Auth::user())) {
            Response::forbidden('You are not allowed to access this exam.');
        }

        $results = Result::byExam($examId);
        $summary = Result::summaryByExam($examId);

        $marksTotal = 0;
        $marksCount = count($results);
        foreach ($results as $row) {
            $marksTotal += (float) $row['marks_obtained'];
        }

        $average = $marksCount > 0 ? ($marksTotal / $marksCount) : 0;

        $success = flash('success');
        $error = flash('error');

        if (Auth::hasRole('teacher')) {
            require app_path('views/teacher/exams/results.php');
            return;
        }

        require app_path('views/admin/exams/results.php');
    }

    public function studentResults()
    {
        Auth::requireLogin();

        if (!Auth::hasRole('student')) {
            Response::forbidden('Only students can access this page.');
        }

        $student = Student::findDetailedByUserId(Auth::id());
        if (!$student) {
            Response::forbidden('Student record not found.');
        }

        $results = Result::byStudent((int) $student['id']);

        require app_path('views/student/my_results.php');
    }

    private function assignmentIsActive(array $assignment)
    {
        return !empty($assignment['class_id'])
            && !empty($assignment['subject_id'])
            && !empty($assignment['academic_year_id'])
            && !empty($assignment['term_id']);
    }

    private function indexUrl()
    {
        return Auth::hasRole('teacher') ? url('?page=teacher.exams') : url('?page=admin.exams');
    }

    private function createUrl()
    {
        return Auth::hasRole('teacher') ? url('?page=teacher.exams.create') : url('?page=admin.exams.create');
    }

    private function marksUrl($examId)
    {
        return (Auth::hasRole('teacher') ? url('?page=teacher.exams.marks&exam_id=' . (int) $examId) : url('?page=admin.exams.marks&exam_id=' . (int) $examId));
    }
}
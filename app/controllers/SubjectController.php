<?php
declare(strict_types=1);

class SubjectController
{
    public function index()
    {
        Auth::requireRole(['super_admin']);

        $subjects = Subject::all();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/subjects/index.php');
    }

    public function store()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.structure.subjects'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.structure.subjects'));
        }

        $data = [
            'subject_name' => trim((string) ($_POST['subject_name'] ?? '')),
            'subject_code' => strtoupper(trim((string) ($_POST['subject_code'] ?? ''))),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'subject_name' => 'required|min:2|max:120',
            'subject_code' => 'required|min:2|max:50',
            'description' => 'nullable|max:255',
        ]);

        if (Subject::existsByCode($data['subject_code'])) {
            $errors['subject_code'] = 'This subject code already exists.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.structure.subjects'));
        }

        try {
            $subjectId = Subject::create($data);

            ActivityLog::create(
                Auth::id(),
                'CREATE_SUBJECT',
                'subject',
                $subjectId,
                'Created subject ' . $data['subject_name']
            );

            Session::forget('_old_input');
            Session::flash('success', 'Subject created successfully.');
        } catch (Throwable $e) {
            Session::flash('error', 'Could not create subject: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.structure.subjects'));
    }

    public function toggleStatus()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.structure.subjects'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.structure.subjects'));
        }

        $subjectId = (int) ($_POST['subject_id'] ?? 0);

        if (Subject::toggleStatus($subjectId)) {
            ActivityLog::create(
                Auth::id(),
                'TOGGLE_SUBJECT_STATUS',
                'subject',
                $subjectId,
                'Changed subject status'
            );
            Session::flash('success', 'Subject status updated.');
        } else {
            Session::flash('error', 'Could not update subject status.');
        }

        Response::redirect(url('?page=admin.structure.subjects'));
    }
}
<?php
header('Content-Type: application/json');
include("../config/database.php");
include("../config/auth_check.php");

// ============================================================
//  HELPER – run inside a transaction so both inserts succeed
//  or both roll back together.
// ============================================================

// ---------- Collect & sanitize ----------
$department_id      = $_POST['department_id']      ?? '';
$task_type          = $_POST['task_type']           ?? '';
$recurring_type     = $_POST['recurring_type']      ?? '';
$recurring_interval = $_POST['recurring_interval']  ?? '';
$priority           = $_POST['priority']            ?? 'medium';
$title              = trim($_POST['task_title']      ?? '');
$description        = trim($_POST['description']     ?? '');
$assign_to          = $_POST['assign_to']            ?? '';
$assigned_by        = $_POST['assigned_by']          ?? 1;
$start_date         = $_POST['start_date']           ?? null;
$due_date           = $_POST['due_date']             ?? '';
$est_hours          = $_POST['est_hours']            ?? null;
$remind_before      = $_POST['remind_before']        ?? null;
$alert_not_started  = $_POST['alert_not_started']    ?? null;
$escalate_after     = $_POST['escalate_after']       ?? null;
$status             = $_POST['status']               ?? 'active';

$errors = [];

// ---------- Common validation ----------
if ($department_id === '' || $department_id === 'Select') {
    $errors['department_id'] = 'Department is required.';
}
if ($task_type === '' || $task_type === 'Select') {
    $errors['task_type'] = 'Task type is required.';
}
if ($title === '') {
    $errors['title'] = 'Task title is required.';
}
if ($assign_to === '' || $assign_to === 'Select Staff') {
    $errors['assign_to'] = 'Assignee is required.';
}
if ($due_date === '') {
    $errors['due_date'] = 'Due date is required.';
}
if ($start_date && $due_date && $start_date > $due_date) {
    $errors['due_date'] = 'Due date cannot be before start date.';
}

$jsonData      = null;
$designTargets = [];

switch ($department_id) {

    // ── Design Studio ────────────────────────────────────────
    case '4':
        $d = $_POST['design'] ?? [];
        
        // Meta stored in task_master (no targets array here)
        $meta = [
            'sub_type'       => $d['sub_type']       ?? null,
            'designer_level' => $d['designer_level']  ?? null,
            'sketch_format'  => $d['sketch_format']   ?? null,
        ];
        $jsonData = json_encode($meta, JSON_UNESCAPED_UNICODE);

        // Targets go to separate table
        $catIds   = $d['category_id']    ?? [];
        $weekly   = $d['weekly_target']  ?? [];
        $monthly  = $d['monthly_target'] ?? [];
        $budget   = $d['budget']         ?? [];
        $fabric   = $d['fabric']         ?? [];
        $workType = $d['work_type']      ?? [];
        $occasion = $d['occasion']       ?? [];
        $refId    = $d['reference_id']   ?? [];
        $palette  = $d['color_palette']  ?? [];

        foreach ($catIds as $i => $catId) {
            // Skip completely empty rows
            if (empty($weekly[$i]) && empty($monthly[$i]) && empty($budget[$i])) {
                continue;
            }
            $designTargets[] = [
                'category_id'   => (int) $catId,
                'weekly'        => (int)   ($weekly[$i]   ?? 0),
                'monthly'       => (int)   ($monthly[$i]  ?? 0),
                'budget'        => (float) ($budget[$i]   ?? 0),
                'fabric'        => $fabric[$i]   ?? null,
                'work_type'     => $workType[$i] ?? null,
                'occasion'      => $occasion[$i] ?? null,
                'reference_id'  => $refId[$i]    ?? null,
                'color_palette' => $palette[$i]  ?? null,
            ];
        }
        break;
}

// ---------- Return validation errors ----------
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ---------- Generate codes ----------
$task_no     = generate_task_no();
$con->begin_transaction();

try {

    // ── Step 1: task_master ──────────────────────────────────
    $stmt = $con->prepare("
        INSERT INTO task_master
            (task_no, department_id, task_type, priority, title, description,
             assigned_to, assigned_by, start_date, deadline_time, est_hours,
             remind_before, alert_not_started, escalate_after,
             status, department_details, recurring_type, recurring_interval)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $task_no,
        $department_id,
        $task_type,
        $priority,
        $title,
        $description,
        $assign_to,
        $assigned_by,
        $start_date  ?: null,
        $due_date,
        $est_hours   ?: null,
        $remind_before,
        $alert_not_started,
        $escalate_after,
        $status,
        $jsonData,           // meta only (no targets array)
        $recurring_type,
        $recurring_interval,
    ]);

    $task_id = $con->insert_id;
    if (!empty($designTargets)) {

        $targetStmt = $con->prepare("
            INSERT INTO design
                (task_id,design_name,design_code, style, weekly_target, monthly_target, budget,
                 fabric, work_type, occasion, color,assign_to,due_date)
            VALUES
                (?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        foreach ($designTargets as $row) {
            $design_code = generate_design_code();
            $targetStmt->execute([
                $task_id,
                $title,
                $design_code,
                $row['category_id'],
                $row['weekly'],
                $row['monthly'],
                $row['budget'],
                $row['fabric'],
                $row['work_type'],
                $row['occasion'],
                $row['color_palette'],
                $assign_to,
                $due_date
            ]);
        }
    }

    $con->commit();

    echo json_encode([
        'success'        => true,
        'task_id'        => $task_id,
        'targets_saved'  => count($designTargets),
    ]);

} catch (Exception $e) {
    $con->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
    ]);
}
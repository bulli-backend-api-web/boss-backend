<?php
require_once '../config/database.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors',0);

$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

$search = $_POST['search']['value'] ?? '';
$status = $_POST['status'] ?? '';

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = ($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$columns = [
    0 => 'id',
    2 => 'title'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';

$task_type = !empty($_POST['task_type']) ? $_POST['task_type'] : "";
 
$where = " WHERE 1=1 ";
$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND title LIKE '%$search%'";
}

if($task_type && $task_type!='all'){
    $where .= " AND task_type = '$task_type'";
}


/* 📊 TOTAL RECORDS (no db_row) */
$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM task_master");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM task_master $where");

/* bind params dynamically */
if (!empty($params)) {
    $types = '';
    $values = [];

    foreach ($params as $val) {
        $types .= 's';
        $values[] = $val;
    }

    $stmtFiltered->bind_param($types, ...$values);
}

$stmtFiltered->execute();

$result = $stmtFiltered->get_result();
$row = $result->fetch_assoc();

$totalFiltered = $row['total'];

/* 📦 MAIN DATA QUERY */
$sql = "SELECT id,title,task_type,assigned_to,assigned_by,priority,status,department_id,deadline_time,assigned_by,created_at from task_master $where ORDER BY $orderColumn $orderDir LIMIT $start, $length";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* 🎯 FORMAT DATA */
$data = [];
$sr = $start + 1;

foreach ($rows as $row) {

    $id = $row['id'];
    $username = [];
    if($row['assigned_to']){
        $user_sql = "SELECT name from user where id IN ({$row['assigned_to']})";
        $user_res = $con->query($user_sql);
        if($user_res && $user_res->num_rows > 0){
            while($user_row = $user_res->fetch_assoc()){
                $username[] = $user_row['name'];
            }
        }
    }
    $assing_by = '';
    if($row['assigned_by']){
        $assign_sql = "SELECT name from user where id  = {$row['assigned_by']}";
        $assign_res = $con->query($assign_sql);
        if($assign_res && $assign_res->num_rows > 0){
            $assign_row = $assign_res->fetch_assoc();
            $assing_by = $assign_row['name'];
        }
    }
    $department_name = "NA";
    if($row['department_id']){
        $dpt_sql = "SELECT department_name from departments where id = {$row['department_id']}";
        $dpt_res = $con->query($dpt_sql);
        if($dpt_res && $dpt_res->num_rows > 0){
            $dpt_row = $dpt_res->fetch_assoc();
            $department_name = $dpt_row['department_name'];
            
        }
    }
    $actions = '';
    $data[] = [
        'sr_no' => $sr++,
        'task' => '<div class="task-title">'.$row['title'].'</div><div class="task-subtitle">Assigned by: '.$assing_by.' · '.date('j M, Y', strtotime($row['created_at'])).'</div>',
        'task_type' => '<span class="badge badge-light-primary">'.ucfirst($row['task_type']).'</span>',
        'dept' => $department_name,
        'staff' => implode("<br/>",$username),
        'priority' => '<span class="priority-'. strtolower($row['priority']).'"> ● '.ucfirst($row['priority']).'</span>',
        'deadline' => date('d M h:i A', strtotime($row['deadline_time'])),
        'status' => getTaskStatusBadge($row['status']),
        'actions' => $actions
    ];
}

/* 📤 OUTPUT */
echo json_encode([
    "draw" => (int)$draw,
    "recordsTotal" => (int)$totalRecords,
    "recordsFiltered" => (int)$totalFiltered,
    "data" => $data
]);
exit;
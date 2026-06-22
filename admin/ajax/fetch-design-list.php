<?php
require_once '../config/database.php';

header('Content-Type: application/json');


$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

$search = $_POST['search']['value'] ?? '';
$status = $_POST['status'] ?? '';
$design_type = isset($_POST['design_type']) ? $_POST['design_type'] : "";

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = ($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$columns = [
    0 => 'd.id',
    1 => 'd.design_name',
    2 => 'd.status'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'd.id';
if($design_type == 'submited_design'){
    $where = " WHERE is_design_upload = 1";
}else if($design_type == 'approve-design'){
    $where = " WHERE d.status = 1";
}else{
    $where = " WHERE 1=1 ";
}

$params = [];

/* 🔍 Search */
if (!empty($search)) {
    $where .= " AND d.design_name LIKE '%$search%'";
}

/* 📌 Status filter */
if ($status !== '') {
    $where .= " AND d.status = $status";
}

/* 📊 TOTAL RECORDS (no db_row) */
$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM design");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

/* 📊 FILTERED RECORDS (no db_row) */
$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM design d $where");

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
$sql = "
    SELECT 
        d.id,
        d.design_name,
        d.design_code,
        d.image,
        d.occasion,
        d.color,
        d.style,
        d.min_sketch,
        d.assign_to,
        d.due_date,
        d.created_by,
        d.created_at,
        d.upload_date,
        d.status,
        d.remarks,
        d.approved_date,
        u1.name AS upload_by_name,
        u2.name AS approved_by_name,
        u3.name AS assign_by_name,
        c.name as cat_name
    FROM design d
    LEFT JOIN user u1 ON u1.id = d.upload_by
    LEFT JOIN user u2 ON u2.id = d.approved_by
    LEFT JOIN user u3 ON u3.id = d.assign_to
    LEFT JOIN category c ON c.id = d.style
    $where
    ORDER BY $orderColumn $orderDir
    LIMIT $start, $length
";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* 🎯 FORMAT DATA */
$data = [];
$sr = $start + 1;

foreach ($rows as $row) {

    $id = $row['id'];

    $image = !empty($row['image'])
        ? $site_path . '/uploads/design/' . $row['image']
        : $site_path . '/assets/media/misc/1.png';

    /* Status badge */
    if ($row['status'] == 0) {
        $statusBadge = '<span class="badge badge-light-warning fw-bold px-4 py-3">Pending</span>';
    } elseif ($row['status'] == 1) {
        $statusBadge = '<span class="badge badge-light-success fw-bold px-4 py-3">Approved</span>';
    } elseif ($row['status'] == 2) {
        $statusBadge = '<span class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>';
    }elseif ($row['status'] == 3) {
        $statusBadge = '<span class="badge badge-light-primary fw-bold px-4 py-3">Modification Needed</span>';
    }elseif ($row['status'] == 4) {
        $statusBadge = '<span class="badge badge-light-success fw-bold px-4 py-3">In Review</span>';
    } else {
        $statusBadge = '<span class="badge badge-light-secondary">Unknown</span>';
    }

    $upload_by = $row['upload_by_name'] ?? 'NA';
    $approved_by = $row['approved_by_name'] ?? 'NA';

    /* Actions */
    $actions = '
        <a href="'.$site_path.'/view-design?id='.my_simple_crypt($id,'encrypt_1').'" 
           class="btn btn-light-primary btn-sm">
         <i class="fa fa-edit">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        <span>Edit</span>
        </a>
    ';
    
    $design_images = "<div style='display:flex;align-items:center;gap:8px;flex-wrap:wrap;'>";

    $design_image_sql = "SELECT image FROM design_images WHERE design_id = {$row['id']}";
    $design_image_res = $con->query($design_image_sql);

    if ($design_image_res && $design_image_res->num_rows > 0) {

        while ($design_image_row = $design_image_res->fetch_assoc()) {

            $img = trim($design_image_row['image']);

            $imagePath = '../../uploads/design/' . $img;

            $design_images .= "
                <img data-src='$imagePath'
                     src='$imagePath'
                     style='width:50px;
                            height:50px;
                            border-radius:8px;
                            border:2px solid #ccc;
                            cursor:pointer;
                            object-fit:cover;'
                     onclick='openPopupCentered(this.src)'
                     class='lazy-img'
                     loading='lazy' />
            ";
        }
    }

    $design_images .= "</div>";

    $data[] = [
        'sr_no' => $sr++,
        'design_name' => $row['design_name'],
        'design_code' => $row['design_code'],
        'occasion' => $row['occasion'],
        'color' => $row['color'],
        'style' => $row['cat_name'],
        'min_sketch' => $row['min_sketch'],
        'sketch'=>$design_images,
        'assign_to' => $row['assign_by_name'],
        'created_date' => !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : "NA",
        'due_date'=>!empty($row['due_date']) ? date('d-m-Y', strtotime($row['due_date'])) : "NA",
        'status' => $statusBadge,
        'remarks' => $row['remarks'],
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
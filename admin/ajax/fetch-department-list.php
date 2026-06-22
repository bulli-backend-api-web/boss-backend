<?php
include("../config/database.php"); // $con is mysqli connection

$columns = [
    0 => "id",
    1 => "name"
];

// DataTables parameters
$orderColumnIndex = $_POST['order'][0]['column'] ?? 3;
$orderDir = $_POST['order'][0]['dir'] ?? 'desc';
$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$orderColumn = $columns[$orderColumnIndex] ?? "department_name";
$orderDir = strtoupper($orderDir) === "ASC" ? "ASC" : "DESC";

// ================== BASE QUERY ==================
$baseQry = " FROM departments WHERE 1=1 ";
$whereParts = [];
$bindTypes = "";
$bindValues = [];

// Search value filter
if (!empty($searchValue)) {
    $whereParts[] = "(department_name LIKE ? )";
    $likeSearch = "%$searchValue%";
    $bindTypes .= "s";
    $bindValues[] = &$likeSearch;
}

$whereSql = "";
if (!empty($whereParts)) {
    $whereSql = " AND " . implode(" AND ", $whereParts);
}

// ================== TOTAL RECORDS ==================
$totalRecordsQuery = $con->prepare("SELECT COUNT(*) AS allcount FROM departments");
$totalRecordsQuery->execute();
$totalRecordsQuery->bind_result($totalRecords);
$totalRecordsQuery->fetch();
$totalRecordsQuery->close();

// ================== FILTERED RECORDS ==================
$totalFilteredSql = "SELECT COUNT(*) AS allcount " . $baseQry . $whereSql;
$totalFilteredQuery = $con->prepare($totalFilteredSql);

if (!empty($bindTypes)) {
    $params = array_merge([$bindTypes], $bindValues);
    call_user_func_array([$totalFilteredQuery, 'bind_param'], $params);
}

$totalFilteredQuery->execute();
$totalFilteredQuery->bind_result($totalFiltered);
$totalFilteredQuery->fetch();
$totalFilteredQuery->close();

// ================== FETCH DATA ==================
$dataSql = "SELECT id, department_name "
         . $baseQry . $whereSql
         . " ORDER BY $orderColumn $orderDir LIMIT ?, ?";

$stmt = $con->prepare($dataSql);

$bindTypesData = $bindTypes . "ii";
$bindValuesData = array_merge($bindValues, [ &$start, &$length ]);

$params = array_merge([$bindTypesData], $bindValuesData);
call_user_func_array([$stmt, 'bind_param'], $params);

$stmt->execute();
$result = $stmt->get_result();

$data = [];
$srno = 1;
while ($r = $result->fetch_assoc()) {
    /* Actions */
    $actions = '
        <a href="javascript:void(0);" class="btn btn-light-primary btn-sm edit-dept" data-bs-toggle="modal" data-bs-target="#kt_modal_update_dept" data-name="'.$r['department_name'].'" data-id="'.$r['id'].'"><i class="fa fa-edit">
            <span class="path1"></span>
            <span class="path2"></span>
        </i><span>Edit</span></a>&nbsp;&nbsp;<a href="#" data-action="'.$site_path.'/ajax/ajax-delete-master-data.php" data-id="'.$r['id'].'" class="btn btn-light-danger btn-sm delete_dept" ><i class="fa fa-trash">
            <span class="path1"></span>
            <span class="path2"></span>
        </i><span>Delete</span></a>';
    $data[] = [
        "sr_no" => $srno,
        "name" => $r['department_name'],
        "actions" => $actions
    ];
    $srno++;
}

$stmt->close();

// ================== RESPONSE ==================
$response = [
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
exit;

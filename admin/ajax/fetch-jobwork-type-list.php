<?php
include("../config/database.php"); // $con is mysqli connection

$columns = [
    0 => "id",
    1 => "name",
    2 => "amount",
];

// DataTables parameters
$orderColumnIndex = $_POST['order'][0]['column'] ?? 3;
$orderDir = $_POST['order'][0]['dir'] ?? 'desc';
$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$orderColumn = $columns[$orderColumnIndex] ?? "id";
$orderDir = strtoupper($orderDir) === "ASC" ? "ASC" : "DESC";

// ================== BASE QUERY ==================
$baseQry = " FROM jobwork_type WHERE 1=1 ";
$whereParts = [];
$bindTypes = "";
$bindValues = [];

// Search value filter
if (!empty($searchValue)) {
    $whereParts[] = "(name LIKE ? )";
    $likeSearch = "%$searchValue%";
    $bindTypes .= "s";
    $bindValues[] = &$likeSearch;
}

$whereSql = "";
if (!empty($whereParts)) {
    $whereSql = " AND " . implode(" AND ", $whereParts);
}

// ================== TOTAL RECORDS ==================
$totalRecordsQuery = $con->prepare("SELECT COUNT(*) AS allcount FROM jobwork_type");
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
$dataSql = "SELECT id, name, amount,status,created_by"
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
    $statusBadge = $r['status'] == 1 
        ? '<span class="badge badge-light-success">Active</span>' 
        : '<span class="badge badge-light-danger">Inactive</span>';
    $data[] = [
        "sr_no" => $srno,
        "namae" => $r['name'],
        "amount" => $r['amount'],
        "status" => $statusBadge,
        "actions" => '<a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions 
                        <i class="ki-outline ki-down fs-5 ms-1"></i></a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#kt_modal_update_jobwork_type" class="menu-link px-3 edit-jobwork-type" data-jobwork-name="'.$r['name'].'" data-amount="'.$r['amount'].'" data-status="'.$r['status'].'" data-id="'.$r['id'].'">Edit</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" data-action="'.$site_path.'/ajax/ajax-delete-master-data" data-id="'.$r['id'].'" class="menu-link px-3 delete_jobwork" >Delete</a>
                            </div>
                        </div>'
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

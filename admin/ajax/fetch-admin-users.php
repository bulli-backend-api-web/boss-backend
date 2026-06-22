<?php
include("../config/database.php"); // $con is mysqli connection

$columns = [
    0 => "username",
    1 => "name",
    2 => "mobile",
    3 => "typee"
];

// DataTables parameters
$orderColumnIndex = $_POST['order'][0]['column'] ?? 3;
$orderDir = $_POST['order'][0]['dir'] ?? 'desc';
$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$search_by_role = $_POST['search_by_role'] ?? 'all';

$orderColumn = $columns[$orderColumnIndex] ?? "username";
$orderDir = strtoupper($orderDir) === "ASC" ? "ASC" : "DESC";

// ================== BASE QUERY ==================
$baseQry = " FROM user WHERE 1=1 ";
$whereParts = [];
$bindTypes = "";
$bindValues = [];

// Search value filter
if (!empty($searchValue)) {
    $whereParts[] = "(username LIKE ? OR name LIKE ? OR mobile LIKE ? OR email LIKE ?)";
    $likeSearch = "%$searchValue%";
    $bindTypes .= "ssss";
    $bindValues[] = &$likeSearch;
    $bindValues[] = &$likeSearch;
    $bindValues[] = &$likeSearch;
    $bindValues[] = &$likeSearch;
}

// Role filter
if (!empty($search_by_role) && $search_by_role !== 'all') {
    $whereParts[] = "typee = ?";
    $bindTypes .= "s";
    $bindValues[] = &$search_by_role;
}

$whereSql = "";
if (!empty($whereParts)) {
    $whereSql = " AND " . implode(" AND ", $whereParts);
}

// ================== TOTAL RECORDS ==================
$totalRecordsQuery = $con->prepare("SELECT COUNT(*) AS allcount FROM user");
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
$dataSql = "SELECT id, username, password, name, mobile, email, status, typee,profile_picture,created_date  "
         . $baseQry . $whereSql
         . " ORDER BY $orderColumn $orderDir LIMIT ?, ?";

$stmt = $con->prepare($dataSql);

// Bind search + role + limit
$bindTypesData = $bindTypes . "ii";
$bindValuesData = array_merge($bindValues, [ &$start, &$length ]);

$params = array_merge([$bindTypesData], $bindValuesData);
call_user_func_array([$stmt, 'bind_param'], $params);

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($r = $result->fetch_assoc()) {
    $user_id = $r['id'];
    $last_login_date = $last_login_time= "NA";
    $last_login = "SELECT login_datetime FROM login_history where user_id = {$user_id} ORDER BY id DESC";
    $last_login_res = $con->query($last_login);
    if($last_login_res && $last_login_res->num_rows > 0){
        $last_login_row = $last_login_res->fetch_assoc();
        $last_login_date = $last_login_row['login_datetime'];
        $utcTime = new DateTime($last_login_date, new DateTimeZone('UTC'));
        $utcTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
        $last_login_time = $utcTime->format('Y-m-d H:i:s');
        
    }
    if ($last_login_date!="NA") {
        $last_login_time = timeAgo(date('Y-m-d H:i:s', strtotime($last_login_time)));
    }

    $user_image = "N/A";
    if($r['profile_picture']){
        $image_path = $define_company_website."uploads/staff/".$r['profile_picture'];
        $user_image = '<img src="'.$image_path.'" alt="Avatar" class="w-100" />';
    }
    
     $actions = '
    <div class="d-flex gap-2">

        <!-- VIEW BUTTON -->
        <a href="'.$site_path.'/view-users?id='.my_simple_crypt($user_id,'encrypt_1').'" 
           class="btn btn-light-primary btn-sm d-inline-flex align-items-center gap-2">

            <i class="fa fa-eye">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>

            <span>View</span>
        </a>

        <!-- DELETE BUTTON -->
        <a href="javascript:void(0);" 
           onclick="deleteUser('.$user_id.')" 
           class="btn btn-light-danger btn-sm d-inline-flex align-items-center gap-2">

            <i class="fa fa-trash">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>

            <span>Delete</span>
        </a>

    </div>';

    $data[] = [
        "select_all" => '<div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="'.$r['id'].'" />
                         </div>',
        "username" => '<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                            <a href="#">
                                <div class="symbol-label">
                                    '.$user_image.'
                                </div>
                            </a>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="'.$site_path.'/view-users?id='.my_simple_crypt($r['id'],'encrypt_1').'" class="text-gray-800 text-hover-primary mb-1">'.$r['name'].'</a>
                            <span>'.$r['email'].'</span>
                        </div>',
        "role" => $r['typee'],
        "last_login" => $last_login_time,
        "two_step" => 'NA',
        "join_date" => date('d M Y, h:i A',strtotime($r['created_date'])),
        "actions" =>$actions
    ];
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

<?php
include("../config/database.php"); // $con is mysqli connection

$columns = [
    0 => "id",
    1 => "update_date",
    2 => "name",
    3 => "mrpprice",
    4 => "reseller_user_price",
    5 => "product_nick_name",
    6 => "local_user_price"
];

error_reporting(E_ALL);
ini_set('display_errors',0);

// DataTables parameters
$orderColumnIndex = $_POST['order'][0]['column'] ?? 3;
$orderDir = $_POST['order'][0]['dir'] ?? 'desc';
$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$orderColumn = $columns[$orderColumnIndex] ?? "updated_date";
$orderDir = strtoupper($orderDir) === "ASC" ? "ASC" : "DESC";
$product_status = isset($_POST['product_status']) ? $_POST['product_status'] : "";
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : "";
$to_date= isset($_POST['to_date']) ? $_POST['to_date'] : "";

// ================== BASE QUERY ==================
$baseQry = " FROM product WHERE 1=1 ";
if($product_status!="" && $product_status !="all"){
    $baseQry .= " AND status = $product_status";
}

if($from_date && $to_date){
    $baseQry .= " AND update_date between '$from_date' AND '$to_date'";
}

$whereParts = [];
$bindTypes = "";
$bindValues = [];

// Search value filter
if (!empty($searchValue)) {
    $whereParts[] = "(name LIKE ? OR sku LIKE ? OR barcode like ? )";
    $likeSearch = "%$searchValue%";
    $bindTypes .= "sss";
    $bindValues[] = &$likeSearch;
    $bindValues[] = &$likeSearch;
    $bindValues[] = &$likeSearch;
}

$whereSql = "";
if (!empty($whereParts)) {
    $whereSql = " AND " . implode(" AND ", $whereParts);
}

// ================== TOTAL RECORDS ==================
$totalRecordsQuery = $con->prepare("SELECT COUNT(*) AS allcount FROM product");
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
$dataSql = "SELECT id, name,sku,img1,min_price,stockstatus,update_date,status,product_stock"
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
    $product_id = $r['id'];
    $stockStatusBadge = $r['stockstatus'] == 1 
        ? '<span class="badge badge-light-success">Instock</span>' 
        : '<span class="badge badge-light-danger">Outstock</span>';
    
    $statusBadge = $r['status'] == 1 
        ? '<span class="badge badge-light-success">Active</span>' 
        : '<span class="badge badge-light-danger">Inactive</span>';
        
    
    $image_url = $site_path."/images/user_images/no_image.svg";
    if($r['img1']){
        $image_url = $r['img1'];
    }
    
    $image_path  = "<img src='".$image_url."' 
     onerror='this.style.display='none'; this.nextElementSibling.style.display='inline-block';' loading='lazy' class='lazy-img'>";
    
 
    $product_name = htmlspecialchars($r['name']); // prevent XSS
    $short_name = strlen($product_name) > 25 ? substr($product_name, 0, 25) . '...' : $product_name;
    
    $actions = '
    <div class="d-flex gap-2">

        <!-- VIEW BUTTON -->
        <a href="'.$site_path.'/edit-product?id='.my_simple_crypt($product_id,'encrypt_1').'" 
           class="btn btn-light-primary btn-sm d-inline-flex align-items-center gap-2">

            <i class="fa fa-edit">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>

            <span>Edit</span>
        </a>
    </div>';
    $data[] = [
        
        "sr_no" => $srno,
        "product_name" => '<span class="truncate-text" title="'.$product_name.'">'.$short_name.'</span>',
        "image" => '<img data-src="'.$image_url.'" loading="lazy" class="lazy-img" width="50">',
        "sku" =>$r['sku'],
        "qty" => $r['product_stock'],
        "sell_price" => $r['min_price'],
        "status" => $statusBadge,
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

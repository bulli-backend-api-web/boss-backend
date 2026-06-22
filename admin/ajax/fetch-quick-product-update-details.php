<?php
include("../config/database.php"); // $con is mysqli connection

$columns = [
    0 => "update_date",
    1 => "name",
    2 => "status",
    3 => "sku",
    5 => "product_stock",
    6 => "weight",
    7 => "max_price",
    8 => "min_price"
];

// DataTables parameters
$orderColumnIndex = $_POST['order'][0]['column'] ?? 3;
$orderDir = $_POST['order'][0]['dir'] ?? 'desc';
$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$orderColumn = $columns[$orderColumnIndex] ?? "updated_date";
$orderDir = strtoupper($orderDir) === "ASC" ? "ASC" : "DESC";
$stock_status = isset($_POST['stock_status']) ? $_POST['stock_status'] : "";
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : "";
$to_date= isset($_POST['to_date']) ? $_POST['to_date'] : "";

// ================== BASE QUERY ==================
$baseQry = " FROM product WHERE 1=1 ";
if($stock_status !="-1"){
    $baseQry .= " AND stockstatus = $stock_status";
}

if($from_date && $to_date){
    $baseQry .= " AND update_date between '$from_date' AND '$to_date'";
}

$whereParts = [];
$bindTypes = "";
$bindValues = [];

// Search value filter
if (!empty($searchValue)) {
    $whereParts[] = "(name LIKE ? OR product_nick_name LIKE ? )";
    $likeSearch = "%$searchValue%";
    $bindTypes .= "ss";
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
$dataSql = "SELECT id,shopify_product_id,weight,name,status,min_price as sellprice,sku,product_stock,stockstatus,img1,timg1,barcode,max_price  "
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
    $statusBadge = $r['stockstatus'] == 1 
        ? '<span class="badge badge-light-success">Instock</span>' 
        : '<span class="badge badge-light-danger">Outstock</span>';
    if($r['img1']){
        $img_url  = "<img data-src='".$r['img1']."&width=100' onclick=\"openPopupCentered('".$r['img1']."')\"  width='50' height='50' loading='lazy' class='lazy-img'>";
    }else{
        $img_url  = "<img src='".$site_path."/images/default_image.jpeg' onclick='openPopupCentered(this.src)'  width='50' height='50'>";
    }
    
    $product_sku = '<input type="text" name="product_sku_'.$r['id'].'"  id="product_sku_'.$r['id'].'" data-id="'.$r['id'].'" data-shopify-product-id = "'.$r['shopify_product_id'].'"  class="form-control change_product_sku" value="'.$r['sku'].'" style="width:110px;">';
    
    $product_stock = '<input type="text" name="product_stock_'.$r['id'].'"  id="product_stock_'.$r['id'].'" data-id="'.$r['id'].'" data-shopify-product-id = "'.$r['shopify_product_id'].'"  class="form-control change_product_stock" value="'.$r['product_stock'].'" style="width:110px;" pattern="[0-9]{1,20}">';
    $mrp_price = '<input type="text" name="product_mrpprice_'.$r['id'].'"  id="product_mrpprice_'.$r['id'].'" data-shopify-product-id = "'.$r['shopify_product_id'].'" data-id="'.$r['id'].'"  class="form-control change_product_mrpprice" value="'.$r['max_price'].'" style="width:110px;" pattern="[0-9]{1,20}">';
    
    $weight = '<input type="text" name="product_weight_'.$r['id'].'"  id="product_weight_'.$r['id'].'" data-shopify-product-id = "'.$r['shopify_product_id'].'" data-id="'.$r['id'].'"  class="form-control change_product_weight" value="'.$r['weight'].'" style="width:110px;" pattern="[0-9]{1,20}">';
    $product_name = htmlspecialchars($r['name']); // prevent XSS
    $hidden_id = '<input type="hidden" class="product-id" value="'.$r['id'].'">';
    $short_name = strlen($product_name) > 15 ? substr($product_name, 0, 15) . '...' : $product_name;
    $ProductstatusBadge = $r['status'] == 1 
        ? '<span class="badge badge-light-success">Active</span>' 
        : '<span class="badge badge-light-danger">Inactive</span>';
    $data[] = [
        "image" => $img_url,
        "product_name" =>'<span class="truncate-text" title="'.$product_name.'">'.$short_name.'</span>' ,
        "status" =>$ProductstatusBadge ,
        "code" => $product_sku,
        "product_stock" => $product_stock,
        "weight" => $weight,
        "price" => $mrp_price,
        "stock_status" => $statusBadge,
        "actions" => '<a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions 
                        <i class="ki-outline ki-down fs-5 ms-1"></i></a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="'.$site_path.'/edit-product?id='.my_simple_crypt($r['id'],'encrypt_1').'"  class="menu-link px-3">Edit</a>
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

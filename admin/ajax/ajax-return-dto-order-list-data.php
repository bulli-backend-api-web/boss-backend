<?php
include("../config/database.php"); 
include("../config/auth_check.php");

$bank_list = [];// selectAllBankList();
$columns = [
    0 => "id",
    1 => "createdAt",
    2 => "createdAt",
    3 => "customer_name",
    4 => "mobile_number",
    11=> "rev_pickup_status"
];
$orderColumnIndex = $_POST['order'][0]['column'] ?? 1; // default column index
$orderDir = $_POST['order'][0]['dir'] ?? 'desc';       // default direction

$orderColumn = $columns[$orderColumnIndex] ?? "id";

$orderQuery = " ORDER BY $orderColumn $orderDir ";

// Read DataTables parameters
$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';

// Optional filters
$from_date = $_POST['from_date'] ?? '2025-01-01';
$to_date   = $_POST['to_date'] ?? date('Y-m-d');
$search_by_status = $_POST['search_by_status'] ?? 'all';
$dto_status = $_POST['dto_status'] ?? 'Pending';

$baseQry = " FROM dto_orders WHERE rev_pickup_status = '$dto_status'";

if($search_by_status == 'all'){
    $baseQry .= " AND  ( dto_type is null or dto_type ='Return')";
}else if($search_by_status == 0){
    $baseQry .= " AND is_refund IN (0,2) AND  ( dto_type is null or dto_type ='Return')";
}else if($search_by_status == 1){
    $baseQry .= " AND is_refund IN (1) AND  ( dto_type is null or dto_type ='Return')";
}

// ================== BASE QUERY ==================
if($from_date && $to_date){
    $baseQry .= " AND createdAt between '$from_date' AND '$to_date'";
}
// ================== SEARCH FILTER ==================
$filterQry = $baseQry;

if(!empty($searchValue)){
    $searchValueEscaped = mysqli_real_escape_string($con, $searchValue);
    $filterQry .= " AND (
        order_id LIKE '%$searchValueEscaped%' 
        OR customer_name LIKE '%$searchValueEscaped%' 
        OR courier LIKE '%$searchValueEscaped%' 
        OR tracking_number LIKE '%$searchValueEscaped%' 
        OR ticket_id LIKE '%$searchValueEscaped%'    
    )";
}

// ================== TOTAL RECORDS ==================
$totalRecordsQuery = mysqli_query($con,"SELECT COUNT(*) AS allcount ".$baseQry);
$totalRecords = mysqli_fetch_assoc($totalRecordsQuery)['allcount'];

// ================== FILTERED RECORDS ==================
$totalFilteredQuery = mysqli_query($con,"SELECT COUNT(*) AS allcount ".$filterQry);
$totalFiltered = mysqli_fetch_assoc($totalFilteredQuery)['allcount'];

// ================== FETCH DATA ==================
$orderQueryFinal  = "SELECT *
               ".$filterQry."
               ".$orderQuery." 
               LIMIT $start, $length";   
$orderRecords = mysqli_query($con, $orderQueryFinal);

$data = [];
while ($row = $orderRecords->fetch_assoc()) {
    $agent_name = "NA";
    if ($row['agent_id']) {
        $rRes = $con->query("SELECT name FROM user WHERE id = {$row['agent_id']}");
        if ($rRes && $rRes->num_rows > 0) {
            $agent_name = $rRes->fetch_assoc()['name'];
        }
    }
    
    $image_upload_by = "NA";
    if ($row['image_upload_by']) {
        $rRes = $con->query("SELECT name FROM user WHERE id = {$row['image_upload_by']}");
        if ($rRes && $rRes->num_rows > 0) {
            $image_upload_by = $rRes->fetch_assoc()['name'];
        }
    }

     $refund_status = 'PENDING';
     if($row['is_refund'] == 0){
          $refund_status = 'PENDING';
     }else if($row['is_refund'] == 1){
          $refund_status = 'SUCCESS';
     }else if($row['is_refund'] == 2){
          $refund_status = 'In Progress';
     }
     
    $proof = '';
    if ($row['refund_proof_image']) {
        $imagePath = file_exists("../../" . $row['refund_proof_image']) ? "../../" . $row['refund_proof_image'] : $row['refund_proof_image'];
        $proof = "<img data-src='$imagePath' style='width:50px;border-radius:8px;border:2px solid #ccc;' onclick='openPopupCentered(this.src)' class='lazy-img' loading='lazy' />";
        if($designation == 'admin'){
            $proof .= "<input style='margin-top:2px;' type='file' name='refund_proof_image' id='refund_proof_image' data-id='{$row['id']}' data-order-id = '{$row['order_id']}' />";
        }
    } else {
        if($designation == 'admin'){
        $proof = "<input type='file' name='refund_proof_image' id='refund_proof_image' data-id='{$row['id']}' data-order-id = '{$row['order_id']}' />";
        }else{
            $proof = '';
    }
    
    }
    
     $client_damage_proof = '';
     $damage_prrof = "<i class='fa fa-times-circle' style='color:#dc3545;font-size:22px;'></i>";
    if ($row['damage_proof']) {
        $images = explode(',', $row['damage_proof']);
        $damage_prrof = "<div style='display:flex; gap:5px; flex-wrap:wrap;'>";
        foreach ($images as $img) {
            $img = trim($img);
            $imagePath = file_exists("../../" . $img) ? "../../" . $img : $img;

            $damage_prrof .= "<i class='fa fa-check-circle' style='color:#28a745;font-size:22px;cursor:pointer;' onclick='openPopupCentered(\"$imagePath\")' title='View Image'>";
        }
        $damage_prrof .= "</div>";
    } else {
        $damage_prrof = "<input type='file' name='damage_proof_image' id='damage_proof_image' data-id='{$row['id']}' />";
    }
    
    if ($row['client_damage_image']) {
        $images = json_decode($row['client_damage_image']);
        $client_damage_proof = "<div style='display:flex; gap:5px; flex-wrap:wrap;'>";
        foreach ($images as $img) {
            $img = trim($img);
            $imagePath = $site_path."/".$img;

            $client_damage_proof .= "<img data-src='$imagePath'
                style='width:50px;height:50px;border-radius:8px;border:2px solid #ccc;cursor:pointer;'
                onclick='openPopupCentered(this.src)'  class='lazy-img' loading='lazy' />";
        }
        $client_damage_proof .= "</div>";
    }
    
    $damage_incorrect_proof = $courier_rev_image_exchange = $dispatch_saree_pics = 'NA';
    
    if($row['damage_incorrect_proof']){
        $imagePath = file_exists("../../" . $row['damage_incorrect_proof']) ? "../../" . $row['damage_incorrect_proof'] : $row['damage_incorrect_proof'];
        $damage_incorrect_proof = "<img data-src='$imagePath' style='width:50px;border-radius:8px;border:2px solid #ccc;' onclick='openPopupCentered(this.src)'  class='lazy-img' loading='lazy' />";
    }
    
    
    $dtwoWay = ($row['dto_way'] == 1) ? 'Self Courier' : 'BK Arranged Courier';
    
    $remarks = "<input type = 'text' name='remarks' id='remarks' value='{$row['remarks']}' class = 'form-control remarksupdate' data-id='{$row['id']}'/>";
    $whatsapp_verify = ($row['is_mobile_verify'] == 1) ? '<label class="label label-success">Verified</label>' : '<label class="label label-danger">Not verified</label>';
    if($designation == 'admin'){
    $bankOptions = "<select class='form-control bank_change' data-id='{$row['id']}'>
                    <option value=''>Select Bank</option>";
foreach($bank_list as $bank){
    $selected = ($bank['id'] == $row['bank_id']) ? "selected" : "";
    $bankOptions .= "<option value='{$bank['id']}' $selected>{$bank['name']}</option>";
}
        $bankOptions .= "</select>";
    }else{
        $bankOptions = '';
    }

    $refund_status = '';
    if($designation == 'admin'){
        $refund_status = "<select class='form-control refuncstatuschange' data-id='{$row['id']}' data-customer-mobile='".$row['mobile_number']."' data-customer-name='".$row['customer_name']."' data-order-id = '{$row['order_id']}'>
                <option value='0'" . ($row['is_refund'] == '0' ? ' selected' : '') . ">Pending</option>
                <option value='2'" . ($row['is_refund'] == '2' ? ' selected' : '') . ">In Progress</option>
                <option value='1'" . ($row['is_refund'] == '1' ? ' selected' : '') . ">Success</option>
             </select>";
    }
    
    $now = new DateTime("now", new DateTimeZone("Asia/Kolkata"));
    $created = new DateTime($row['createdAt'], new DateTimeZone("UTC"));
    $created->setTimezone(new DateTimeZone("Asia/Kolkata"));
    if (!empty($row['rev_pickup_status_date'])) {
        if ($row['id'] >= 1784) {
            $updated = new DateTime($row['rev_pickup_status_date'], new DateTimeZone('Asia/Kolkata'));
        } else {
            $updated = new DateTime($row['rev_pickup_status_date'], new DateTimeZone('UTC'));
            $updated->setTimezone(new DateTimeZone('Asia/Kolkata'));
        }
    } else {
        $updated = new DateTime('now', new DateTimeZone('Asia/Kolkata')); // safe fallback
    }

    
    
    $courier_recv_image = $row['courier_received_image'];
    $data[] = [
        'order_from' => $row['order_from'],
        'dto_way' => $dtwoWay,
        'date' => date('d-m-Y',strtotime($row['createdAt'])),
        'ticket_id' => $row['ticket_id'],
        'order_id' => $row['order_id'],
        'customer_name' => $row['customer_name'],
        'mobile_number' => $row['mobile_number'],
        'agent_name' => $agent_name,
        'qty' => $row['qty'],
        'courier_name' => $row['courier'],
        'tracking_number' => $row['tracking_number'],
        'rev_pickup_status' => "<div class='d-flex align-items-center gap-2'><span class='status-light'></span><select class='form-control revpickuchange' data-id='{$row['id']}'>
                <option value='Pending'" . ($row['rev_pickup_status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                <option value='InTransist'" . ($row['rev_pickup_status'] == 'InTransist' ? ' selected' : '') . ">InTransist</option>
                <option value='Delivered'" . ($row['rev_pickup_status'] == 'Delivered' ? ' selected' : '') . ">Delivered</option>
             </select></div>",
        'reason' => $row['reason'],
        'damage_proof' => $damage_prrof,
        'client_damage_proof' => $client_damage_proof,
        'courier_rev_image' => !empty($row['courier_received_image']) ? "<i class='fa fa-check-circle' style='color:#28a745;font-size:22px;cursor:pointer;' onclick='openPopupCentered(\"$courier_recv_image\")' title='View Image'>" : "",
        'price' => $row['amount'],
        'deduction' => $row['deduction'],
        'deduction1' => $row['deduction1'],
        'deduction2' => $row['deduction2'],
        'final_price' => $row['final_amount'],
        'gpay_number' => $row['gpay_number']." ".$row['gpay_name'],
        'gpay_number_verify' => $whatsapp_verify,
        'refund_status' => $refund_status,
        'refund_initiate_date' => $row['refund_proof_image_upload_date'],
        'bank' => $bankOptions,
        'refund_initiate_photo' => $proof,
        'image_upload_date' => !empty($row['image_uploaded_date']) ? date('d-m-Y H:i:s',strtotime($row['image_uploaded_date'])) : "NA",
        'update_by' => $image_upload_by,
        'app_remaks' => $row['app_remarks'],
        'remark' => $remarks,
        'action' => '
            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" 
               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
               Actions 
               <i class="ki-outline ki-down fs-5 ms-1"></i>
            </a>

            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 
                 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                 <div class="menu-item px-3">
                        <a href="'.$site_path.'/edit-dto?id='.my_simple_crypt($row['id'],'encrypt_1').'" 
                           class="menu-link px-3">Edit</a>
                    </div>
                    <div class="menu-item px-3">
                        <a href="#" data-action="'.$site_path.'/ajax/delete-master-data" data-id="'.$row['id'].'" class="menu-link px-3 delete_return_dto_order" >Delete</a>
                    </div>
                </div>'

    ];
}

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


<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : 'add-design-details';

if($action == 'add-design-details'){
    
    $design_name = $_POST['design_name'];
    $design_code = $_POST['design_code'];
    $occasion = $_POST['occasion'];
    $style = $_POST['style'];
    $color = $_POST['color'];
    $reference = $_POST['reference'];
    $budget = $_POST['budget'];
    $minimum_sketch = $_POST['minimum_sketch'];
    $assign_to = $_POST['assign_to'];
    $due_date = $_POST['due_date'];
    $created_by = $uid;
    $reference_link = isset($_POST['reference_link']) ? $_POST['reference_link'] : "";
    $brand_name = isset($_POST['brand_name']) ? $_POST['brand_name'] : "";
    $embrodary = isset($_POST['embrodary']) ? $_POST['embrodary'] : "";
    $model = isset($_POST['model']) ? $_POST['model'] : "";
    $upload_images = [];
    if (!empty($_FILES['reference']['name'][0])) {
        foreach ($_FILES['reference']['name'] as $key => $file_name) {
            $tmp_name = $_FILES['reference']['tmp_name'][$key];
            $error    = $_FILES['reference']['error'][$key];
            if ($error == 0) {
                $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $new_name = time().'_'.$key.'.'.$extension;
                move_uploaded_file(
                    $tmp_name,
                    '../../uploads/reference/'.$new_name
                );

                $upload_images[] = $new_name;
            }
        }
    }
    if($upload_images){
        $reference_image = implode(",",$upload_images);
    }else{
        $reference_image = NULL;
    }
    
    $insert_query = "INSERT INTO design(design_name,design_code,budget,occasion,color,style,min_sketch,assign_to,due_date,created_by,reference_image,reference_link,brand_name,model_id,ambrodary_id) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param("ssisssiisisssii", $design_name,$design_code,$budget,$occasion,$color,$style,$minimum_sketch,$assign_to,$due_date,$created_by,$reference_image,$reference_link,$brand_name,$model,$embrodary);
    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Design Created Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "something went wrong while create design"]);
    }
}else if($action == 'update-design-details'){
    $redirect_page = $_POST['redirect_page'];
    $hidden_id = $_POST['hidden_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];
    $approved_by = $uid;
    $approve_date = date('Y-m-d H:i:s');
    if($status == 1){
        $status_remark = "Approved";
    }else if($status == 2){
        $status_remark = "Rejected";
    }else if($status == 3){
        $status_remark = "Modification Needed";
    }else{
        $status_remark = "Updated";
    }
    $design_code = $_POST['design_code'];
    $design_name = $_POST['design_name'];
    $style = $_POST['style'];
    $assign_to = $_POST['assign_to'];
    $budget = $_POST['budget'];
    $target_days = 3;
    
    $reference_link = isset($_POST['reference_link']) ? $_POST['reference_link'] : "";
    $brand_name = isset($_POST['brand_name']) ? $_POST['brand_name'] : "";
    $embrodary = isset($_POST['embrodary']) ? $_POST['embrodary'] : "";
    $model = isset($_POST['model']) ? $_POST['model'] : "";
    
    $update_query = "UPDATE design SET status = ?, remarks = ?,approved_by = ?, approved_date = ?,reference_link=?,brand_name=?,model_id=?,ambrodary_id=? where id = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("isisssiii", $status,$remarks,$approved_by,$approve_date,$reference_link,$brand_name,$model,$embrodary,$hidden_id);
    if($stmt->execute()){
        if($status == 1){
            $sample_no = generate_sample_no();
            $sample_insert_query = "INSERT INTO sampling(sample_code,design_id,design_code,name,category,assign_to,assign_by,budget,target_days) values (?,?,?,?,?,?,?,?,?)";
            $stmt1 = $con->prepare($sample_insert_query);
            $stmt1->bind_param("sissiiidi",$sample_no,$hidden_id,$design_code,$design_name,$style,$assign_to,$uid,$budget,$target_days);
            $stmt1->execute();
        }
        echo json_encode(["status" => "success", "message" => "Design $status_remark Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "something went wrong while create design"]);
    }
    
}else{
    echo json_encode(["status" => "error", "message" => "invalid request"]);
}
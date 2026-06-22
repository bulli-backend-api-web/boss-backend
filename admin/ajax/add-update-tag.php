<?php
include("../config/database.php");
include("../config/auth_check.php");

$action = !empty($_POST['action']) ? $_POST['action'] : '';
if($action == 'add-style'){
    $style_name = $_POST['style_name'];
    $checkSql = "SELECT COUNT(*) as cnt FROM category WHERE name = ?";
    $stmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($stmt, "s", $style_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "Name already exists in system"]);
        exit;
    }
    
    $insert_query = "INSERT INTO category(name) VALUES (?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('s',$style_name);
    if($stmt->execute()){
        $last_insert_id = $con->insert_id;
        echo json_encode(["status" => "success", "message" => "Category Added Successfully.",'id'=>$last_insert_id,'tag_name'=>$style_name]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while added data"]);
    }
}else if($action == 'update-style'){
    $style_name = $_POST['style_name'];
    $tag_id = $_POST['id'];
    
    $update_Sql = "UPDATE category SET name = ? where id = ?";
    $stmt1 = $con->prepare($update_Sql);
    $stmt1->bind_param('si',$style_name,$tag_id);
    
    if($stmt1->execute()){
        echo json_encode(["status" => "success", "message" => "Tag Updated Successfully."]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error while updated channel"]);
    }
}else{
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}

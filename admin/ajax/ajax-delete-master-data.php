<?php
include("../config/database.php"); // DB connection
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$action = $_POST['action'];
if ($action == 'delete_inwards') {
    $batch_id = isset($_POST['batch_id']) ? (int) $_POST['batch_id'] : 0;

    if ($batch_id <= 0) {
        $response['message'] = "Invalid challan";
        echo json_encode($response);
        exit;
    }

    /*
      |--------------------------------------------------------------------------
      | Get challan
      |--------------------------------------------------------------------------
     */
    $stmt = $con->prepare("
    SELECT *
    FROM stock_inward_batch
    WHERE id = ?
    AND is_deleted = 0
    LIMIT 1
");
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $batch = $result->fetch_assoc();
    $stmt->close();

    if (!$batch) {
        $response['message'] = "Challan not found";
        echo json_encode($response);
        exit;
    }

    /*
      |--------------------------------------------------------------------------
      | Do not delete if scanning started
      |--------------------------------------------------------------------------
     */
    if ((int) $batch['scanned_qty'] > 0 || $batch['challan_status'] == 'SCANNING' || $batch['challan_status'] == 'COMPLETED') {
        $response['message'] = "Cannot delete. Stock scanning already started.";
        echo json_encode($response);
        exit;
    }

    mysqli_begin_transaction($con);

    try {

        /*
          |--------------------------------------------------------------------------
          | Soft delete challan
          |--------------------------------------------------------------------------
         */
        $stmt = $con->prepare("
        UPDATE stock_inward_batch
        SET 
            is_deleted = 1,
            challan_status = 'CANCELLED',
            status = 'CANCELLED'
        WHERE id = ?
    ");
        $stmt->bind_param("i", $batch_id);
        $stmt->execute();
        $stmt->close();

        /*
          |--------------------------------------------------------------------------
          | Mark labels cancelled/pending rows inactive
          | If you do not have is_deleted in stock_inward_qr, skip this block.
          |--------------------------------------------------------------------------
         */
        $checkCol = $con->query("SHOW COLUMNS FROM stock_inward_qr LIKE 'is_deleted'");
        if ($checkCol && $checkCol->num_rows > 0) {

            $stmt = $con->prepare("
            UPDATE stock_inward_qr
            SET is_deleted = 1
            WHERE batch_id = ?
        ");
            $stmt->bind_param("i", $batch_id);
            $stmt->execute();
            $stmt->close();
        }

        mysqli_commit($con);

        $response['status'] = true;
        $response['message'] = "Challan deleted successfully";

        echo json_encode($response);
        exit;
    } catch (Exception $e) {

        mysqli_rollback($con);

        $response['message'] = "Delete failed: " . $e->getMessage();
        echo json_encode($response);
        exit;
    }
} else if ($action == 'delete_dept') {
    $dept_id = $_POST['dept_id'];
    $stmt = $con->prepare('DELETE from departments where id = ?');
    $stmt->bind_param('i', $dept_id);
    $stmt->execute();
    $response['status'] = "success";
    $response['message'] = "Department deleted successfully";

    echo json_encode($response);
    exit;
} else if ($action == 'delete_tag') {
    $tag_id = $_POST['tag_id'];
    $stmt = $con->prepare('DELETE from category where id = ?');
    $stmt->bind_param('i', $tag_id);
    $stmt->execute();
    $response['status'] = "success";
    $response['message'] = "Tag deleted successfully";

    echo json_encode($response);
    exit;
} else if ($action == 'delete_fabric_type') {
    $ftype_id = $_POST['ftype_id'];
    $stmt = $con->prepare('DELETE from fabric_type where id = ?');
    $stmt->bind_param('i', $ftype_id);
    $stmt->execute();
    $response['status'] = "success";
    $response['message'] = "Fabric Type deleted successfully";

    echo json_encode($response);
    exit;
} else if ($action == 'delete_user') {
    $user_id = $_POST['user_id'];
    $stmt = $con->prepare('DELETE from user where id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $response['status'] = "success";
    $response['message'] = "User deleted successfully";

    echo json_encode($response);
    exit;
}else if($action == 'delete_jobwork'){
    $job_id = $_POST['job_id'];
    $stmt = $con->prepare('DELETE from jobwork_type where id = ?');
    $stmt->bind_param('i', $job_id);
    $stmt->execute();
    $response['status'] = "success";
    $response['message'] = "JObwork Type deleted successfully";

    echo json_encode($response);
    exit;
}else if($action == 'delete_staff'){
    $staff_id = $_POST['staff_id'];
    $stmt = $con->prepare('DELETE from staff_register where id = ?');
    $stmt->bind_param('i', $staff_id);
    $stmt->execute();
    $response['status'] = "success";
    $response['message'] = "Staff deleted successfully";

    echo json_encode($response);
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}


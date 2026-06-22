<?php
include("../config/database.php");
$action = isset($_POST['action']) ? $_POST['action'] : "";
$role_id = isset($_POST['role_id']) ? $_POST['role_id'] : "";

if ($action == 'update_role' && $role_id) {
    $user_role = isset($_POST['user_role']) ? implode(",",$_POST['user_role']) : "";
    $typee_id = isset($_POST['typee_id']) ? $_POST['typee_id'] : "";
    $sql = "UPDATE user SET typee = ?,typee_id=? WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $user_role,$typee_id, $role_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Role updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update role"]);
    }
} else if ($action == 'update_email') {
    $profile_email = $_POST['profile_email'];
    $sql = "UPDATE user SET email = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $profile_email, $role_id);

    if (mysqli_stmt_execute($stmt)) {
        $details = "Email Address change to {$profile_email}";
        logActivity($role_id, "Email Address Change", $details);
        echo json_encode(["status" => "success", "message" => "Email updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update role"]);
    }
} else if ($action == 'update_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $role_id = $_POST['update_password_role_id'];
    $checkPassword = "SELECT id FROM user WHERE id = ? AND TRIM(password) = ?";
    $stmt = mysqli_prepare($con, $checkPassword);
    mysqli_stmt_bind_param($stmt, "is", $role_id, $current_password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) === 0) {
        echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
    } else {
        $updatePassword = "UPDATE user SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $updatePassword);
        mysqli_stmt_bind_param($stmt, "si", $new_password, $role_id);

        if (mysqli_stmt_execute($stmt)) {
            logActivity($role_id, "Password Change", "Update New Passoword");
            echo json_encode([
                "status" => "success",
                "message" => "Password updated successfully."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update password."
            ]);
        }
    }
} else if ($action == 'update_user_details') {
    
    $update_user_role_id = $_POST['update_user_role_id'];
    $name = $_POST['name'];
    $mobile_number = $_POST['mobile_number'];
    $scan_app_modules = isset($_POST['scan_app_moduel']) ? $_POST['scan_app_moduel'] : [];
    $status = $_POST['status'];
    $department_id = $_POST['department_id'];
    $brand_name = !empty($_POST['brand_name']) ? $_POST['brand_name'] : [];
    $user_profile_picture = $_POST['profile_picture_hidden'];
    if (!empty($_FILES['avatar']['name'])) {
        $filePath = $_FILES['avatar']['tmp_name'];
        $fileName = $update_user_role_id."_".basename($_FILES['avatar']['name']);
        $folder = '../images/user_images/'.$fileName;
        move_uploaded_file($filePath, $folder);
        $user_profile_picture = $fileName;
    }
    $sql = "UPDATE user SET name = ?,mobile = ?,profile_picture = ?,status=?, department_id = ?,company_id = ?  WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssiiss", $name, $mobile_number, $user_profile_picture, $status,$department_id,implode(",",$brand_name),$update_user_role_id);

    if (mysqli_stmt_execute($stmt)) {
        if (!empty($scan_app_modules)) {
            $deleteSql = "DELETE FROM app_assign_modules WHERE user_id = ?";
            $deleteStmt = mysqli_prepare($con, $deleteSql);
            mysqli_stmt_bind_param($deleteStmt, "i", $update_user_role_id);
            mysqli_stmt_execute($deleteStmt);
            foreach ($scan_app_modules as $val) {
                $sql = "INSERT INTO app_assign_modules (user_id, module_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $update_user_role_id, $val);
                mysqli_stmt_execute($stmt);
            }
        }
        echo json_encode(["status" => "success", "message" => "User Details updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update role"]);
    }
} else if ($action == 'delete_user') {
    $customer_id = $_POST['customer_id'];
    $sql = "DELETE FROM user  WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "User Deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update role"]);
    }
} else if ($action == 'multiple_delete_user') {
    $ids = $_POST['ids'];
    if ($ids) {
        $user_id_string = implode(",", $ids);
        $sql = "DELETE FROM user  WHERE id in ($user_id_string)";
        $stmt = mysqli_prepare($con, $sql);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "success", "message" => "Selected User Deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update role"]);
        }
    } else {
        echo json_encode(["status" => "success", "message" => "Plase select atleast one user for delete"]);
    }
} else if ($action == 'add_new_user') {
    $role_id = $_POST['role_id'];
    $user_name = $_POST['user_name'];
    $user_password = $_POST['user_password'];
    $fullname = $_POST['fullname'];
    $user_email = $_POST['user_email'];
    $user_mobile = $_POST['user_mobile'];
    $department_id = $_POST['department_id'];
    $brand_name = isset($_POST['brand_name']) ? $_POST['brand_name'] : "";
    $face_attendance = $_POST['face_attendance'];
    $profile_picture = NULL;
    if (!empty($_FILES['profile_picture']['profile_picture'])) {
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        $error    = $_FILES['profile_picture']['error'];
        $file_name    = $_FILES['profile_picture']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $profile_picture = "staff_profiel_".time().'.'.$extension;
            move_uploaded_file($tmp_name,'../../uploads/staff/'.$new_name);
        }
    }
    $status = 1;
    $scan_app_modules = isset($_POST['scan_app_moduel']) ? $_POST['scan_app_moduel'] : [];
    
    $admin_sql = "SELECT id from role where slug = ?";
    $stmt = mysqli_prepare($con, $admin_sql);
    mysqli_stmt_bind_param($stmt, "s", $user_role);
    mysqli_stmt_execute($stmt);
    $adminResult = mysqli_stmt_get_result($stmt);
    $typee_id = 0;
    if ($adminResult) {
        $row = mysqli_fetch_assoc($adminResult);
        $typee_id = $row['id'];
    }
    
    $brand_name = !empty($_POST['brand_name']) ? $_POST['brand_name'] : [];
 
    $checkUser = "SELECT id FROM user WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($con, $checkUser);
    mysqli_stmt_bind_param($stmt, "ss", $user_name, $user_email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo json_encode(["status" => "error", "message" => "Username / Email is already taken."]);
    } else {
        $sql = "INSERT INTO user(typee,username ,password,name,mobile,email,profile_picture,status,typee_id,department_id,company_id,face_attendance) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssiissi", $user_role, $user_name, $user_password, $fullname, $user_mobile, $user_email, $profile_picture, $status, $typee_id,$department_id,$brand_name,$face_attendance);
        if (mysqli_stmt_execute($stmt)) {
            $last_id = $con->insert_id;
            if (!empty($scan_app_modules)) {
                foreach ($scan_app_modules as $val) {
                    $sql = "INSERT INTO app_assign_modules (user_id, module_id) VALUES (?, ?)";
                    $stmt = mysqli_prepare($con, $sql);
                    mysqli_stmt_bind_param($stmt, "ii", $last_id, $val);
                    mysqli_stmt_execute($stmt);
                }
            }
            echo json_encode(["status" => "success", "message" => "User Added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update role"]);
        }
    }
} else if ($action == 'clear_udid') {
    $customer_id = $_POST['customer_id'];
    $udid = NULL;
    $sql = "UPDATE user SET udid = ?  WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $udid, $customer_id);

    if (mysqli_stmt_execute($stmt)) {
        logActivity($role_id, "Clear UDID", "Clear user UDID");
        echo json_encode(["status" => "success", "message" => "UDID Clear Sucessfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to clear udid"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
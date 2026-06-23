<?php

header("Content-Type: application/json");
include("../config/database.php"); // DB connection

$role_name = trim($_POST['role_name'] ?? '');
$action = $_POST['action'];
$role_id = $_POST['role_id'] ?? null; // required for update

if ($action != 'delete_role')
    if ($role_name == '') {
        echo json_encode(["status" => "error", "message" => "Role name is required"]);
        exit;
    }

// 🔹 Generate slug
function makeSlug($string) {
    // Convert to lowercase
    $slug = strtolower($string);
    // Replace spaces & underscores with hyphen
    $slug = preg_replace('/[\s_]+/', '-', $slug);
    // Remove special characters
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    // Remove multiple hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    // Trim hyphens from ends
    return trim($slug, '-');
}

$slug = makeSlug($role_name);

if ($action == 'add_role') {
    $permissions = $_POST['permissions'] ?? [];
    $checkSql = "SELECT COUNT(*) as cnt FROM role WHERE role_name = ?";
    $stmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($stmt, "s", $role_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "Role already exists in user table"]);
        exit;
    }
    $sql = "INSERT INTO role (role_name, slug) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $role_name, $slug);
    $last_id = mysqli_insert_id($con);
    if (mysqli_stmt_execute($stmt)) {
        $deleteSql = "DELETE FROM role_modules WHERE role_id = ?";
        $deleteStmt = mysqli_prepare($con, $deleteSql);
        mysqli_stmt_bind_param($deleteStmt, "i", $last_id);
        mysqli_stmt_execute($deleteStmt);

        foreach ($permissions as $perm_id => $val) {
            $sql = "INSERT INTO role_modules (role_id, module_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $last_id, $perm_id);
            mysqli_stmt_execute($stmt);
        }

        echo json_encode([
            "status" => "success",
            "message" => "Role added successfully",
            "slug" => $slug,
            "id" => $last_id
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database insert failed"]);
    }
} else if ($action == 'update_role' && $role_id) {
    $permissions = $_POST['permissions'] ?? [];
    $checkSql = "SELECT COUNT(*) as cnt FROM role WHERE role_name = ? and id != ?";
    $stmt = mysqli_prepare($con, $checkSql);
    mysqli_stmt_bind_param($stmt, "si", $role_name, $role_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['cnt'] > 0) {
        echo json_encode(["status" => "error", "message" => "Role already exists in user table"]);
        exit;
    }

    $sql = "UPDATE role SET role_name = ?, slug = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $role_name, $slug, $role_id);

    if (mysqli_stmt_execute($stmt)) {
        if (!empty($permissions)) {
            $deleteSql = "DELETE FROM role_modules WHERE role_id = ?";
            $deleteStmt = mysqli_prepare($con, $deleteSql);
            mysqli_stmt_bind_param($deleteStmt, "i", $role_id);
            mysqli_stmt_execute($deleteStmt);
            foreach ($permissions as $perm_id => $val) {
                $sql = "INSERT INTO role_modules (role_id, module_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $role_id, $perm_id);
                mysqli_stmt_execute($stmt);
            }
        }
        echo json_encode(["status" => "success", "message" => "Role updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update role"]);
    }
} else if ($action == 'delete_role') {
    $sql = "DELETE FROM role WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $role_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Role deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete role"]);
    }
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}


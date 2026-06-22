<?php

header("Content-Type: application/json");
include("config/database.php");

$permission_name = trim($_POST['permission_name'] ?? '');
$permission_category = isset($_POST['permission_category']) ? $_POST['permission_category'] : "";
$action = $_POST['action'];

function makeSlug($string) {
    $slug = strtolower($string);
    $slug = preg_replace('/[\s_]+/', '-', $slug);
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function createModuleFileFull($moduleName, $path = __DIR__ . '/', $site_path = '') {
    // Generate slug from module name
    $slug = strtolower(trim($moduleName));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    // Ensure directory exists
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $filePath = $path . $slug . '.php';

    if (file_exists($filePath)) {
        return false; // file already exists
    }

    // Starter code
    $starterCode = "<?php\n";
    $starterCode .= "// Auto-generated module: $slug\n\n";
    $starterCode .= "include(\"config/database.php\");\n";
    $starterCode .= "include(\"config/auth_check.php\");\n";
    $starterCode .= "include(\"includes/sidemenu.php\");\n";
    $starterCode .= "?>\n\n";

    $starterCode .= "<div class=\"app-main flex-column flex-row-fluid\" id=\"kt_app_main\">\n";
    $starterCode .= "    <div class=\"d-flex flex-column flex-column-fluid\">\n";
    $starterCode .= "        <div id=\"kt_app_toolbar\" class=\"app-toolbar pt-7 pt-lg-10\">\n";
    $starterCode .= "            <div id=\"kt_app_toolbar_container\" class=\"app-container container-fluid d-flex align-items-stretch\">\n";
    $starterCode .= "                <div class=\"app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100\">\n";
    $starterCode .= "                    <div class=\"page-title d-flex flex-column justify-content-center gap-1 me-3\">\n";
    $starterCode .= "                        <h1 class=\"page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0\">$moduleName</h1>\n";
    $starterCode .= "                    </div>\n";
    $starterCode .= "                </div>\n";
    $starterCode .= "            </div>\n";
    $starterCode .= "        </div>\n\n";

    $starterCode .= "        <div id=\"kt_app_content\" class=\"app-content\">\n";
    $starterCode .= "            <div id=\"kt_app_content_container\" class=\"app-container container-fluid\">\n";
    $starterCode .= "                <!-- Your module content goes here -->\n";
    $starterCode .= "            </div>\n";
    $starterCode .= "        </div>\n";
    $starterCode .= "    </div>\n\n";

    $starterCode .= "    <div id=\"kt_app_footer\" class=\"app-footer\">\n";
    $starterCode .= "        <div class=\"app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3\">\n";
    $starterCode .= "            <div class=\"text-gray-900 order-2 order-md-1\">\n";
    $starterCode .= "                <span class=\"text-muted fw-semibold me-1\"><?php echo date('Y');?>&copy;</span>\n";
    $starterCode .= "                <a href=\"https://vastranand.in\" target=\"_blank\" class=\"text-gray-800 text-hover-primary\">vastranand. All Rights Reserved. Powered by Vastranand Pvt Ltd.</a>\n";
    $starterCode .= "            </div>\n";
    $starterCode .= "        </div>\n";
    $starterCode .= "    </div>\n";
    $starterCode .= "</div>\n\n";

    // Scripts
    $starterCode .= "<script>var hostUrl = \"$site_path/assets/\";</script>\n";
    $starterCode .= "<script src=\"$site_path/assets/plugins/global/plugins.bundle.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/scripts.bundle.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/plugins/custom/datatables/datatables.bundle.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/custom/apps/customers/list/export.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/custom/apps/customers/list/list.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/widgets.bundle.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/custom/widgets.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/custom/apps/chat/chat.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/custom/utilities/modals/upgrade-plan.js\"></script>\n";
    $starterCode .= "<script src=\"$site_path/assets/js/custom/utilities/modals/users-search.js\"></script>\n";

    file_put_contents($filePath, $starterCode);

    return $filePath;
}

/* Add Permission */
if ($action == 'add_permission') {
    if ($permission_name == '') {
        echo json_encode(["status" => "error", "message" => "Permission name is required"]);
        exit;
    }

    $slug = makeSlug($permission_name);
    $sql = "INSERT INTO modules (category_id,module_name, slug) VALUES (?,?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $permission_category, $permission_name, $slug);

    if (mysqli_stmt_execute($stmt)) {
        $fileCreated = createModuleFileFull($permission_name, __DIR__ . '/', $site_path);

        echo json_encode([
            "status" => "success",
            "message" => "Permission added successfully",
            "slug" => $slug
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database insert failed"]);
    }
}

/* Delete Permission */ else if ($action == 'delete_permission') {
    $permission_id = $_POST['id'];
    $sql = "DELETE FROM modules  WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $permission_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Permission Deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update role"]);
    }
}

/* Update Permission */ else if ($action == 'update_permission') {
    $permission_id = $_POST['permission_id'];
    $permission_name = $_POST['permission_name'];
    $slug = makeSlug($permission_name);

    $sql = "UPDATE modules SET module_name = ?,slug = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $permission_name, $slug, $permission_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Permission updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update role"]);
    }
} else if ($action == 'add_category') {
    $category_name = $_POST['category_name'];
    $sql = "INSERT INTO module_category (category_name) VALUES (?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $category_name);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            "status" => "success",
            "message" => "Category added successfully"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database insert failed"]);
    }
}



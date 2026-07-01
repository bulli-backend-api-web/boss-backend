<?php

include '../config/database.php';
include '../config/auth_check.php';
$action = !empty($_POST['action']) ? $_POST['action'] : "add-ref-library";
if ($action == 'add-ref-library') {
    $name = $_POST['name'] ?? '';
    $ref_code = $_POST['ref_code'] ?? '';
    $reference_type = $_POST['reference_type'] ?? '';
    $garment = $_POST['garment'] ?? '';
    $work_type = $_POST['work_type'] ?? '';
    $occasion = $_POST['occasion'] ?? '';
    $collections = $_POST['collections'] ?? '';
    $primary_colour = $_POST['primary_colour'] ?? '';
    $secondary_colours = $_POST['secondary_colours'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $status = $_POST['status'] ?? '';
    if(!empty($_FILES['ref_image']['name'])){
        $tmp_name = $_FILES['ref_image']['tmp_name'];
        $error = $_FILES['ref_image']['error'];
        $file_name = $_FILES['ref_image']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $ref_image = "ref_image_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/reference_library/' . $ref_image);
        }
    }else{
        $ref_image = '';
    }

    $stmt = $con->prepare("
    INSERT INTO reference_library
    (
        code,
        name,
        photo,
        reference_type,
        garment,
        work_type,
        ocassion,
        collection_id,
        primary_color,
        secondary_color,
        notes,
        tags,
        created_by
    )
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
");

    $stmt->bind_param(
            "sssiiissssssi",
            $ref_code,
            $name,
            $ref_image,
            $reference_type,
            $garment,
            $work_type,
            $occasion,
            $collections,
            $primary_colour,
            $secondary_colours,
            $notes,
            $tags,
            $uid
    );
    if ($stmt->execute()) {
        echo json_encode(['success' => '1', 'message' => "Reference Library Added successfully"]);
    } else {
        
    }
} else if ($action = 'add-collection') {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $year = $_POST['year'] ?? null;
    $month = $_POST['month'] ?? null;
    $target_refs = $_POST['target_refs'] ?? null;
    $occasion = $_POST['occasion'] ?? null;
    $colour = $_POST['colour'] ?? null;
    $brief = trim($_POST['brief'] ?? '');

// Server-side validation (never trust client-side alone)
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required.']);
        exit;
    }

    try {
        $stmt = $con->prepare("
        INSERT INTO collections
        (name, code, year, month, target_refs, occasion, colour, brief,created_by, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?, NOW())
    ");
        $stmt->execute([
            $name,
            $code,
            $year ?: null,
            $month,
            $target_refs ?: null,
            $occasion,
            $colour,
            $brief ?: null,
            $uid
        ]);

        echo json_encode(['success' => true, 'collection_id' => $con->insert_id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}


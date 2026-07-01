<?php
include("../config/database.php");
include("../config/auth_check.php");
header('Content-Type: application/json');

$where = [];
$params = [];
$types = '';

if (!empty($_GET['year'])) {
     $where[] = "YEAR(created_at) = ?";
    $params[] = $_GET['year'];
    $types .= 's';
}
if (!empty($_GET['garment'])) {
    $where[] = "garment = ?";
    $params[] = $_GET['garment'];
    $types .= 's';
}
if (!empty($_GET['work'])) {
    $where[] = "work_type = ?";
    $params[] = $_GET['work'];
    $types .= 's';
}
if (!empty($_GET['occasion'])) {
    $where[] = "ocassion = ?";
    $params[] = $_GET['occasion'];
    $types .= 's';
}
if (!empty($_GET['style'])) {
    $where[] = "style = ?";
    $params[] = $_GET['style'];
    $types .= 's';
}
if (!empty($_GET['source'])) {
    $where[] = "source = ?";
    $params[] = $_GET['source'];
    $types .= 's';
}
if (!empty($_GET['reference_type'])) {
    $where[] = "reference_type = ?";
    $params[] = $_GET['reference_type'];
    $types .= 'i';
}

$sql = "SELECT * FROM reference_library";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sort = $_GET['sort'] ?? 'newest';
$sql .= $sort === 'oldest' ? " ORDER BY created_at ASC" : " ORDER BY created_at DESC";

try {
    $stmt = $con->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    $refs = [];
    foreach ($rows as $row) {
        $refs[] = [
            'code'   => $row['code'],
            'title'  => $row['name'],
            'image'  => !empty($row['photo']) ? $define_company_website."uploads/reference_library/".$row['photo'] : "",
            'tags'   => $row['tags'],
            'count'  => $row['usage_count'] ?? 1,
            'rating' => $row['rating'] ?? 5,
        ];
    }

    echo json_encode(['success' => true, 'refs' => $refs, 'total' => count($refs)]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
<?php

include("../config/database.php"); 
include("../config/auth_check.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

header('Content-Type: application/json');

// 2. Process incoming request
$action = $_GET['action'] ?? '';

if ($action === 'fetch') {
    try {
        $result = $con->query("SELECT id, name, status as is_active FROM category ORDER BY id ASC");

        // 2. Fetch all rows directly from that result object
        $tags = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $tags]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read raw JSON data payload from JavaScript fetch
    $input = json_decode(file_get_contents('php://input'), true);
    $tagName = trim($input['name'] ?? '');

    if (empty($tagName)) {
        echo json_encode(['status' => 'error', 'message' => 'Tag name cannot be empty']);
        exit;
    }

    try {
        // Prepare statements to prevent SQL Injection
        $stmt = $con->prepare("INSERT INTO category (name, status) VALUES (?, 0)");
        $stmt->execute([$tagName]);
        
        // Get the generated ID to append directly to the frontend DOM array
        $newId = $con->lastInsertId();
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => $newId,
                'name' => $tagName,
                'is_active' => 0
            ]
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry error code
            echo json_encode(['status' => 'error', 'message' => 'This tag already exists.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    exit;
}

// If no action matched
echo json_encode(['status' => 'error', 'message' => 'Invalid Request Action']);
exit;
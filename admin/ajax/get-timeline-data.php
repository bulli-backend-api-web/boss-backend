<?php
include("../config/database.php");
include("../config/auth_check.php");
header('Content-Type: application/json');

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$quarterDefs = [
    1 => ['label' => 'Q1', 'range' => 'Jan – Mar', 'themes' => 'Bridal season · Valentine · Holi', 'color' => '#d6336c', 'months' => [1,2,3]],
    2 => ['label' => 'Q2', 'range' => 'Apr – Jun', 'themes' => 'Eid · Summer · Akshaya Tritiya', 'color' => '#2f9e44', 'months' => [4,5,6]],
    3 => ['label' => 'Q3', 'range' => 'Jul – Sep', 'themes' => 'Rakshabandhan · Janmashtami · Pre-Navratri', 'color' => '#1971c2', 'months' => [7,8,9]],
    4 => ['label' => 'Q4', 'range' => 'Oct – Dec', 'themes' => 'Navratri · Dussehra · Diwali · Wedding peak', 'color' => '#e03131', 'months' => [10,11,12]],
];

$monthNames = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

// Fetch all refs for the year, grouped by month
$sql = "SELECT id, code, name as title, photo as image, tags, MONTH(created_at) as month
        FROM reference_library
        WHERE YEAR(created_at) = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $year);
$stmt->execute();
$result = $stmt->get_result();

$refsByMonth = [];
while ($row = $result->fetch_assoc()) {
    $refsByMonth[$row['month']][] = $row;
}

$output = [];
foreach ($quarterDefs as $qNum => $q) {
    $months = [];
    foreach ($q['months'] as $mNum) {
        $refs = $refsByMonth[$mNum] ?? [];
        $months[] = [
            'name'  => $monthNames[$mNum],
            'count' => count($refs),
            'refs'  => array_map(function ($r) {
                return [
                    'code'   => $r['code'],
                    'title'  => $r['title'],
                    'image'  => $r['image'],
                    'tags'   => $r['tags'] ? explode(',', $r['tags']) : [],
                    'rating' => (int)$r['rating'],
                ];
            }, $refs)
        ];
    }

    $output[] = [
        'q'        => $q['label'],
        'year'     => $year,
        'range'    => $q['range'],
        'themes'   => $q['themes'],
        'color'    => $q['color'],
        'campaign' => null, // add campaign lookup here if you have a campaigns table
        'months'   => $months
    ];
}

echo json_encode($output);
$con->close();
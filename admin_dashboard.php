<?php
include 'includes/config.php';          // your DB config
header('Content-Type: application/json');

$response = [];

// total *distinct* buses (one per bus_number)
$sql = "SELECT COUNT(DISTINCT bus_number) AS total FROM add_routes";
$row = $conn->query($sql)->fetch_assoc();
$response['buses'] = (int)$row['total'];

// total students
$row = $conn->query("SELECT COUNT(*) AS total FROM student_signup")->fetch_assoc();
$response['students'] = (int)$row['total'];

// total routes
$row = $conn->query("SELECT COUNT(*) AS total FROM add_routes")->fetch_assoc();
$response['routes'] = (int)$row['total'];

// total drivers
$row = $conn->query("SELECT COUNT(*) AS total FROM driver_signup")->fetch_assoc();
$response['drivers'] = (int)$row['total'];

echo json_encode([
    "status" => "success",
    "data"   => $response
]);

$conn->close();
?>

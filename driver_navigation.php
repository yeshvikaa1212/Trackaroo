<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$route_number = $data['route_number'] ?? null;

if (!$route_number) {
    echo json_encode([
        "status" => "error",
        "message" => "Route number not provided"
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT route_number, route, time FROM add_routes WHERE route_number = ?");
$stmt->bind_param("s", $route_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $route = $result->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "data" => $route
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Route not found"
    ]);
}

$stmt->close();
$conn->close();
?>

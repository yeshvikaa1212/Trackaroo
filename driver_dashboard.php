<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$route_number = $data['route_number'] ?? '';

if (!$route_number) {
    echo json_encode(["status" => "error", "message" => "Route number is required"]);
    exit;
}

// Get bus number for selected route
$route_query = $conn->query("SELECT bus_number FROM add_routes WHERE route_number = '$route_number'");
if ($route_query->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Invalid route number"]);
    exit;
}
$route_data = $route_query->fetch_assoc();
$bus_number = $route_data['bus_number'];

// Count total stops assigned to this bus
$count_query = $conn->query("SELECT COUNT(*) as total_stops FROM add_routes WHERE bus_number = '$bus_number'");
$count_data = $count_query->fetch_assoc();

$response = [
    "status" => "success",
    "bus_number" => $bus_number,
    "total_stops" => $count_data['total_stops']
];

echo json_encode($response);
$conn->close();
?>

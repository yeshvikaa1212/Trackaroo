<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$route_number = $data['route_number'];
$route = $data['route'];
$time = $data['time'];  // should be like "08:30 AM"
$bus_number = $data['bus_number'];

$stmt = $conn->prepare("INSERT INTO add_routes (route_number, route, time, bus_number) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $route_number, $route, $time, $bus_number);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Route added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add route"]);
}

$stmt->close();
$conn->close();
?>

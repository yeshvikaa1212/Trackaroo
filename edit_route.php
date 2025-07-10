<?php
include 'includes/config.php'; // Adjust path if needed
header("Content-Type: application/json");

// Decode JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (
    !isset($data['route_number']) || !isset($data['route']) ||
    !isset($data['time']) || !isset($data['bus_number'])
) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Extract data
$route_number = $data['route_number'];
$route = $data['route'];
$time = $data['time'];
$bus_number = $data['bus_number'];

// Prepare and execute update query
$stmt = $conn->prepare("UPDATE add_routes SET route = ?, time = ?, bus_number = ? WHERE route_number = ?");
$stmt->bind_param("ssss", $route, $time, $bus_number, $route_number);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Route updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No route found or data unchanged"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update route"]);
}

// Cleanup
$stmt->close();
$conn->close();
?>

<?php
include 'includes/config.php';
header("Content-Type: application/json");

// Receive route_number and driver_id from the request
$data = json_decode(file_get_contents("php://input"), true);
$route_number = $data['route_number'] ?? null;
$driver_id = $data['driver_id'] ?? null;

if (!$route_number || !$driver_id) {
    echo json_encode(["status" => "error", "message" => "Missing route or driver ID"]);
    exit;
}

// Fetch driver info
$driver_sql = "SELECT driver_id, driver_name, phone_number, license_number FROM driver_signup WHERE driver_id = ?";
$stmt = $conn->prepare($driver_sql);
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$driver_result = $stmt->get_result();

if ($driver_result->num_rows > 0) {
    $driver = $driver_result->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "driver_data" => $driver,
        "selected_route" => $route_number
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Driver not found"]);
}

$stmt->close();
$conn->close();
?>

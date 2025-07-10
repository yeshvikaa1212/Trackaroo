<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$student_id = $data['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(["status" => "error", "message" => "Missing student ID"]);
    exit;
}

// 1. Get student details and route number
$stmt = $conn->prepare("SELECT name, route_number FROM student_signup WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$studentResult = $stmt->get_result();

if ($studentResult->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Student not found"]);
    exit;
}

$student = $studentResult->fetch_assoc();
$route_number = $student['route_number'];

// 2. Get route details (bus number, pickup time, route)
$stmt = $conn->prepare("SELECT bus_number, time, route FROM add_routes WHERE route_number = ?");
$stmt->bind_param("s", $route_number);
$stmt->execute();
$routeResult = $stmt->get_result();

if ($routeResult->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Route not found"]);
    exit;
}

$route = $routeResult->fetch_assoc();

// 3. Calculate ETA
date_default_timezone_set("Asia/Kolkata");
$pickupTime = strtotime($route['time']);
$currentTime = time();
$eta = max(0, round(($pickupTime - $currentTime) / 60));

// 4. Get driver details using assign_driver_3.php logic
$stmt = $conn->prepare("SELECT driver_id FROM assign_driver_3 WHERE route_number = ?");
$stmt->bind_param("s", $route_number);
$stmt->execute();
$driverIdResult = $stmt->get_result();

$driver_name = "Not Assigned";
$driver_phone = "Not Available";

if ($driverIdResult->num_rows > 0) {
    $driverRow = $driverIdResult->fetch_assoc();
    $driver_id = $driverRow['driver_id'];

    $stmt = $conn->prepare("SELECT driver_name, phone_number FROM driver_signup WHERE driver_id = ?");
    $stmt->bind_param("s", $driver_id);
    $stmt->execute();
    $driverResult = $stmt->get_result();

    if ($driverResult->num_rows > 0) {
        $driver = $driverResult->fetch_assoc();
        $driver_name = $driver['driver_name'];
        $driver_phone = $driver['phone_number'];
    }
}

// 5. Send JSON response
echo json_encode([
    "status" => "success",
    "data" => [
        "student_name"     => $student['name'],
        "route_number"     => $route_number,
        "route"            => $route['route'],
        "bus_number"       => $route['bus_number'],
        "pickup_time"      => date("h:i A", $pickupTime),
        "eta_minutes_left" => $eta,
        "driver_name"      => $driver_name,
        "driver_phone"     => $driver_phone
    ]
]);

$conn->close();
?>

<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

// Assume 'student_id' is passed in the request
if (empty($data['student_id'])) {
    echo json_encode(["status" => "error", "message" => "Student ID required."]);
    exit;
}

$student_id = $data['student_id'];

// Get student info and assigned route
$stmt = $conn->prepare("SELECT name, route_number FROM student_signup WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Student not found."]);
    exit;
}

$student = $result->fetch_assoc();
$route_number = $student['route_number'];

// Get route details from add_route
$stmt = $conn->prepare("SELECT bus_number, time AS time, route FROM add_routes WHERE route_number = ?");
$stmt->bind_param("s", $route_number);
$stmt->execute();
$routeResult = $stmt->get_result();

if ($routeResult->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Route not found."]);
    exit;
}

$route = $routeResult->fetch_assoc();

// Calculate ETA (e.g. if pickup is at 7:30 AM and now is 7:25 → ETA = 5 mins)
date_default_timezone_set("Asia/Kolkata"); // adjust if needed
$pickupTime = strtotime($route['time']);
$currentTime = time();
$eta = max(0, round(($pickupTime - $currentTime) / 60)); // in minutes

// Format today's date nicely
$today = date("l, F j, Y");

echo json_encode([
    "status" => "success",
    "data" => [
        "student_name"     => $student['name'],
        "date"             => $today,
        "bus_number"       => $route['bus_number'],
        "route_number"     => $route_number,
        "pickup_time"      => date("h:i A", $pickupTime),
        "route"            => $route['route'],
        "eta_minutes_left" => $eta
    ]
]);
?>

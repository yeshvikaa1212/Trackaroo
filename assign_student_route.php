<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data['student_id'] ?? null;
$address = $data['address'] ?? null;

if (!$student_id || !$address) {
    echo json_encode([
        "status" => "error",
        "message" => "Student ID and address are required."
    ]);
    exit;
}

// Get all routes
$sql = "SELECT route_number, route FROM add_routes";
$result = $conn->query($sql);

$route_number = null;

while ($row = $result->fetch_assoc()) {
    $route_parts = explode(" to ", strtolower($row['route']));
    $pickup = trim($route_parts[0]);
    $drop = trim($route_parts[1] ?? "");

    if (stripos($address, $pickup) !== false || stripos($address, $drop) !== false) {
        $route_number = $row['route_number'];
        break;
    }
}

if ($route_number) {
    // Update student record
    $update = $conn->prepare("UPDATE student_signup SET route_number = ? WHERE student_id = ?");
    $update->bind_param("ss", $route_number, $student_id);
    $update->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Route assigned",
        "route_number" => $route_number
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No matching route found for the address."
    ]);
}

$conn->close();
?>

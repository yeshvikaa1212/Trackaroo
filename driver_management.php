<?php
include 'includes/config.php'; // Adjust path if needed

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

// Fetch all drivers
$sql = "SELECT driver_id, driver_name, phone_number, license_number FROM driver_signup";
$result = $conn->query($sql);

$drivers = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $drivers[] = [
            "driver_id" => $row['driver_id'],
            "driver_name" => $row['driver_name'],
            "phone_number" => $row['phone_number'],
            "license_number" => $row['license_number']
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $drivers
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No drivers found"
    ]);
}

$conn->close();
?>

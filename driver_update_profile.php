<?php
include 'includes/config.php';

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->driver_id) &&
    isset($data->driver_name) &&
    isset($data->phone_number)
) {
    $driver_id = $data->driver_id;
    $driver_name = $data->driver_name;
    $phone_number = $data->phone_number;

    $stmt = $conn->prepare("UPDATE driver_signup SET name=?, phone=? WHERE driver_id=?");
    $stmt->bind_param("sss", $driver_name, $phone_number, $driver_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update profile"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>

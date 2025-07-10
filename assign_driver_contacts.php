<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['phone_number'])) {
    $phone_number = $data['phone_number'];

    // Check if phone number already exists
    $check = $conn->prepare("SELECT id FROM assign_driver_contacts WHERE phone_number = ?");
    $check->bind_param("s", $phone_number);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        // Insert if it doesn't exist
        $stmt = $conn->prepare("INSERT INTO assign_driver_contacts (phone_number) VALUES (?)");
        $stmt->bind_param("s", $phone_number);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Phone number stored successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to store phone number"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "info", "message" => "Phone number already exists"]);
    }

    $check->close();
} else {
    echo json_encode(["status" => "error", "message" => "Missing phone number"]);
}

$conn->close();
?>

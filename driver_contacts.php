<?php
include 'includes/config.php';
header("Content-Type: application/json");

$sql = "SELECT phone_number FROM assign_driver_contacts";
$result = $conn->query($sql);

$contacts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row['phone_number'];
    }

    echo json_encode([
        "status" => "success",
        "data" => $contacts
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No assigned driver phone numbers found."
    ]);
}

$conn->close();
?>

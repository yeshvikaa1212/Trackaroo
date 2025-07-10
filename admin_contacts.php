<?php
include 'includes/config.php';
header("Content-Type: application/json");

// Decode JSON body as associative array
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['transport']) || !isset($data['admin'])) {
    echo json_encode(["status" => "error", "message" => "Missing transport or admin field"]);
    exit;
}

$transport = $data['transport'];
$admin = $data['admin'];

// Prepare and execute SQL insert
$stmt = $conn->prepare("INSERT INTO admin_contacts (transport, admin) VALUES (?, ?)");
$stmt->bind_param("ss", $transport, $admin);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Contacts added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add contacts"]);
}

$stmt->close();
$conn->close();
?>

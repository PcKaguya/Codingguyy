<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('Content-Type: application/json');

    if (!isset($_GET['uniqueid'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Unique ID not provided"]);
        exit;
    }

    $uniqueId = $_GET['uniqueid'];
    $conn = new mysqli("localhost", "root", "", "webhookprotector");

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
        exit;
    }

    $stmt = $conn->prepare("SELECT blocked_create FROM webhooks WHERE unique_id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param("s", $uniqueId);
    $stmt->execute();
    $stmt->bind_result($blockedCreate);
    $stmt->fetch();
    $stmt->close();

    if ($blockedCreate === null) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Unique ID not found"]);
        $conn->close();
        exit;
    }

    $newBlockedStatus = ($blockedCreate === 'YES') ? 'NO' : 'YES';
    $stmt = $conn->prepare("UPDATE webhooks SET blocked_create = ? WHERE unique_id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param("ss", $newBlockedStatus, $uniqueId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Webhook blocked_create status set to $newBlockedStatus"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error executing update: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}
?>

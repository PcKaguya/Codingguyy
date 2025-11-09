<?php
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    header('Content-Type: application/json');
    
    if (isset($_GET['uniqueid'])) {
        $uniqueId = filter_var($_GET['uniqueid'], FILTER_SANITIZE_STRING);

        $conn = new mysqli("localhost", "root", "", "webhookprotector");

        if ($conn->connect_error) {
            echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM webhooks WHERE unique_id = ?");
        $stmt->bind_param("s", $uniqueId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["status" => "success", "message" => "Webhook successfully deleted"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Webhook not found"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Unique ID not provided"]);
    }
    exit;
}
?>

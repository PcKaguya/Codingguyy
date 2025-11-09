<?php
$config = include('config.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('HTTP/1.0 403 Forbidden');
   
}
    
    
if (isset($_GET['uniqueid'])) {
    $uniqueId = $_GET['uniqueid'];

    function getWebhookFromDatabase($uniqueId) {
        $dbHost = 'localhost';
		$dbUsername = 'root';
		$dbPassword = '';
		$dbName = 'webhookprotector';
        $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT webhook, ip_address, blocked_post FROM webhooks WHERE unique_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $uniqueId);
        $stmt->execute();
        $stmt->bind_result($webhook, $ipAddress, $blockedPost);
        $stmt->fetch();

        $stmt->close();
        $conn->close();

        return [$webhook, $ipAddress, $blockedPost];
    }

    list($webhook, $ipAddress, $blockedPost) = getWebhookFromDatabase($uniqueId);

    if ($blockedPost === 'YES') {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Posting is blocked for this webhook."]);
        exit;
    }

    if ($webhook) {
        $ch = curl_init($webhook);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (isset($_FILES['file'])) {
            $filePath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $postData = [
                "file" => new CURLFile($filePath, mime_content_type($filePath), $fileName),
                "username" => $uniqueId
            ];

            $jsonData = file_get_contents("php://input");
            if ($jsonData) {
                $decodedData = json_decode($jsonData, true);
                if (isset($decodedData['content'])) {
                    $postData['payload_json'] = json_encode([
                        'content' => $decodedData['content'],
                        'uniqueid' => $uniqueId,
                        'username' => $uniqueId
                    ]);
                }
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        } else {
            $jsonData = file_get_contents("php://input");
            if ($jsonData) {
                $decodedData = json_decode($jsonData, true);
                $decodedData['uniqueid'] = $uniqueId;
                $decodedData['username'] = $uniqueId;
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($decodedData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            } else {
                echo "only post requests allowed";
                exit;
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $webhookURL = $config['webhookURL'];

        $ch = curl_init($webhookURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (isset($_FILES['file'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($decodedData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        echo "Message sent successfully";

    } else {
        echo "only post requests allowed";
    }
} else {
    echo "only post requests allowed";
}

?>

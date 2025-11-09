<?php
// Webhook URL for Discord (replace this with your actual Discord webhook URL)
$discord_webhook_url = 'https://discord.com/api/webhooks/1304169279947673700/57uxYb7uFCzUQKfzHWzDEb5bAe9GGralyYphne2gLSTebUWHc1r2DcEtgD6MkYXxSAGG';

// Check if the POST request has the 'message' field
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    // Sanitize the input message to prevent any harmful injections
    $message = htmlspecialchars($_POST['message']);

    // Create the payload to send to the Discord webhook
    $data = array(
        'content' => $message,  // The message content you want to send to Discord
    );

    // Initialize cURL to send the POST request to the Discord webhook
    $ch = curl_init($discord_webhook_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the request and silently handle it
    curl_exec($ch);
    curl_close($ch);

    header("HTTP/1.0 500 Internal Server Error");
} else {
    header("HTTP/1.0 500 Internal Server Error");
}
?>

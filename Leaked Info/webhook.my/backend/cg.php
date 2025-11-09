<?php
session_start();

$adminPassword = 'cgisbest';
$updateDelay = 2;

if (!isset($_SESSION['lastUpdateTime'])) {
    $_SESSION['lastUpdateTime'] = time();
}

$currentWebhookURL = '';
if (file_exists('config.php')) {
    $config = include('config.php');
    $currentWebhookURL = $config['webhookURL'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password'])) {
        if ($_POST['password'] === $adminPassword) {
            $_SESSION['loggedin'] = true;
        } else {
            echo "Incorrect password!";
        }
    } elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        if (isset($_POST['newWebhookURL'])) {
            $newWebhookURL = $_POST['newWebhookURL'];
            $currentTime = time();

            if (!filter_var($newWebhookURL, FILTER_VALIDATE_URL)) {
                echo "Invalid URL format.";
            } elseif (!preg_match('/^https:\/\/discord\.com\/api\/webhooks\/\d+\/[a-zA-Z0-9_-]+$/', $newWebhookURL)) {
                echo "Invalid Discord webhook URL.";
            } elseif (($currentTime - $_SESSION['lastUpdateTime']) < $updateDelay) {
                echo "You must wait a bit before updating the webhook URL again.";
            } else {
                $configContent = "<?php\nreturn [\n    'webhookURL' => '$newWebhookURL'\n];\n";
                file_put_contents('config.php', $configContent);
                $_SESSION['lastUpdateTime'] = $currentTime;
                $currentWebhookURL = $newWebhookURL;
                echo "Webhook URL updated successfully!";
            }
        }
    }
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '
    <form method="post">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
        <button type="submit">Login</button>
    </form>
    ';
} else {
    echo "<p>Current Webhook URL: <strong>" . htmlspecialchars($currentWebhookURL) . "</strong></p>";
    echo '
    <form method="post">
        <label for="newWebhookURL">New Webhook URL:</label>
        <input type="text" name="newWebhookURL" id="newWebhookURL">
        <button type="submit">Update</button>
    </form>
    ';
}
?>

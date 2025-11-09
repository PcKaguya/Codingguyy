<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");

$webhook = "https://discord.com/api/webhooks/1328461024168378398/nC1_X3A5wRnwXH7_6VSPm7p4QKGfrbpG4Mv1Jibk6ELEIHLiFCS96s03UDKZb5KViahH";


$ch = curl_init($webhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

if (isset($_FILES['file'])) {
    $filePath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $postData = [
        "file" => new CURLFile($filePath, mime_content_type($filePath), $fileName),
    ];

    $jsonData = file_get_contents("php://input");
    if ($jsonData) {
        $decodedData = json_decode($jsonData, true);
        if (isset($decodedData['content'])) {
            $postData['payload_json'] = json_encode(['content' => $decodedData['content']]);
        }
    }
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

} else {
    $jsonData = file_get_contents("php://input");
    if ($jsonData) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    } else {
        echo "No file uploaded and no content provided.";
        exit;
    }
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false || $httpCode >= 400) {
    echo "Error occurred while forwarding content to the dynamic webhook.";
} else {
    echo $response;
}

curl_close($ch);
sleep(1);

?>

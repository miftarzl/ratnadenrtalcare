<?php
header('Content-Type: application/json');

// Set error handling
error_reporting(0);

// Tangkap request body dari browser
$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);

if (!$inputData || empty(trim($inputData['message'] ?? ''))) {
    http_response_code(400);
    echo json_encode(['error' => 'Pesan tidak boleh kosong']);
    exit;
}

// Endpoint internal Flask chatbot di dalam jaringan Docker
// Jika di luar Docker, fallback ke 127.0.0.1:5001 / 5000
$chatbotHost = getenv('CHATBOT_HOST') ?: (gethostbyname('chatbot') !== 'chatbot' ? 'chatbot' : '127.0.0.1');
$chatbotPort = getenv('CHATBOT_PORT') ?: ($chatbotHost === 'chatbot' ? '5000' : '5001');
$chatbotUrl = "http://{$chatbotHost}:{$chatbotPort}/chatbot";

// Kirim request dari PHP ke Flask via cURL
$ch = curl_init($chatbotUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $inputJSON);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Gagal menghubungi server chatbot',
        'details' => $curlError ?: "HTTP status code: $httpCode"
    ]);
    exit;
}

echo $response;
?>

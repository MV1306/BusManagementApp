<?php
// save_route.php

$routeId = $_GET['id'] ?? null;
if (!$routeId) {
    echo json_encode(['success' => false, 'message' => 'Route ID missing']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$apiUrl = "https://busmanagementapi.onrender.com//BusManagementAPI/UpdateRoute/" . urlencode($routeId);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen(json_encode($input))
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'API request failed']);
    curl_close($ch);
    exit;
}

curl_close($ch);

$responseData = json_decode($response, true);

// Return API response status to frontend
if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode(['success' => true, 'message' => 'Route updated successfully']);
} else {
    $errorMsg = $responseData['message'] ?? 'Failed to update route';
    echo json_encode(['success' => false, 'message' => $errorMsg]);
}

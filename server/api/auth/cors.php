<?php
// Set the allowed origin. Replace with your exact Netlify domain.
$allowedOrigin = 'https://project-kelompok-5.netlify.app';

// Set other CORS headers
header("Access-Control-Allow-Origin: $allowedOrigin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow necessary methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // If you're using cookies/credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204); // Respond with 200 OK for preflight
    exit();
}
?>
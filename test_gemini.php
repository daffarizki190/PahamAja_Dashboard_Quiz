<?php
require 'vendor/autoload.php';
use Illuminate\Support\Facades\Http;

// Mocking minimal environment
$apiKey = 'AIzaSyDVp-TO_by8O7ioErK3BCHfeF221Zap7kw';
$model = 'gemini-1.5-flash';

echo "Testing Gemini API with model: $model\n";

$response = @file_get_contents("https://generativelanguage.googleapis.com/v1beta/models/{$model}?key={$apiKey}");

if ($response === false) {
    echo "Error: Could not reach API or Model not found.\n";
    $headers = $http_response_header ?? [];
    foreach ($headers as $h) echo "Header: $h\n";
} else {
    echo "Success! Model info:\n";
    echo $response . "\n";
}

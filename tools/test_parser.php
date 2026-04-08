<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\FileParserService;

$filePath = 'C:/Users/MyBook Hype AMD/Downloads/COM_PRK_2.2.13 Petunjuk Kehilangan Tiket dan Kartu Kendaraan Rev 00.docx';

if (!file_exists($filePath)) {
    die("File not found: $filePath\n");
}

$outputFile = __DIR__ . '/extracted_text.txt';

try {
    $parser = new FileParserService();
    $text = $parser->parseFile($filePath, 'docx');
    
    file_put_contents($outputFile, "--- EXTRACTED TEXT START ---\n" . $text . "\n--- EXTRACTED TEXT END ---\nLength: " . strlen($text) . " bytes\n");
    echo "Output written to $outputFile\n";
} catch (Exception $e) {
    file_put_contents($outputFile, "Error: " . $e->getMessage() . "\n");
    echo "Error written to $outputFile\n";
}


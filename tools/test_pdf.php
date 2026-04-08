<?php
require 'vendor/autoload.php';

$filePath = 'C:/Users/MyBook Hype AMD/Downloads/COM_PRK_2.2.13 Petunjuk Kehilangan Tiket dan Kartu Kendaraan Rev 00.pdf';

if (!file_exists($filePath)) {
    die("File not found at: $filePath\n");
}

try {
    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($filePath);
    
    echo "PAGES: " . count($pdf->getPages()) . "\n";
    
    $details = $pdf->getDetails();
    echo "DETAILS:\n";
    print_r($details);
    
    $text = $pdf->getText();
    echo "TEXT LENGTH: " . strlen($text) . "\n";
    echo "FIRST 100 CHARS:\n";
    echo substr($text, 0, 100) . "\n";
    
    // Check if any page has text
    foreach ($pdf->getPages() as $index => $page) {
        $pText = $page->getText();
        if (trim($pText) !== '') {
            echo "PAGE $index HAS TEXT (" . strlen($pText) . " chars)\n";
        }
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

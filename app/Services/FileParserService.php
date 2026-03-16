<?php

namespace App\Services;

use Exception;
use PhpOffice\PhpPresentation\IOFactory as PptIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser;

class FileParserService
{
    /**
     * Parse the given file and return the text content.
     *
     * @throws Exception
     */
    public function parseFile(string $filePath, string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => $this->parsePdf($filePath),
            'docx' => $this->parseDocx($filePath),
            'pptx' => $this->parsePptx($filePath),
            default => throw new Exception("Unsupported file format: {$extension}"),
        };
    }

    /**
     * Extract text from PDF.
     */
    private function parsePdf(string $filePath): string
    {
        $parser = new Parser;
        $pdf = $parser->parseFile($filePath);

        return $pdf->getText();
    }

    /**
     * Extract text from DOCX.
     */
    private function parseDocx(string $filePath): string
    {
        $phpWord = WordIOFactory::load($filePath);
        $fullText = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $fullText .= $this->extractNodeText($element);
            }
        }

        return $fullText;
    }

    /**
     * Recursively extract text from PhpWord elements.
     */
    private function extractNodeText($element): string
    {
        $text = '';
        
        // Handle elements that have direct text content (like Text elements)
        if (method_exists($element, 'getText')) {
            $content = $element->getText();
            if (is_string($content)) {
                $text .= $content . "\n";
            }
        }
        
        // Recurse into child elements (like TextRun, Table, etc.)
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $text .= $this->extractNodeText($child);
            }
        }

        return $text;
    }

    /**
     * Extract text from PPTX.
     */
    private function parsePptx(string $filePath): string
    {
        $presentation = PptIOFactory::load($filePath);
        $fullText = '';
        foreach ($presentation->getAllSlides() as $slide) {
            foreach ($slide->getShapeCollection() as $shape) {
                if (method_exists($shape, 'getText')) {
                    $textObj = $shape->getText();
                    foreach ($textObj->getParagraphs() as $paragraph) {
                        foreach ($paragraph->getRichTextElements() as $richText) {
                            $fullText .= $richText->getText();
                        }
                        $fullText .= "\n";
                    }
                }
            }
        }

        return $fullText;
    }
}

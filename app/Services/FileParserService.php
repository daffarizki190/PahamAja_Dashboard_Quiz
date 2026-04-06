<?php

namespace App\Services;

use Exception;
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
        $fullText = '';

        foreach ($pdf->getPages() as $page) {
            try {
                $pageText = $page->getText();
                if (trim($pageText) !== '') {
                    $fullText .= $pageText . "\n";
                }
            } catch (\Throwable) {
                // Skip problematic pages
            }
        }

        if (trim($fullText) === '') {
            // Try fallback method — getText on the entire document
            $fullText = $pdf->getText();
        }

        return $fullText;
    }

    /**
     * Extract text from DOCX.
     */
    private function parseDocx(string $filePath): string
    {
        if (! class_exists('ZipArchive')) {
            throw new Exception('ZipArchive extension is required to parse DOCX files. Enable the PHP zip extension (php-zip).');
        }

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

        if (is_null($element)) {
            return '';
        }

        // Handle specific element types for better structure
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $text .= $element->getText();
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $child) {
                $text .= $this->extractNodeText($child);
            }
            $text .= "\n";
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $child) {
                        $text .= $this->extractNodeText($child);
                    }
                    $text .= "\t"; // Cell separator
                }
                $text .= "\n";
            }
        } elseif (method_exists($element, 'getElements')) {
            try {
                $children = $element->getElements();
                if (is_iterable($children)) {
                    foreach ($children as $child) {
                        $text .= $this->extractNodeText($child);
                    }
                }
            } catch (\Throwable) {
            }
        } elseif (method_exists($element, 'getText')) {
            try {
                $elementText = $element->getText();
                if (is_string($elementText)) {
                    $text .= $elementText;
                    if (! ($element instanceof \PhpOffice\PhpWord\Element\Text)) {
                        $text .= "\n";
                    }
                }
            } catch (\Throwable) {
            }
        }

        return $text;
    }

    private function extractTextContent($content): string
    {
        if (is_null($content)) {
            return '';
        }

        if (is_string($content) || is_numeric($content)) {
            $value = trim((string) $content);

            return $value === '' ? '' : $value."\n";
        }

        if (is_array($content)) {
            $text = '';
            foreach ($content as $item) {
                $text .= $this->extractTextContent($item);
            }

            return $text;
        }

        if (is_object($content)) {
            if (is_callable([$content, 'getElements'])) {
                try {
                    $children = $content->getElements();
                    if (! is_iterable($children)) {
                        return '';
                    }

                    $text = '';
                    foreach ($children as $child) {
                        $text .= $this->extractNodeText($child);
                    }

                    return $text;
                } catch (\Throwable) {
                    return '';
                }
            }

            if (is_callable([$content, 'getText'])) {
                try {
                    return $this->extractTextContent($content->getText());
                } catch (\Throwable) {
                    return '';
                }
            }

            if (method_exists($content, '__toString')) {
                $value = trim((string) $content);

                return $value === '' ? '' : $value."\n";
            }
        }

        return '';
    }

    /**
     * Extract text from PPTX using native ZipArchive (no GD required).
     * PPTX files are ZIP archives. Each slide is at ppt/slides/slideN.xml.
     */
    private function parsePptx(string $filePath): string
    {
        if (! class_exists('ZipArchive')) {
            throw new Exception('ZipArchive extension is required to parse PPTX files. Enable the PHP zip extension (php-zip).');
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception('Failed to open PPTX file. The file may be corrupted.');
        }

        $fullText = '';
        $slideIndex = 1;

        while (true) {
            $slideXml = $zip->getFromName("ppt/slides/slide{$slideIndex}.xml");
            if ($slideXml === false) {
                break;
            }

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($slideXml);
            libxml_clear_errors();

            if ($xml !== false) {
                $xml->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
                $textNodes = $xml->xpath('//a:t');
                if ($textNodes) {
                    foreach ($textNodes as $node) {
                        $text = trim((string) $node);
                        if ($text !== '') {
                            $fullText .= $text . ' ';
                        }
                    }
                    $fullText .= "\n";
                }
            }

            $slideIndex++;
        }

        $zip->close();

        return trim($fullText);
    }
}

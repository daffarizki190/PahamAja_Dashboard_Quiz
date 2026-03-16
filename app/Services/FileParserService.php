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

        if (is_object($element) && is_callable([$element, 'getText'])) {
            try {
                $text .= $this->extractTextContent($element->getText());
            } catch (\Throwable) {
            }
        }

        if (is_object($element) && is_callable([$element, 'getElements'])) {
            try {
                $children = $element->getElements();
                if (is_iterable($children)) {
                    foreach ($children as $child) {
                        $text .= $this->extractNodeText($child);
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

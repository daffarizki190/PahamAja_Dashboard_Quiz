<?php

namespace Tests\Unit;

use App\Services\FileParserService;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\PhpWord;
use Mockery;

class FileParserTest extends TestCase
{
    /**
     * Test recursive text extraction from Word elements.
     */
    public function test_extract_node_text_handles_text_run()
    {
        $service = new FileParserService();
        
        // Use reflection to access private method for testing
        $reflection = new \ReflectionClass(FileParserService::class);
        $method = $reflection->getMethod('extractNodeText');
        $method->setAccessible(true);

        // Mock a TextRun element which contains other elements
        $mockTextRun = Mockery::mock(TextRun::class);
        $mockTextRun->shouldReceive('getText')->andReturn(null); // TextRun itself returns null for getText()
        
        $mockTextChild = Mockery::mock(\PhpOffice\PhpWord\Element\Text::class);
        $mockTextChild->shouldReceive('getText')->andReturn('Hello World');
        
        $mockTextRun->shouldReceive('getElements')->andReturn([$mockTextChild]);

        $result = $method->invoke($service, $mockTextRun);

        $this->assertEquals("Hello World\n", $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

<?php

$dir = __DIR__ . '/resources/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Match avatar_url($VAR->avatar)
        // Replaces with avatar_url($VAR->avatar, $VAR->name)
        // It should match strings like avatar_url($participant->employee->avatar) and replace with avatar_url($participant->employee->avatar, $participant->employee->name)
        
        $newContent = preg_replace_callback(
            '/avatar_url\(\s*(\$[^>]+(?:->[a-zA-Z0-9_]+)*)->avatar\s*\)/',
            function ($matches) {
                $baseVar = $matches[1];
                return "avatar_url({$baseVar}->avatar, {$baseVar}->name)";
            },
            $content
        );
        
        if ($newContent !== $content) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}

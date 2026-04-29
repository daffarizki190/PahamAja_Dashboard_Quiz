<?php

$files = [
    'resources/views/quiz/result.blade.php',
    'resources/views/quiz/confirm.blade.php',
    'resources/views/admin/quizzes/show.blade.php',
    'resources/views/admin/quizzes/index.blade.php',
    'resources/views/admin/employees/show.blade.php',
    'resources/views/admin/dashboard.blade.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (!file_exists($path)) continue;

    $content = file_get_contents($path);

    // This regex will match blocks like:
    // @if($emp && $emp->avatar)
    //   <img ...>
    // @else
    //   ...
    // @endif
    // We want to keep ONLY the content between @if and @else (which is the img part)
    
    // Using a more generic approach: Find @if(...) related to avatar, capture the true branch, and replace the whole block
    
    $newContent = preg_replace(
        '/@if\([^)]*avatar\s*\)(.*?)@else.*?@endif/s',
        '$1',
        $content
    );

    // Also replace simple @if without @else
    $newContent = preg_replace(
        '/@if\([^)]*avatar\s*\)(.*?)@endif/s',
        '$1',
        $newContent
    );
    
    if ($newContent !== $content) {
        file_put_contents($path, $newContent);
        echo "Updated: $file\n";
    }
}

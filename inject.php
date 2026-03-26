<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/\.blade\.php$/', RegexIterator::MATCH);

foreach ($files as $file) {
    if ($file->isFile()) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'prevent-double-submit.js') === false) {
            $scriptTag = "<script src=\"{{ asset('js/prevent-double-submit.js') }}\"></script>\n</body>";
            // Replace last occurance of </body> or just str_replace if there's only one
            $new_content = str_replace('</body>', $scriptTag, $content);
            if ($new_content !== $content) {
                file_put_contents($file->getPathname(), $new_content);
                echo "Updated: " . $file->getPathname() . "\n";
            }
        }
    }
}
echo "Done injection.\n";

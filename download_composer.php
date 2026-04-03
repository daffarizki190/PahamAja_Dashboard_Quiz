<?php

echo "Downloading composer.phar...\n";
if (copy('https://getcomposer.org/composer.phar', 'composer.phar')) {
    echo "Success!\n";
} else {
    echo "Failed to download composer.phar\n";
}

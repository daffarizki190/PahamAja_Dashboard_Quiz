@echo off
echo Downloading composer.phar...
php -r "copy('https://getcomposer.org/composer.phar', 'composer.phar');"
if exist composer.phar (
    echo Successfully downloaded composer.phar.
    echo Installing laravel-dompdf...
    php composer.phar require barryvdh/laravel-dompdf
    echo Installation attempt finished.
) else (
    echo Failed to download composer.phar.
)
pause

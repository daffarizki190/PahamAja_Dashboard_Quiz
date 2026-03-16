<?php

echo '<h1>Isolated PHP Runtime Test</h1>';
echo '<p>PHP Version: '.phpversion().'</p>';
if (function_exists('mongodb_driver_version')) {
    echo '<p>MongoDB Extension is LOADED.</p>';
} else {
    echo '<p>MongoDB Extension is NOT LOADED.</p>';
}
echo '<p>Hello, Vercel!</p>';

<?php

use App\Providers\AppServiceProvider;
use Barryvdh\DomPDF\ServiceProvider as DomPdfServiceProvider;
use SimpleSoftwareIO\QrCode\QrCodeServiceProvider;

return [
    AppServiceProvider::class,
    DomPdfServiceProvider::class,
    QrCodeServiceProvider::class,
];

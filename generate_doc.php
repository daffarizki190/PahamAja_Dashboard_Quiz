<?php

require 'vendor/autoload.php';

use PhpOffice\Common\Adler32;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

$phpWord = new PhpWord();

// Define fonts
$phpWord->addFontStyle('T1', array('name' => 'Arial', 'size' => 16, 'bold' => true));
$phpWord->addFontStyle('T2', array('name' => 'Arial', 'size' => 14, 'bold' => true));
$phpWord->addFontStyle('P', array('name' => 'Arial', 'size' => 11));
$phpWord->addFontStyle('PB', array('name' => 'Arial', 'size' => 11, 'bold' => true));

$section = $phpWord->addSection();

// Title
$section->addText('Rekomendasi Hosting Hyper Cloud Host', 'T1');
$section->addText('Untuk 3 Project Dashboard Laravel', 'T1');
$section->addTextBreak(1);

// Analysis
$section->addText('Analisis Kebutuhan Teknis Proyek:', 'T2');
$section->addText('• Framework: Laravel 12.0', 'P');
$section->addText('• Fitur Khusus: Integrasi Google Gemini AI, PDF Parsing, Excel/Word Generation', 'P');
$section->addText('• Kapasitas: 3 Project sekaligus', 'P');
$section->addTextBreak(1);

// Why Hyper Pro
$section->addText('Alasan Detail Mengapa Harus Hyper Pro (Sangat Direkomendasikan):', 'T2');
$section->addListItem('RAM 5GB (Kritikal): Menghindari error "Out of Memory" saat menjalankan proses AI dan manipulasi dokumen di 3 dashboard sekaligus.', 0, 'P');
$section->addListItem('Penyimpanan 20GB SSD: Memberikan ruang cukup untuk folder vendor/node_modules dari 3 project, file log, dan backup data.', 0, 'P');
$section->addListItem('CPU 3 Core: Memberikan responsivitas tinggi saat ada permintaan paralel di dashboard.', 0, 'P');
$section->addListItem('Harga Promo: Hanya sekitar Rp 41.667/bulan (Pembayaran Tahunan) dengan fitur Unlimited Addon Domain.', 0, 'P');
$section->addTextBreak(1);

// Why Hyper Starter
$section->addText('Alasan Jika Memilih Hyper Starter (Opsional):', 'T2');
$section->addListItem('Biaya Minimal: Hanya Rp 150rb/tahun, cocok untuk budget yang sangat ketat.', 0, 'P');
$section->addListItem('Tahap Development: Cukup jika dashboard hanya digunakan untuk testing internal dengan akses terbatas.', 0, 'P');
$section->addText('Risiko: Rentan terhadap Error 500/503 jika RAM melampaui 2GB dan storage 7GB cepat penuh oleh file log/backup.', 'P', array('italic' => true, 'color' => 'FF0000'));
$section->addTextBreak(1);

// Flash Sale Table
$section->addText('Perbandingan Paket Flash Sale:', 'T2');
$table = $section->addTable(array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80));
$table->addRow();
$table->addCell(2000)->addText('Fitur', 'PB');
$table->addCell(2000)->addText('Hyper Starter', 'PB');
$table->addCell(2000)->addText('Hyper Pro', 'PB');

$table->addRow();
$table->addCell(2000)->addText('Harga / Thn', 'P');
$table->addCell(2000)->addText('Rp 150.000', 'P');
$table->addCell(2000)->addText('Rp 500.000', 'P');

$table->addRow();
$table->addCell(2000)->addText('RAM', 'P');
$table->addCell(2000)->addText('2 GB', 'P');
$table->addCell(2000)->addText('5 GB', 'P');

$table->addRow();
$table->addCell(2000)->addText('CPU', 'P');
$table->addCell(2000)->addText('2 Core', 'P');
$table->addCell(2000)->addText('3 Core', 'P');

$table->addRow();
$table->addCell(2000)->addText('SSD Storage', 'P');
$table->addCell(2000)->addText('7 GB', 'P');
$table->addCell(2000)->addText('20 GB', 'P');

$section->addTextBreak(1);
$section->addText('Kesimpulan:', 'T2');
$section->addText('Untuk kestabilan jangka panjang dan kenyamanan operasional 3 dashboard Laravel, paket Hyper Pro adalah investasi terbaik Anda.', 'P');

// Save the document
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('Rekomendasi_Hosting_HyperCloud.docx');

echo "File created: Rekomendasi_Hosting_HyperCloud.docx\n";

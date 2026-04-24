<?php
// resize_icons.php
$source_image = __DIR__ . '/assets/img/Logo_PP_Matholi\'ul_Anwar.png';
$output_dir = __DIR__ . '/assets/icons/';

// Pastikan file gambar asli ada
if (!file_exists($source_image)) {
    // Coba nama file lain barangkali tanpa kutip tunggal
    $source_image = __DIR__ . '/assets/img/Logo_PP_Matholiul_Anwar.png';
    if (!file_exists($source_image)) {
        die("Error: Gambar sumber tidak ditemukan. Pastikan nama file dan foldernya benar.\n");
    }
}

$sizes = [
    72 => 'android-icon-72x72.png',
    96 => 'android-icon-96x96.png',
    144 => 'android-icon-144x144.png',
    192 => 'android-icon-192x192.png',
    512 => 'android-chrome-512x512.png'
];

// Load gambar asli
$src_img = imagecreatefrompng($source_image);
if (!$src_img) {
    die("Error: Tidak dapat membaca gambar sumber. Pastikan itu adalah file PNG yang valid.\n");
}

imagealphablending($src_img, true);
imagesavealpha($src_img, true);

$src_w = imagesx($src_img);
$src_h = imagesy($src_img);

echo "Membuat ikon PWA dari logo berukuran {$src_w}x{$src_h}...\n";

foreach ($sizes as $size => $filename) {
    $dst_img = imagecreatetruecolor($size, $size);
    
    // Pertahankan transparansi
    imagealphablending($dst_img, false);
    imagesavealpha($dst_img, true);
    $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
    imagefilledrectangle($dst_img, 0, 0, $size, $size, $transparent);
    
    // Gambar (resize)
    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $size, $size, $src_w, $src_h);
    
    // Simpan
    $output_path = $output_dir . $filename;
    imagepng($dst_img, $output_path);
    imagedestroy($dst_img);
    
    echo "✔ Berhasil membuat $filename\n";
}

imagedestroy($src_img);
echo "\nSemua ikon berhasil dibuat dan di-resize tanpa pecah!\n";
?>

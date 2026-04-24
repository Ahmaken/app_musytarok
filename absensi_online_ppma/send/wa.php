<?php
/**
 * WhatsApp API Wrapper untuk quizb.my.id
 * Simpan file ini dengan nama wa.php
 */

function kirimWA($number, $message) {
    // Pengaturan API
    $api_key = "TgoqAUYMP7UoowZe4DamgKt14oQm3l"; // Ganti dengan API Key Anda
    $sender  = "628133129223";   // Ganti dengan nomor pengirim yang terdaftar
    $baseUrl = "https://wa.quizb.my.id/send-message";

    // Menyiapkan parameter
    $params = [
        'api_key' => $api_key,
        'sender'  => $sender,
        'number'  => $number,
        'message' => $message
    ];

    // Membangun URL dengan query string (otomatis menghandle spasi/karakter khusus)
    $url = $baseUrl . "?" . http_build_query($params);

    // Inisialisasi cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Lewati verifikasi SSL jika diperlukan
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return "Error: " . $error;
    } else {
        return $response;
    }
}
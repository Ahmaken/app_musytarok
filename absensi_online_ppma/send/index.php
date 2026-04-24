<?php
include('wa.php');

$response = null;

// Logika ketika tombol Kirim ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_tujuan = $_POST['number'];
    $pesan = $_POST['message'];
    
    // Panggil fungsi dari wa.php
    $hasil = kirimWA($nomor_tujuan, $pesan);
    
    // Decode response jika API mengembalikan JSON
    $response = json_decode($hasil, true) ?: $hasil;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Sender - Hakimz Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-green-600 p-6 text-white">
            <h1 class="text-xl font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                WhatsApp Gateway
            </h1>
            <p class="text-green-100 text-sm mt-1">Hakimz Project - Multi Device API</p>
        </div>

        <form action="" method="POST" class="p-6 space-y-4">
            <div>
                <label for="number" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Tujuan</label>
                <input type="text" name="number" id="number" required 
                    placeholder="Contoh: 62888xxxxxxxx"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition duration-200">
                <p class="text-[10px] text-gray-400 mt-1 italic">*Gunakan format 62 tanpa tanda +</p>
            </div>

            <div>
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">Pesan</label>
                <textarea name="message" id="message" rows="4" required
                    placeholder="Tulis pesan Anda di sini..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition duration-200 resize-none"></textarea>
            </div>

            <button type="submit" 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg transform transition active:scale-95 flex items-center justify-center">
                <span>Kirim Pesan Sekarang</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>

        <div class="bg-gray-50 p-4 border-t border-gray-100 text-center">
            <span class="text-xs text-gray-500 uppercase tracking-widest">Powered by QuizB API</span>
        </div>
    </div>

    <?php if ($response): ?>
    <script>
        Swal.fire({
            title: 'Response API',
            text: '<?php echo is_array($response) ? json_encode($response) : addslashes($response); ?>',
            icon: 'info',
            confirmButtonColor: '#059669',
            confirmButtonText: 'Oke Sip!'
        });
    </script>
    <?php endif; ?>

</body>
</html>
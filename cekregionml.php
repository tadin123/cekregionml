<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = "";

// Jika form dikirim dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"], $_POST["server"])) {
    $uid = trim($_POST["id"]);
    $server = trim($_POST["server"]);

    if (!empty($uid) && !empty($server)) {
        // API Credentials dari Yanjiestore
        $apiId = "a6848499f740"; // Ganti dengan API ID kamu
        $apiKey = "9532ab1a1f8831bfbc01643e8da8d8d4094438c0"; // Ganti dengan API Key kamu
        $apiUrl = "https://yanjiestore.com/api/cekregion"; // URL API

        // Generate Signature
        $signature = md5($apiId . $apiKey);

        // Data yang dikirim ke API
        $postData = [
            "id" => $uid, 
            "server" => $server,
            "api_id" => $apiId,
            "api_key" => $apiKey,
            "signature" => $signature
        ];

        // Inisialisasi cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_TIMEOUT => 10
        ]);

        // Eksekusi request API
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Cek apakah request berhasil
        if ($curlError) {
            $response = json_encode(["status" => false, "msg" => "cURL Error: $curlError"]);
        } elseif ($httpCode !== 200) {
            $response = json_encode(["status" => false, "msg" => "Gagal menghubungi API (HTTP $httpCode)"]);
        } else {
            $response = $result;
        }
    } else {
        $response = json_encode(["status" => false, "msg" => "User ID dan Server ID tidak boleh kosong!"]);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Region Mobile Legends</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 400px; margin-top: 50px; }
        .btn-signin { background: #007bff; color: #fff; border: none; padding: 10px; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Cek Region Mobile Legends</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="uid" class="form-label">User ID</label>
                <input type="number" name="id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="server" class="form-label">Server ID</label>
                <input type="number" name="server" class="form-control" required>
            </div>
            <button type="submit" class="btn-signin">Cek Region</button>
        </form>

        <?php if ($response): ?>
            <div class="mt-4">
                <h4>Hasil:</h4>
                <pre><?php echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8'); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

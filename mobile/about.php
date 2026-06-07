<?php
// mobile/about.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Info - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>Tentang Aplikasi</h1>
        <a href="../about.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2 style="font-size: 1.2rem;">PediaDose (Mobile)</h2>
            <p style="margin-top: 10px; font-size: 0.9rem; line-height: 1.5; color: var(--text-dark);">
                PediaDose adalah aplikasi asisten pediatrik komprehensif yang dirancang untuk memverifikasi dan menghitung kewajaran dosis obat anak.
            </p>
            <p style="margin-top: 10px; font-size: 0.9rem; line-height: 1.5; color: var(--text-dark);">
                <strong>Fitur Utama:</strong>
                <ul style="margin-left: 15px; margin-top: 5px; font-size: 0.85rem; line-height: 1.5; color: var(--text-dark);">
                    <li><strong>Cek Dosis AI:</strong> Memverifikasi keamanan resep dengan fitur Smart Input.</li>
                    <li><strong>Kalkulator:</strong> Menghitung konversi dosis anak (Young, Dilling, Fried, Clark) beserta takaran sediaan.</li>
                </ul>
            </p>
        </div>

        <div class="card" style="border-top: 5px solid var(--alert-red-text);">
            <h2 style="color: var(--alert-red-text); font-size: 1.1rem;">Disclaimer Medis!</h2>
            <div class="alert alert-danger" style="margin-top: 10px; padding: 10px; font-size: 0.85rem;">
                <strong>PERHATIAN: APLIKASI INI HANYA UNTUK TUJUAN EDUKASI DAN DEMONSTRASI.</strong>
            </div>
            <ul style="margin-left: 20px; margin-top: 10px; font-size: 0.85rem; line-height: 1.5; color: var(--text-dark);">
                <li>Aplikasi ini <strong>bukan</strong> perangkat medis.</li>
                <li>Sistem ini <strong>tidak</strong> boleh digunakan sebagai pengganti nasihat medis.</li>
                <li>Label "AI Confidence Score" hanyalah simulasi (angka acak).</li>
            </ul>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item">
            <div class="nav-icon">🏠</div>
            Home
        </a>
        <a href="check.php" class="nav-item">
            <div class="nav-icon">🩺</div>
            Cek
        </a>
        <a href="kalkulator.php" class="nav-item">
            <div class="nav-icon">🧮</div>
            Kalkulator
        </a>
        <a href="drugs.php" class="nav-item">
            <div class="nav-icon">💊</div>
            Obat
        </a>
        <a href="history.php" class="nav-item">
            <div class="nav-icon">📋</div>
            Riwayat
        </a>
    </div>

</body>
</html>

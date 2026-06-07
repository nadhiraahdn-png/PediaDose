<?php
// mobile/index.php
require_once '../db.php';
// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM drugs");
$totalObat = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks");
$totalCek = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Overdosing'");
$totalOverdosis = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PediaDose Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>PediaDose</h1>
        <a href="../index.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content">
        <div class="card" style="background: linear-gradient(135deg, var(--primary-blue), #00a8a8); color: white; position: relative; overflow: hidden; padding: 25px 20px; border: none; margin-bottom: 20px; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,128,128,0.2);">
            <!-- Abstract background shapes -->
            <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -20px; left: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; gap: 15px; align-items: center; flex-direction: column; text-align: center; position: relative; z-index: 2;">
                <div>
                    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">Selamat Datang di PediaDose</h2>
                    <p style="font-size: 0.95rem; line-height: 1.5; opacity: 0.95; font-weight: 400;">
                        Sistem simulasi cerdas pengecekan dosis pediatrik berdasarkan parameter berat badan klinis & kalkulator konversi takaran.
                    </p>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px; align-items: center; justify-content: center;">
                    <div class="float-1"><img src="../assets/img/pill.png" alt="Pill" style="width: 55px; height: 55px; border-radius: 50%; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); transform: rotate(-15deg); object-fit: cover;"></div>
                    <div class="float-2" style="z-index: 3;"><img src="../assets/img/syrup.png" alt="Syrup" style="width: 70px; height: 70px; border-radius: 50%; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); object-fit: cover;"></div>
                    <div class="float-3"><img src="../assets/img/blister.png" alt="Blister" style="width: 55px; height: 55px; border-radius: 50%; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); transform: rotate(15deg); object-fit: cover;"></div>
                </div>
            </div>
        </div>

        <div class="grid-2" style="gap: 15px;">
            <div class="stat-card" style="background: linear-gradient(135deg, #e0f7fa, #b2ebf2); border: none; border-radius: 12px; padding: 15px; position: relative; overflow: hidden;">
                <h3 style="color: #00838f; font-size: 0.9rem; margin-bottom: 5px; font-weight: 600; position: relative; z-index: 2;">Database Obat</h3>
                <div class="value" style="color: #006064; font-size: 1.8rem; font-weight: 800; position: relative; z-index: 2;"><?= $totalObat ?></div>
                <div style="font-size: 3rem; position: absolute; right: -5px; bottom: -10px; opacity: 0.3; z-index: 1;">💊</div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border: none; border-radius: 12px; padding: 15px; position: relative; overflow: hidden;">
                <h3 style="color: #2e7d32; font-size: 0.9rem; margin-bottom: 5px; font-weight: 600; position: relative; z-index: 2;">Total Cek</h3>
                <div class="value" style="color: #1b5e20; font-size: 1.8rem; font-weight: 800; position: relative; z-index: 2;"><?= $totalCek ?></div>
                <div style="font-size: 3rem; position: absolute; right: -5px; bottom: -10px; opacity: 0.3; z-index: 1;">🩺</div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #ffebee, #ffcdd2); border: none; border-radius: 12px; padding: 15px; position: relative; overflow: hidden;">
                <h3 style="color: #c62828; font-size: 0.9rem; margin-bottom: 5px; font-weight: 600; position: relative; z-index: 2;">Overdosing</h3>
                <div class="value" style="color: #b71c1c; font-size: 1.8rem; font-weight: 800; position: relative; z-index: 2;"><?= $totalOverdosis ?></div>
                <div style="font-size: 3rem; position: absolute; right: -5px; bottom: -10px; opacity: 0.3; z-index: 1;">⚠️</div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #fff8e1, #ffecb3); border: none; border-radius: 12px; padding: 15px; position: relative; overflow: hidden;">
                <h3 style="color: #f57f17; font-size: 0.9rem; margin-bottom: 5px; font-weight: 600; position: relative; z-index: 2;">Underdosing</h3>
                <?php
                    // Get underdosing specifically for mobile which might have been missing earlier
                    $stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Underdosing'");
                    $totalUnderdosis = $stmt->fetchColumn();
                ?>
                <div class="value" style="color: #e65100; font-size: 1.8rem; font-weight: 800; position: relative; z-index: 2;"><?= $totalUnderdosis ?></div>
                <div style="font-size: 3rem; position: absolute; right: -5px; bottom: -10px; opacity: 0.3; z-index: 1;">📉</div>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(to right, #fff5f5, #ffebee); border: 2px dashed #ef9a9a; border-radius: 12px; margin-top: 20px; padding: 15px;">
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <div style="font-size: 2rem; background: #ffebee; padding: 5px; border-radius: 10px; box-shadow: 0 4px 8px rgba(211,47,47,0.1);">🛑</div>
                <div>
                    <h3 style="color: #c62828; font-size: 1.1rem; margin-bottom: 5px;">Peringatan Medis</h3>
                    <p style="color: #b71c1c; line-height: 1.5; font-size: 0.85rem;">
                        Aplikasi ini hanyalah <strong>simulasi edukasi</strong>. Segala hasil "AI Score" didapat dari algoritma simulasi untuk demonstrasi visual. <br><br>
                        Aplikasi ini <strong>tidak boleh</strong> digunakan sebagai pengganti keputusan klinis tenaga medis (Dokter/Apoteker).
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item active">
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

    <script src="../assets/js/script.js"></script>
</body>
</html>

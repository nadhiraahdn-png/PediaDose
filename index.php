<?php
require_once 'db.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM drugs");
$totalObat = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks");
$totalCek = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Overdosing'");
$totalOverdosis = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Underdosing'");
$totalUnderdosis = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM checks WHERE status = 'Normal'");
$totalNormal = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PediaDose</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>PediaDose</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="check.php">Cek Dosis</a></li>
            <li><a href="kalkulator.php">Kalkulator Dosis</a></li>
            <li><a href="drugs.php">Data Obat</a></li>
            <li><a href="history.php">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/index.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">Dashboard</h1>
        
        <div class="card" style="background: linear-gradient(135deg, var(--primary-blue), #00a8a8); color: white; position: relative; overflow: hidden; padding: 40px; border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,128,128,0.2);">
            <!-- Abstract background shapes for fun -->
            <div style="position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -80px; right: 150px; width: 150px; height: 150px; background: rgba(255,255,255,0.15); border-radius: 50%;"></div>
            <div style="position: absolute; top: 20px; left: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap; position: relative; z-index: 2;">
                <div style="flex: 1; min-width: 250px;">
                    <h2 style="font-size: 2.2rem; margin-bottom: 15px;">Selamat Datang di PediaDose</h2>
                    <p style="font-size: 1.1rem; line-height: 1.6; opacity: 0.95; font-weight: 400;">
                        Sistem simulasi cerdas pengecekan kewajaran dosis obat pediatrik berdasarkan parameter berat badan dan panduan klinis dasar, dilengkapi kalkulator konversi takaran.
                    </p>
                </div>
                <div style="display: flex; gap: 15px; align-items: center; justify-content: center; min-width: 200px; padding-right: 20px;">
                    <div class="float-1"><img src="assets/img/pill.png" alt="Pill" style="width: 75px; height: 75px; border-radius: 50%; border: 4px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.15); transform: rotate(-15deg); object-fit: cover;"></div>
                    <div class="float-2" style="z-index: 3;"><img src="assets/img/syrup.png" alt="Syrup" style="width: 100px; height: 100px; border-radius: 50%; border: 5px solid white; box-shadow: 0 15px 30px rgba(0,0,0,0.2); object-fit: cover;"></div>
                    <div class="float-3"><img src="assets/img/blister.png" alt="Blister" style="width: 75px; height: 75px; border-radius: 50%; border: 4px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.15); transform: rotate(15deg); object-fit: cover;"></div>
                </div>
            </div>
        </div>

        <div class="dashboard-grid" style="gap: 20px; margin-top: 25px;">
            <div class="stat-card" style="background: linear-gradient(135deg, #e0f7fa, #b2ebf2); border: none; border-radius: 16px; padding: 25px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: #00838f; font-size: 1.05rem; margin-bottom: 5px; font-weight: 600;">Total Database Obat</h3>
                    <div class="value" style="color: #006064; font-size: 2.5rem; font-weight: 800;"><?= $totalObat ?></div>
                </div>
                <div style="font-size: 3rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">💊</div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border: none; border-radius: 16px; padding: 25px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: #2e7d32; font-size: 1.05rem; margin-bottom: 5px; font-weight: 600;">Total Pengecekan</h3>
                    <div class="value" style="color: #1b5e20; font-size: 2.5rem; font-weight: 800;"><?= $totalCek ?></div>
                </div>
                <div style="font-size: 3rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">🩺</div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #ffebee, #ffcdd2); border: none; border-radius: 16px; padding: 25px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: #c62828; font-size: 1.05rem; margin-bottom: 5px; font-weight: 600;">Kasus Overdosing</h3>
                    <div class="value" style="color: #b71c1c; font-size: 2.5rem; font-weight: 800;"><?= $totalOverdosis ?></div>
                </div>
                <div style="font-size: 3rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">⚠️</div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #fff8e1, #ffecb3); border: none; border-radius: 16px; padding: 25px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: #f57f17; font-size: 1.05rem; margin-bottom: 5px; font-weight: 600;">Kasus Underdosing</h3>
                    <div class="value" style="color: #e65100; font-size: 2.5rem; font-weight: 800;"><?= $totalUnderdosis ?></div>
                </div>
                <div style="font-size: 3rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">📉</div>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(to right, #fff5f5, #ffebee); border: 2px dashed #ef9a9a; border-radius: 16px; margin-top: 30px; padding: 25px; position: relative;">
            <div style="display: flex; align-items: flex-start; gap: 20px;">
                <div style="font-size: 2.5rem; background: #ffebee; padding: 10px; border-radius: 12px; box-shadow: 0 4px 10px rgba(211,47,47,0.1);">🛑</div>
                <div>
                    <h3 style="color: #c62828; font-size: 1.3rem; margin-bottom: 10px;">Peringatan Medis</h3>
                    <p style="color: #b71c1c; line-height: 1.6; font-size: 1.05rem;">
                        Aplikasi ini hanyalah <strong>simulasi edukasi</strong>. Segala hasil perhitungan dan label "AI Confidence Score" dibuat berdasarkan <em>rule-based</em> sederhana dan algoritma simulasi (angka acak) untuk demonstrasi visual. <br><br>
                        Aplikasi ini <strong>tidak boleh</strong> digunakan sebagai pengganti keputusan klinis dari tenaga medis profesional seperti Dokter atau Apoteker.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

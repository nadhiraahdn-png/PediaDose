<?php
// mobile/kalkulator.php
require_once '../config/db.php';

$result = null;

// Fetch Calculator History
$stmtCalc = $pdo->query("SELECT * FROM kalkulator_history ORDER BY waktu_kalkulasi DESC");
$calc_histories = $stmtCalc->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dosis_dewasa = (float)$_POST['dosis_dewasa'];
    $rumus = htmlspecialchars($_POST['rumus']);
    $umur_tahun = (float)($_POST['umur_tahun'] ?? 0);
    $umur_bulan = (float)($_POST['umur_bulan'] ?? 0);
    $berat_badan = (float)($_POST['berat_badan'] ?? 0);
    $sediaan = htmlspecialchars($_POST['sediaan']);
    $kekuatan_mg = (float)($_POST['kekuatan_mg'] ?? 1);
    $kekuatan_ml = (float)($_POST['kekuatan_ml'] ?? 1);

    if ($kekuatan_mg <= 0) $kekuatan_mg = 1;
    if ($kekuatan_ml <= 0) $kekuatan_ml = 1;

    $dosis_anak_mg = 0;
    $rumus_label = "";
    $error = "";

    if ($rumus === 'young') {
        if ($umur_tahun <= 0) $error = "Isi umur tahun (Rumus Young).";
        $dosis_anak_mg = ($umur_tahun / ($umur_tahun + 12)) * $dosis_dewasa;
        $rumus_label = "Young (< 12 thn)";
        $rincian_perhitungan = "({$umur_tahun} / ({$umur_tahun} + 12)) &times; {$dosis_dewasa} mg = " . round($dosis_anak_mg, 2) . " mg";
    } elseif ($rumus === 'dilling') {
        if ($umur_tahun <= 0) $error = "Isi umur tahun (Rumus Dilling).";
        $dosis_anak_mg = ($umur_tahun / 20) * $dosis_dewasa;
        $rumus_label = "Dilling (> 8 thn)";
        $rincian_perhitungan = "({$umur_tahun} / 20) &times; {$dosis_dewasa} mg = " . round($dosis_anak_mg, 2) . " mg";
    } elseif ($rumus === 'fried') {
        if ($umur_bulan <= 0) $error = "Isi umur bulan (Rumus Fried).";
        $dosis_anak_mg = ($umur_bulan / 150) * $dosis_dewasa;
        $rumus_label = "Fried (< 1 thn)";
        $rincian_perhitungan = "({$umur_bulan} / 150) &times; {$dosis_dewasa} mg = " . round($dosis_anak_mg, 2) . " mg";
    } elseif ($rumus === 'clark') {
        if ($berat_badan <= 0) $error = "Isi berat badan (Rumus Clark).";
        $dosis_anak_mg = ($berat_badan / 70) * $dosis_dewasa;
        $rumus_label = "Clark (BB)";
        $rincian_perhitungan = "({$berat_badan} / 70) &times; {$dosis_dewasa} mg = " . round($dosis_anak_mg, 2) . " mg";
    }

    if (empty($error)) {
        $konversi_label = "";
        $dosis_anak_mg_format = round($dosis_anak_mg, 1);

        if ($sediaan === 'tablet') {
            $jumlah_tablet = $dosis_anak_mg / $kekuatan_mg;
            $jumlah_tablet_bulat = round($jumlah_tablet, 1);
            $konversi_label = "<strong>{$jumlah_tablet_bulat} Tablet/Puyer</strong>";
        } elseif ($sediaan === 'sirup') {
            $volume_ml = $dosis_anak_mg / ($kekuatan_mg / $kekuatan_ml);
            $volume_ml_bulat = round($volume_ml, 0);
            
            if ($volume_ml_bulat >= 15 && $volume_ml_bulat % 15 == 0) {
                $sendok = $volume_ml_bulat / 15;
                $takar = " ({$sendok} C)";
            } elseif ($volume_ml_bulat >= 5 && $volume_ml_bulat % 5 == 0) {
                $sendok = $volume_ml_bulat / 5;
                $takar = " ({$sendok} Cth)";
            } else {
                $takar = "";
            }

            $konversi_label = "<strong>{$volume_ml_bulat} ml{$takar}</strong>";
        } elseif ($sediaan === 'drop') {
            $volume_ml = $dosis_anak_mg / ($kekuatan_mg / $kekuatan_ml);
            $volume_ml_format = round($volume_ml, 1);
            $jumlah_tetes = round($volume_ml * 20, 0);
            $konversi_label = "<strong>{$volume_ml_format} ml (~{$jumlah_tetes} tetes)</strong>";
        }

        $result = [
            'dosis_anak_mg' => $dosis_anak_mg_format,
            'rumus_label' => $rumus_label,
            'konversi_label' => $konversi_label,
            'rincian_perhitungan' => $rincian_perhitungan ?? ''
        ];

        // Simpan ke riwayat kalkulator
        $parameter_nilai = "";
        if ($rumus === 'young' || $rumus === 'dilling') $parameter_nilai = $umur_tahun . " Tahun";
        elseif ($rumus === 'fried') $parameter_nilai = $umur_bulan . " Bulan";
        elseif ($rumus === 'clark') $parameter_nilai = $berat_badan . " kg";

        $stmt = $pdo->prepare("INSERT INTO kalkulator_history (dosis_dewasa_mg, rumus, parameter_nilai, sediaan, hasil_dosis_anak_mg, hasil_konversi_teks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$dosis_dewasa, $rumus_label, $parameter_nilai, $sediaan, $dosis_anak_mg_format, $konversi_label]);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kalkulator - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
    <style>
        .bottom-nav {
            justify-content: space-between;
        }
        .nav-item {
            flex: 1;
            font-size: 0.7rem;
            padding: 8px 2px;
        }
        .nav-icon {
            font-size: 1.2rem;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

    <div class="mobile-topbar">
        <h1>Kalkulator Konversi</h1>
        <a href="../kalkulator.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content" style="padding-bottom: 80px;">
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger" style="margin-bottom: 15px; padding: 10px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="card" id="resultCard" style="border-top: 5px solid var(--primary-blue);">
                <h2 style="font-size: 1.1rem;">Hasil Kalkulasi</h2>
                <div class="alert alert-info" style="margin-top: 10px; padding: 10px;">
                    <p style="font-size: 0.9rem; margin-bottom: 5px;">Metode: <strong><?= $result['rumus_label'] ?></strong></p>
                    <p style="font-size: 0.85rem; margin-bottom: 5px; color: var(--primary-blue);">Rincian: <i><?= $result['rincian_perhitungan'] ?></i></p>
                    <p style="font-size: 1.1rem; margin-bottom: 5px;">Dosis Anak: <strong><?= $result['dosis_anak_mg'] ?> mg</strong></p>
                    <p style="font-size: 1.1rem;">Takaran: <?= $result['konversi_label'] ?></p>
                </div>
                
                <div style="margin-top: 15px; display: flex; gap: 10px; flex-direction: column;">
                    <button type="button" onclick="showInputForm()" class="btn btn-secondary" style="background: var(--text-muted); color: white;">Kembali ke Input</button>
                    <a href="kalkulator.php" class="btn btn-primary" style="text-align: center;">Reset Baru</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="card" id="formCard" <?= $result ? 'style="display:none;"' : '' ?>>
            <form action="kalkulator.php" method="POST" id="kalkulatorForm">
                
                <div class="form-group">
                    <label>Dosis Dewasa (mg)</label>
                    <input type="number" step="0.1" name="dosis_dewasa" class="form-control" required placeholder="Cth: 500" value="<?= isset($_POST['dosis_dewasa']) ? htmlspecialchars($_POST['dosis_dewasa']) : '' ?>">
                </div>

                <div class="form-group">
                    <label>Rumus Pediatrik</label>
                    <select id="rumus" name="rumus" class="form-control" required onchange="updateParameterInput()">
                        <option value="">-- Pilih --</option>
                        <option value="young" <?= (isset($_POST['rumus']) && $_POST['rumus'] == 'young') ? 'selected' : '' ?>>Young (< 12 Thn)</option>
                        <option value="dilling" <?= (isset($_POST['rumus']) && $_POST['rumus'] == 'dilling') ? 'selected' : '' ?>>Dilling (> 8 Thn)</option>
                        <option value="fried" <?= (isset($_POST['rumus']) && $_POST['rumus'] == 'fried') ? 'selected' : '' ?>>Fried (< 1 Thn)</option>
                        <option value="clark" <?= (isset($_POST['rumus']) && $_POST['rumus'] == 'clark') ? 'selected' : '' ?>>Clark (Berat Badan)</option>
                    </select>
                </div>

                <div id="param-container" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <div class="form-group" id="param-umur-tahun" style="display: none; margin-bottom: 0;">
                        <label>Umur (Tahun)</label>
                        <input type="number" id="umur_tahun" name="umur_tahun" class="form-control" min="1" placeholder="Cth: 6" value="<?= isset($_POST['umur_tahun']) ? htmlspecialchars($_POST['umur_tahun']) : '' ?>">
                    </div>
                    <div class="form-group" id="param-umur-bulan" style="display: none; margin-bottom: 0;">
                        <label>Umur (Bulan)</label>
                        <input type="number" id="umur_bulan" name="umur_bulan" class="form-control" min="1" placeholder="Cth: 8" value="<?= isset($_POST['umur_bulan']) ? htmlspecialchars($_POST['umur_bulan']) : '' ?>">
                    </div>
                    <div class="form-group" id="param-berat-badan" style="display: none; margin-bottom: 0;">
                        <label>Berat Badan (kg)</label>
                        <input type="number" step="0.1" id="berat_badan" name="berat_badan" class="form-control" min="1" placeholder="Cth: 15" value="<?= isset($_POST['berat_badan']) ? htmlspecialchars($_POST['berat_badan']) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Bentuk Sediaan</label>
                    <select id="sediaan" name="sediaan" class="form-control" required onchange="updateSediaanInput()">
                        <option value="">-- Pilih --</option>
                        <option value="tablet" <?= (isset($_POST['sediaan']) && $_POST['sediaan'] == 'tablet') ? 'selected' : '' ?>>Tablet / Puyer</option>
                        <option value="sirup" <?= (isset($_POST['sediaan']) && $_POST['sediaan'] == 'sirup') ? 'selected' : '' ?>>Sirup</option>
                        <option value="drop" <?= (isset($_POST['sediaan']) && $_POST['sediaan'] == 'drop') ? 'selected' : '' ?>>Drop</option>
                    </select>
                </div>

                <div id="kekuatan-container" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <div class="form-group" id="kekuatan-tablet" style="display: none; margin-bottom: 0;">
                        <label>Kekuatan per Tablet (mg)</label>
                        <input type="number" step="0.1" id="kekuatan_tablet_mg" name="kekuatan_mg" class="form-control" placeholder="Cth: 500" value="<?= isset($_POST['sediaan']) && $_POST['sediaan']=='tablet' && isset($_POST['kekuatan_mg']) ? htmlspecialchars($_POST['kekuatan_mg']) : '' ?>">
                    </div>
                    
                    <div class="form-group" id="kekuatan-sirup" style="display: none; margin-bottom: 0;">
                        <label>Kekuatan Cairan</label>
                        <div style="display: flex; gap: 5px; align-items: center;">
                            <input type="number" step="0.1" id="kekuatan_sirup_mg" name="kekuatan_mg" class="form-control" placeholder="mg" style="flex: 1;" value="<?= (isset($_POST['sediaan']) && ($_POST['sediaan']=='sirup' || $_POST['sediaan']=='drop') && isset($_POST['kekuatan_mg'])) ? htmlspecialchars($_POST['kekuatan_mg']) : '' ?>">
                            <span>/</span>
                            <input type="number" step="0.1" id="kekuatan_sirup_ml" name="kekuatan_ml" class="form-control" placeholder="ml" style="flex: 1;" value="<?= isset($_POST['kekuatan_ml']) ? htmlspecialchars($_POST['kekuatan_ml']) : '' ?>">
                            <span>ml</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 5px;">Hitung Konversi</button>
            </form>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h2 style="font-size: 1.1rem; margin-bottom: 10px;">Riwayat Kalkulator</h2>
            <div style="display:flex; flex-direction: column; gap: 10px;">
                <?php foreach($calc_histories as $calc): ?>
                    <div class="clickable-row" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 10px;" onclick="showCalcDetail(<?= htmlspecialchars(json_encode($calc), ENT_QUOTES, 'UTF-8') ?>)">
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">
                            <?= date('d M Y, H:i', strtotime($calc['waktu_kalkulasi'])) ?>
                        </div>
                        <div style="font-size: 0.9rem;">
                            <strong>Dewasa:</strong> <?= $calc['dosis_dewasa_mg'] ?> mg<br>
                            <strong>Metode:</strong> <?= $calc['rumus'] ?> (<?= $calc['parameter_nilai'] ?>)<br>
                            <strong>Anak:</strong> <span style="color: var(--primary-blue); font-weight: bold;"><?= $calc['hasil_dosis_anak_mg'] ?> mg</span><br>
                            <strong>Sediaan:</strong> <?= $calc['hasil_konversi_teks'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($calc_histories)): ?>
                    <p style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">Belum ada riwayat kalkulator.</p>
                <?php endif; ?>
            </div>
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
        <a href="kalkulator.php" class="nav-item active">
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

    <!-- Modal Detail Kalkulator -->
    <div id="calcDetailModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('calcDetailModal')">&times;</span>
            <h2 style="color: var(--primary-blue); margin-bottom: 5px; font-size: 1.1rem;">Detail Kalkulasi Dosis</h2>
            <p style="color: var(--text-muted); font-size: 0.8rem;" id="modalCalcWaktu"></p>
            
            <div class="detail-grid" style="font-size: 0.9rem;">
                <div><strong>Dosis Dewasa</strong></div>
                <div id="modalCalcDewasa"></div>
                
                <div><strong>Metode/Rumus</strong></div>
                <div id="modalCalcRumus"></div>

                <div><strong>Parameter</strong></div>
                <div id="modalCalcParameter"></div>
                
                <div><strong>Bentuk Sediaan</strong></div>
                <div id="modalCalcSediaan" style="text-transform: capitalize;"></div>
                
                <div><strong>Dosis Anak</strong></div>
                <div id="modalCalcAnak" style="font-weight: bold; color: var(--primary-blue);"></div>
                
                <div><strong>Takaran</strong></div>
                <div id="modalCalcTakaran"></div>
            </div>
        </div>
    </div>

    <script>
        function showCalcDetail(data) {
            document.getElementById('modalCalcWaktu').innerText = data.waktu_kalkulasi;
            document.getElementById('modalCalcDewasa').innerText = data.dosis_dewasa_mg + " mg";
            document.getElementById('modalCalcRumus').innerText = data.rumus;
            document.getElementById('modalCalcParameter').innerText = data.parameter_nilai;
            document.getElementById('modalCalcSediaan').innerText = data.sediaan;
            document.getElementById('modalCalcAnak').innerText = data.hasil_dosis_anak_mg + " mg";
            document.getElementById('modalCalcTakaran').innerHTML = data.hasil_konversi_teks;
            
            document.getElementById('calcDetailModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = "none";
            }
        }

        function showInputForm() {
            document.getElementById('resultCard').style.display = 'none';
            document.getElementById('formCard').style.display = 'block';
        }

        function updateParameterInput() {
            const rumus = document.getElementById('rumus').value;
            const container = document.getElementById('param-container');
            const umurTahun = document.getElementById('param-umur-tahun');
            const umurBulan = document.getElementById('param-umur-bulan');
            const beratBadan = document.getElementById('param-berat-badan');

            umurTahun.style.display = 'none';
            umurBulan.style.display = 'none';
            beratBadan.style.display = 'none';

            if (rumus === 'young' || rumus === 'dilling') {
                container.style.display = 'block';
                umurTahun.style.display = 'block';
            } else if (rumus === 'fried') {
                container.style.display = 'block';
                umurBulan.style.display = 'block';
            } else if (rumus === 'clark') {
                container.style.display = 'block';
                beratBadan.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        function updateSediaanInput() {
            const sediaan = document.getElementById('sediaan').value;
            const container = document.getElementById('kekuatan-container');
            const tablet = document.getElementById('kekuatan-tablet');
            const sirup = document.getElementById('kekuatan-sirup');
            
            const mgInputTablet = document.getElementById('kekuatan_tablet_mg');
            const mgInputSirup = document.getElementById('kekuatan_sirup_mg');
            const mlInputSirup = document.getElementById('kekuatan_sirup_ml');

            tablet.style.display = 'none';
            sirup.style.display = 'none';
            
            mgInputTablet.removeAttribute('name');
            mgInputSirup.removeAttribute('name');
            mlInputSirup.removeAttribute('name');

            if (sediaan === 'tablet') {
                container.style.display = 'block';
                tablet.style.display = 'block';
                mgInputTablet.setAttribute('name', 'kekuatan_mg');
            } else if (sediaan === 'sirup' || sediaan === 'drop') {
                container.style.display = 'block';
                sirup.style.display = 'block';
                mgInputSirup.setAttribute('name', 'kekuatan_mg');
                mlInputSirup.setAttribute('name', 'kekuatan_ml');
            } else {
                container.style.display = 'none';
            }
        }

        window.onload = function() {
            updateParameterInput();
            updateSediaanInput();
        };
    </script>
</body>
</html>

<?php
// history.php
require_once 'config/db.php';

// Fetch All History (Checks)
$stmt = $pdo->query("SELECT * FROM checks ORDER BY waktu_cek DESC");
$histories = $stmt->fetchAll();

// Fetch All History (Checks)
$stmt = $pdo->query("SELECT * FROM checks ORDER BY waktu_cek DESC");
$histories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Cek - PediaDose</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>PediaDose</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="check.php">Cek Dosis</a></li>
            <li><a href="kalkulator.php">Kalkulator Dosis</a></li>
            <li><a href="drugs.php">Data Obat</a></li>
            <li><a href="history.php" class="active">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/history.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 PediaDose</p>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Riwayat Aktivitas</h1>

        <div class="card" id="tab-cek">
            <h2 style="margin-bottom: 15px; font-size: 1.2rem;">Riwayat Pengecekan Dosis</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu Cek</th>
                            <th>Pasien</th>
                            <th>BB (kg)</th>
                            <th>Obat</th>
                            <th>Dosis Harian</th>
                            <th>Status Simulasi</th>
                            <th>AI Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($histories as $history): ?>
                            <?php
                                $badgeClass = 'badge-success';
                                if ($history['status'] == 'Overdosing') $badgeClass = 'badge-danger';
                                if ($history['status'] == 'Underdosing') $badgeClass = 'badge-warning';
                            ?>
                        <tr class="clickable-row" onclick="showCheckDetail(<?= htmlspecialchars(json_encode($history), ENT_QUOTES, 'UTF-8') ?>)">
                            <td><?= date('d M Y, H:i', strtotime($history['waktu_cek'])) ?></td>
                            <?php
                                $years = floor($history['umur_bulan'] / 12);
                                $months = $history['umur_bulan'] % 12;
                                $ageStr = '';
                                if ($years > 0) $ageStr .= $years . ' thn ';
                                if ($months > 0 || $years == 0) $ageStr .= $months . ' bln';
                            ?>
                            <td><?= $history['nama_pasien'] ?> <br><small class="text-muted">(<?= trim($ageStr) ?>)</small></td>
                            <td><?= $history['berat_badan_kg'] ?></td>
                            <td><?= $history['nama_obat'] ?></td>
                            <td><?= $history['total_dosis_harian_mg'] ?> mg</td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $history['status'] ?></span></td>
                            <td><strong><?= $history['ai_confidence_score'] ?>%</strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($histories)): ?>
                        <tr><td colspan="7" style="text-align: center;">Belum ada riwayat pengecekan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Detail Cek Dosis -->
    <div id="checkDetailModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('checkDetailModal')">&times;</span>
            <h2 style="color: var(--primary-blue); margin-bottom: 5px;">Detail Pengecekan Dosis</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;" id="modalWaktuCek"></p>
            
            <div class="detail-grid">
                <div><strong>Nama Pasien</strong></div>
                <div id="modalNamaPasien"></div>
                
                <div><strong>Umur / BB</strong></div>
                <div id="modalUmurBB"></div>

                <div><strong>Diagnosis</strong></div>
                <div id="modalDiagnosis"></div>
                
                <div><strong>Nama Obat</strong></div>
                <div id="modalNamaObat"></div>
                
                <div><strong>Dosis per Kali</strong></div>
                <div id="modalDosisPerKali"></div>
                
                <div><strong>Frekuensi</strong></div>
                <div id="modalFrekuensi"></div>
                
                <div><strong>Total Harian</strong></div>
                <div id="modalTotalHarian"></div>
                
                <div><strong>Status Simulasi</strong></div>
                <div><span id="modalStatus" class="badge"></span></div>
                
                <div><strong>AI Score</strong></div>
                <div id="modalAiScore" style="font-weight: bold;"></div>
            </div>
        </div>
    </div>

    <script>
        function showCheckDetail(data) {
            document.getElementById('modalWaktuCek').innerText = "Waktu Cek: " + data.waktu_cek;
            document.getElementById('modalNamaPasien').innerText = data.nama_pasien;
            
            let years = Math.floor(data.umur_bulan / 12);
            let months = data.umur_bulan % 12;
            let ageStr = (years > 0 ? years + " thn " : "") + (months > 0 || years == 0 ? months + " bln" : "");
            
            document.getElementById('modalUmurBB').innerText = ageStr + " / " + data.berat_badan_kg + " kg";
            document.getElementById('modalDiagnosis').innerText = data.diagnosis ? data.diagnosis : "-";
            document.getElementById('modalNamaObat').innerText = data.nama_obat;
            document.getElementById('modalDosisPerKali').innerText = data.dosis_per_pemberian_mg + " mg";
            document.getElementById('modalFrekuensi').innerText = data.frekuensi_per_hari + "x sehari";
            document.getElementById('modalTotalHarian').innerText = data.total_dosis_harian_mg + " mg/hari";
            
            let statusBadge = document.getElementById('modalStatus');
            statusBadge.innerText = data.status;
            statusBadge.className = 'badge'; // reset
            if (data.status === 'Normal') statusBadge.classList.add('badge-success');
            else if (data.status === 'Overdosing') statusBadge.classList.add('badge-danger');
            else statusBadge.classList.add('badge-warning');
            
            document.getElementById('modalAiScore').innerText = data.ai_confidence_score + "%";
            
            document.getElementById('checkDetailModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>

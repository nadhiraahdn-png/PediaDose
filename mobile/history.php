<?php
// mobile/history.php
require_once '../db.php';

$stmt = $pdo->query("SELECT * FROM checks ORDER BY waktu_cek DESC");
$histories = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>Riwayat Aktivitas</h1>
        <a href="../history.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content" style="padding-bottom: 80px;">
        <div>
        <?php foreach($histories as $history): ?>
            <?php
                $badgeClass = 'var(--alert-green)';
                $textColor = 'var(--alert-green-text)';
                if ($history['status'] == 'Overdosing') { $badgeClass = 'var(--alert-red)'; $textColor = 'var(--alert-red-text)'; }
                if ($history['status'] == 'Underdosing') { $badgeClass = 'var(--alert-yellow)'; $textColor = 'var(--alert-yellow-text)'; }

                $years = floor($history['umur_bulan'] / 12);
                $months = $history['umur_bulan'] % 12;
                $ageStr = '';
                if ($years > 0) $ageStr .= $years . 'thn ';
                if ($months > 0 || $years == 0) $ageStr .= $months . 'bln';
            ?>
        <div class="card clickable-row" style="padding: 15px; margin-bottom: 10px; border-left: 5px solid <?= $textColor ?>;" onclick="showCheckDetail(<?= htmlspecialchars(json_encode($history), ENT_QUOTES, 'UTF-8') ?>)">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <strong style="font-size: 1.1rem;"><?= $history['nama_pasien'] ?></strong>
                <span style="font-size: 0.8rem; color: var(--text-muted);"><?= date('d M Y', strtotime($history['waktu_cek'])) ?></span>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px;">
                <?= trim($ageStr) ?> | <?= $history['berat_badan_kg'] ?> kg
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; background: var(--light-bg); padding: 10px; border-radius: 8px;">
                <div>
                    <div style="font-weight: bold; color: var(--primary-blue);"><?= $history['nama_obat'] ?></div>
                    <div style="font-size: 0.8rem;"><?= $history['total_dosis_harian_mg'] ?> mg/hari</div>
                </div>
                <div style="text-align: right;">
                    <div style="background: <?= $badgeClass ?>; color: <?= $textColor ?>; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; display: inline-block;">
                        <?= $history['status'] ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($histories)): ?>
            <div style="text-align: center; padding: 20px; color: var(--text-muted);">Belum ada riwayat pengecekan.</div>
        <?php endif; ?>
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
        <a href="history.php" class="nav-item active">
            <div class="nav-icon">📋</div>
            Riwayat
        </a>
    </div>
    </div>
    
    <!-- Modal Detail Cek Dosis -->
    <div id="checkDetailModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('checkDetailModal')">&times;</span>
            <h2 style="color: var(--primary-blue); margin-bottom: 5px; font-size: 1.1rem;">Detail Pengecekan</h2>
            <p style="color: var(--text-muted); font-size: 0.8rem;" id="modalWaktuCek"></p>
            
            <div class="detail-grid" style="font-size: 0.9rem;">
                <div><strong>Pasien</strong></div>
                <div id="modalNamaPasien"></div>
                
                <div><strong>Umur / BB</strong></div>
                <div id="modalUmurBB"></div>

                <div><strong>Diagnosis</strong></div>
                <div id="modalDiagnosis"></div>
                
                <div><strong>Obat</strong></div>
                <div id="modalNamaObat"></div>
                
                <div><strong>Dosis/Kali</strong></div>
                <div id="modalDosisPerKali"></div>
                
                <div><strong>Frekuensi</strong></div>
                <div id="modalFrekuensi"></div>
                
                <div><strong>Total Harian</strong></div>
                <div id="modalTotalHarian"></div>
                
                <div><strong>Status</strong></div>
                <div><span id="modalStatus" class="badge"></span></div>
                
                <div><strong>AI Score</strong></div>
                <div id="modalAiScore" style="font-weight: bold;"></div>
            </div>
        </div>
    </div>

    <script>
        function showCheckDetail(data) {
            document.getElementById('modalWaktuCek').innerText = data.waktu_cek;
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

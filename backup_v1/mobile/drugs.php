<?php
// mobile/drugs.php
require_once '../config/db.php';

$message = '';
$alertClass = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM drugs WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "Obat dihapus.";
        $alertClass = "alert-success";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nama_obat = htmlspecialchars($_POST['nama_obat']);
    $diagnosis = htmlspecialchars($_POST['diagnosis']);
    $min_mg = (float)$_POST['min_mg_per_kg_per_day'];
    $max_mg = (float)$_POST['max_mg_per_kg_per_day'];

    if (empty($id)) {
        $stmt = $pdo->prepare("INSERT INTO drugs (nama_obat, diagnosis, min_mg_per_kg_per_day, max_mg_per_kg_per_day) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nama_obat, $diagnosis, $min_mg, $max_mg])) {
            $message = "Obat ditambah.";
            $alertClass = "alert-success";
        }
    } else {
        $stmt = $pdo->prepare("UPDATE drugs SET nama_obat=?, diagnosis=?, min_mg_per_kg_per_day=?, max_mg_per_kg_per_day=? WHERE id=?");
        if ($stmt->execute([$nama_obat, $diagnosis, $min_mg, $max_mg, $id])) {
            $message = "Obat diupdate.";
            $alertClass = "alert-success";
        }
    }
}

$editDrug = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE id = ?");
    $stmt->execute([$id]);
    $editDrug = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM drugs ORDER BY nama_obat ASC");
$drugs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Obat - Mobile</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/mobile-style.css?v=<?= time() ?>">
</head>
<body>

    <div class="mobile-topbar">
        <h1>Data Obat</h1>
        <a href="../drugs.php" style="color: white; text-decoration: none; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 5px;">💻 Desktop</a>
    </div>

    <div class="main-content">
        <?php if ($message): ?>
            <div class="alert <?= $alertClass ?>" style="padding: 10px; font-size: 0.9rem;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-size: 1.1rem; margin-bottom: 10px;"><?= $editDrug ? 'Edit Obat' : 'Tambah Obat' ?></h2>
            <form action="drugs.php" method="POST">
                <input type="hidden" name="id" value="<?= $editDrug['id'] ?? '' ?>">
                
                <div class="form-group">
                    <input type="text" name="nama_obat" class="form-control" required placeholder="Nama Obat" value="<?= $editDrug['nama_obat'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <input type="text" name="diagnosis" class="form-control" placeholder="Indikasi" value="<?= $editDrug['diagnosis'] ?? '' ?>">
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <small style="color: var(--text-muted);">Min (mg/kg)</small>
                        <input type="number" step="0.01" name="min_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['min_mg_per_kg_per_day'] ?? '' ?>">
                    </div>
                    <div style="flex: 1;">
                        <small style="color: var(--text-muted);">Max (mg/kg)</small>
                        <input type="number" step="0.01" name="max_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['max_mg_per_kg_per_day'] ?? '' ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 2; padding: 10px;"><?= $editDrug ? 'Simpan' : 'Tambah' ?></button>
                    <?php if ($editDrug): ?>
                        <a href="drugs.php" class="btn" style="flex: 1; padding: 10px; background-color: #e0e0e0; color: #333;">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h3 style="margin-bottom: 10px; font-size: 1rem;">Daftar Obat</h3>
        <?php foreach($drugs as $drug): ?>
        <div class="card" style="padding: 15px; margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="font-size: 1.1rem; color: var(--primary-blue);"><?= $drug['nama_obat'] ?></strong>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">
                        Range: <?= $drug['min_mg_per_kg_per_day'] ?> - <?= $drug['max_mg_per_kg_per_day'] ?> mg
                    </div>
                </div>
                <div style="display: flex; gap: 5px;">
                    <a href="drugs.php?edit=<?= $drug['id'] ?>" style="background: var(--alert-yellow); padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem;">✏️</a>
                    <a href="drugs.php?delete=<?= $drug['id'] ?>" class="btn-delete" style="background: var(--alert-red); padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem;">🗑️</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item">
            <div class="nav-icon">🏠</div>
            Home
        </a>
        <a href="check.php" class="nav-item">
            <div class="nav-icon">🩺</div>
            Cek Dosis
        </a>
        <a href="drugs.php" class="nav-item active">
            <div class="nav-icon">💊</div>
            Obat
        </a>
        <a href="history.php" class="nav-item">
            <div class="nav-icon">📋</div>
            Riwayat
        </a>
        <a href="about.php" class="nav-item">
            <div class="nav-icon">ℹ️</div>
            Info
        </a>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>

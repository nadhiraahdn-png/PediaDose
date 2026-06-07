<?php
// drugs.php
require_once 'config/db.php';

$message = '';
$alertClass = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM drugs WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "Data obat berhasil dihapus.";
        $alertClass = "alert-success";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nama_obat = htmlspecialchars($_POST['nama_obat']);
    $diagnosis = htmlspecialchars($_POST['diagnosis']);
    $min_mg = (float)$_POST['min_mg_per_kg_per_day'];
    $max_mg = (float)$_POST['max_mg_per_kg_per_day'];
    $catatan = htmlspecialchars($_POST['catatan']);

    if (empty($id)) {
        // Create
        $stmt = $pdo->prepare("INSERT INTO drugs (nama_obat, diagnosis, min_mg_per_kg_per_day, max_mg_per_kg_per_day, catatan) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nama_obat, $diagnosis, $min_mg, $max_mg, $catatan])) {
            $message = "Data obat berhasil ditambahkan.";
            $alertClass = "alert-success";
        }
    } else {
        // Update
        $stmt = $pdo->prepare("UPDATE drugs SET nama_obat=?, diagnosis=?, min_mg_per_kg_per_day=?, max_mg_per_kg_per_day=?, catatan=? WHERE id=?");
        if ($stmt->execute([$nama_obat, $diagnosis, $min_mg, $max_mg, $catatan, $id])) {
            $message = "Data obat berhasil diperbarui.";
            $alertClass = "alert-success";
        }
    }
}

// Fetch Data for Edit Form
$editDrug = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM drugs WHERE id = ?");
    $stmt->execute([$id]);
    $editDrug = $stmt->fetch();
}

// Fetch All Drugs
$stmt = $pdo->query("SELECT * FROM drugs ORDER BY nama_obat ASC");
$drugs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Obat - DoseCheck AI</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>DoseCheck AI</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="check.php">Cek Dosis</a></li>
            <li><a href="drugs.php" class="active">Data Obat</a></li>
            <li><a href="history.php">Riwayat Cek</a></li>
            <li><a href="about.php">Tentang</a></li>
        </ul>
        <div style="padding: 15px 20px;">
            <a href="mobile/drugs.php" style="display: block; background: var(--secondary-blue); color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold;">📱 Versi Mobile</a>
        </div>
        <div class="sidebar-footer">
            <p>&copy; 2026 DoseCheck AI</p>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Manajemen Data Obat</h1>

        <?php if ($message): ?>
            <div class="alert <?= $alertClass ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2><?= $editDrug ? 'Edit Data Obat' : 'Tambah Obat Baru' ?></h2>
            <form action="drugs.php" method="POST" style="margin-top: 15px;">
                <input type="hidden" name="id" value="<?= $editDrug['id'] ?? '' ?>">
                
                <div class="grid-2">
                    <div class="form-group">
                        <label>Nama Obat</label>
                        <input type="text" name="nama_obat" class="form-control" required value="<?= $editDrug['nama_obat'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Indikasi / Diagnosis</label>
                        <input type="text" name="diagnosis" class="form-control" value="<?= $editDrug['diagnosis'] ?? '' ?>">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Min Dosis (mg/kg/hari)</label>
                        <input type="number" step="0.01" name="min_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['min_mg_per_kg_per_day'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Max Dosis (mg/kg/hari)</label>
                        <input type="number" step="0.01" name="max_mg_per_kg_per_day" class="form-control" required value="<?= $editDrug['max_mg_per_kg_per_day'] ?? '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan / Panduan Klinis</label>
                    <textarea name="catatan" class="form-control" rows="3"><?= $editDrug['catatan'] ?? '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><?= $editDrug ? 'Simpan Perubahan' : 'Tambah Obat' ?></button>
                <?php if ($editDrug): ?>
                    <a href="drugs.php" class="btn" style="background-color: var(--light-bg); color: var(--text-dark);">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>Daftar Obat Tersedia</h2>
            <div class="table-responsive" style="margin-top: 15px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Rentang Dosis (mg/kg/hari)</th>
                            <th>Indikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($drugs as $index => $drug): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= $drug['nama_obat'] ?></strong></td>
                            <td><?= $drug['min_mg_per_kg_per_day'] ?> - <?= $drug['max_mg_per_kg_per_day'] ?></td>
                            <td><?= $drug['diagnosis'] ?></td>
                            <td>
                                <a href="drugs.php?edit=<?= $drug['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="drugs.php?delete=<?= $drug['id'] ?>" class="btn btn-sm btn-danger btn-delete">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($drugs)): ?>
                        <tr><td colspan="5" style="text-align: center;">Belum ada data obat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

<?php
// print.php - Cetak kartu pendaftaran

require_once 'config.php';

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = (int)$_GET['id'];
$conn = getConnection();

$sql = "SELECT * FROM pendaftaran WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data tidak ditemukan");
}

$data = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kartu Pendaftaran - <?= $data['nomor_registrasi'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .card { border: 2px solid #000; padding: 20px; max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .foto { text-align: center; margin: 20px 0; }
        .foto img { width: 150px; border: 2px solid #000; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .label { font-weight: bold; width: 200px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px;">🖨️ Cetak</button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 16px;">❌ Tutup</button>
    </div>
    
    <div class="card">
        <div class="header">
            <h2>KARTU PENDAFTARAN MAHASISWA BARU</h2>
            <h3>TAHUN AKADEMIK <?= date('Y') ?>/<?= date('Y')+1 ?></h3>
        </div>
        
        <?php if($data['pas_foto']): ?>
        <div class="foto">
            <img src="uploads/<?= $data['pas_foto'] ?>" alt="Pas Foto">
        </div>
        <?php endif; ?>
        
        <table>
            <tr>
                <td class="label">Nomor Registrasi</td>
                <td>: <strong><?= $data['nomor_registrasi'] ?></strong></td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>: <?= $data['nama_lengkap'] ?></td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td>: <?= $data['tempat_lahir'] ?>, <?= date('d F Y', strtotime($data['tanggal_lahir'])) ?></td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td>: <?= $data['jenis_kelamin'] ?></td>
            </tr>
            <tr>
                <td class="label">Program Studi Pilihan</td>
                <td>: <?= $data['prodi1'] ?></td>
            </tr>
            <tr>
                <td class="label">Jalur Pendaftaran</td>
                <td>: <?= $data['jalur_pendaftaran'] ?></td>
            </tr>
            <tr>
                <td class="label">Asal Sekolah</td>
                <td>: <?= $data['asal_sekolah'] ?></td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>: <strong><?= strtoupper($data['status']) ?></strong></td>
            </tr>
        </table>
        
        <div style="margin-top: 40px; text-align: right;">
            <p>Tanggal Pendaftaran: <?= date('d F Y', strtotime($data['tanggal_daftar'])) ?></p>
            <br><br><br>
            <p>_____________________</p>
            <p>Panitia Pendaftaran</p>
        </div>
    </div>
</body>
</html>
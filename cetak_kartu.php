<?php
// cetak_kartu.php - Cetak kartu pendaftaran mahasiswa

require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: list_mahasiswa.php?error=no_id");
    exit();
}

$id = (int)$_GET['id'];
$conn = getConnection();

// Get data
$sql = "SELECT * FROM pendaftaran WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list_mahasiswa.php?error=not_found");
    exit();
}

$data = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pendaftaran - <?= htmlspecialchars($data['nama_lengkap']) ?></title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .kartu-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .kartu {
            padding: 40px;
        }
        
        .header-kartu {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .header-kartu h1 {
            margin: 10px 0;
            font-size: 24px;
            color: #333;
        }
        
        .header-kartu h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #667eea;
        }
        
        .header-kartu p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        
        .content-kartu {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .foto-container {
            text-align: center;
        }
        
        .foto-box {
            width: 150px;
            height: 200px;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
            overflow: hidden;
        }
        
        .foto-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .data-kartu {
            padding-top: 10px;
        }
        
        .nomor-registrasi {
            background: #667eea;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .info-item {
            display: grid;
            grid-template-columns: 140px 1fr;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            margin: 25px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .status-pending {
            background: #ffc107;
            color: #000;
        }
        
        .status-approved {
            background: #28a745;
            color: white;
        }
        
        .status-rejected {
            background: #dc3545;
            color: white;
        }
        
        .footer-kartu {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
        }
        
        .footer-kartu p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 50px;
            text-align: center;
        }
        
        .signature-box {
            padding: 20px 0;
        }
        
        .signature-line {
            margin-top: 60px;
            padding-top: 10px;
            border-top: 1px solid #333;
            font-weight: 600;
        }
        
        .btn-print {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin: 20px auto;
            display: block;
        }
        
        .btn-print:hover {
            background: #5568d3;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <a href="view_mahasiswa.php?id=<?= $id ?>" class="btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Kartu</button>
    </div>

    <div class="kartu-container">
        <div class="kartu">
            <div class="header-kartu">
                <div class="logo">🎓</div>
                <h1>UNIVERSITAS CONTOH</h1>
                <h2>KARTU PENDAFTARAN MAHASISWA BARU</h2>
                <p>Tahun Akademik 2024/2025</p>
            </div>

            <div class="content-kartu">
                <div class="foto-container">
                    <div class="foto-box">
                        <?php if ($data['pas_foto']): ?>
                            <img src="uploads/<?= htmlspecialchars($data['pas_foto']) ?>" alt="Pas Foto">
                        <?php else: ?>
                            <span style="color: #999;">Tidak ada foto</span>
                        <?php endif; ?>
                    </div>
                    <div class="status-badge status-<?= $data['status'] ?>">
                        <?= strtoupper($data['status']) ?>
                    </div>
                </div>

                <div class="data-kartu">
                    <div class="nomor-registrasi">
                        No. Registrasi: <?= htmlspecialchars($data['nomor_registrasi']) ?>
                    </div>

                    <div class="section-title">DATA PRIBADI</div>
                    
                    <div class="info-item">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value"><?= htmlspecialchars($data['nama_lengkap']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">NIK</div>
                        <div class="info-value"><?= htmlspecialchars($data['nik']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tempat, Tgl Lahir</div>
                        <div class="info-value">
                            <?= htmlspecialchars($data['tempat_lahir']) ?>, 
                            <?= date('d F Y', strtotime($data['tanggal_lahir'])) ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Jenis Kelamin</div>
                        <div class="info-value"><?= htmlspecialchars($data['jenis_kelamin']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Agama</div>
                        <div class="info-value"><?= htmlspecialchars($data['agama']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($data['email']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">No. Telepon</div>
                        <div class="info-value"><?= htmlspecialchars($data['no_telepon']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Alamat</div>
                        <div class="info-value"><?= htmlspecialchars($data['alamat']) ?></div>
                    </div>

                    <div class="section-title">DATA PENDIDIKAN</div>
                    
                    <div class="info-item">
                        <div class="info-label">Asal Sekolah</div>
                        <div class="info-value"><?= htmlspecialchars($data['asal_sekolah']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Jurusan</div>
                        <div class="info-value"><?= htmlspecialchars($data['jurusan_sekolah']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tahun Lulus</div>
                        <div class="info-value"><?= htmlspecialchars($data['tahun_lulus']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Nilai Rata-rata</div>
                        <div class="info-value"><?= htmlspecialchars($data['nilai_rata']) ?></div>
                    </div>

                    <div class="section-title">PROGRAM STUDI</div>
                    
                    <div class="info-item">
                        <div class="info-label">Pilihan 1</div>
                        <div class="info-value"><strong><?= htmlspecialchars($data['prodi1']) ?></strong></div>
                    </div>
                    
                    <?php if ($data['prodi2']): ?>
                    <div class="info-item">
                        <div class="info-label">Pilihan 2</div>
                        <div class="info-value"><?= htmlspecialchars($data['prodi2']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <div class="info-label">Jalur Pendaftaran</div>
                        <div class="info-value"><?= htmlspecialchars($data['jalur_pendaftaran']) ?></div>
                    </div>

                    <div class="section-title">DATA ORANG TUA</div>
                    
                    <div class="info-item">
                        <div class="info-label">Nama Ayah</div>
                        <div class="info-value"><?= htmlspecialchars($data['nama_ayah']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Nama Ibu</div>
                        <div class="info-value"><?= htmlspecialchars($data['nama_ibu']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">No. Telp Ortu</div>
                        <div class="info-value"><?= htmlspecialchars($data['no_telepon_ortu']) ?></div>
                    </div>
                </div>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <p>Pendaftar,</p>
                    <div class="signature-line">
                        <?= htmlspecialchars($data['nama_lengkap']) ?>
                    </div>
                </div>
                
                <div class="signature-box">
                    <p>Petugas Pendaftaran,</p>
                    <div class="signature-line">
                        (.................................)
                    </div>
                </div>
            </div>

            <div class="footer-kartu">
                <p><strong>Catatan:</strong> Kartu ini merupakan bukti pendaftaran yang sah.</p>
                <p>Harap dibawa pada saat verifikasi dokumen dan tes masuk.</p>
                <p style="margin-top: 15px; font-size: 11px;">
                    Dicetak pada: <?= date('d F Y H:i:s') ?>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto print when opened in new tab (optional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>
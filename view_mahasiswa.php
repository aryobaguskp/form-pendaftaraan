<?php
// view_mahasiswa.php - Menampilkan detail mahasiswa

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

// Function to display status badge
function getStatusBadge($status) {
    $badges = [
        'pending' => '<span style="background: #ffc107; color: #000; padding: 5px 15px; border-radius: 5px; font-weight: 600;">⏳ Pending</span>',
        'approved' => '<span style="background: #28a745; color: white; padding: 5px 15px; border-radius: 5px; font-weight: 600;">✅ Approved</span>',
        'rejected' => '<span style="background: #dc3545; color: white; padding: 5px 15px; border-radius: 5px; font-weight: 600;">❌ Rejected</span>'
    ];
    return $badges[$status] ?? $badges['pending'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mahasiswa</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, purple 30%, blue 70%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px;
        }
        .section-title {
            font-size: 20px;
            color: #667eea;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .info-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: #000;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .file-preview {
            margin-top: 10px;
        }
        .file-preview img {
            max-width: 200px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        .status-actions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 Detail Mahasiswa</h1>
            <p>Nomor Registrasi: <strong><?= htmlspecialchars($data['nomor_registrasi']) ?></strong></p>
        </div>

        <div class="content">
            <!-- Status Actions -->
            <div class="status-actions">
                <h3 style="margin-bottom: 15px; color: #333;">Status Pendaftaran: <?= getStatusBadge($data['status']) ?></h3>
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px;">
                    <?php if ($data['status'] !== 'approved'): ?>
                        <button onclick="updateStatus(<?= $id ?>, 'approved')" class="btn btn-success">
                            ✅ Approve
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($data['status'] !== 'rejected'): ?>
                        <button onclick="updateStatus(<?= $id ?>, 'rejected')" class="btn btn-danger">
                            ❌ Reject
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($data['status'] !== 'pending'): ?>
                        <button onclick="updateStatus(<?= $id ?>, 'pending')" class="btn btn-warning">
                            ⏳ Set Pending
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Data Pribadi -->
            <h3 class="section-title">Data Pribadi</h3>
            
            <div class="info-row">
                <div class="info-label">Nama Lengkap:</div>
                <div class="info-value"><?= htmlspecialchars($data['nama_lengkap']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tempat, Tanggal Lahir:</div>
                <div class="info-value"><?= htmlspecialchars($data['tempat_lahir']) ?>, <?= date('d F Y', strtotime($data['tanggal_lahir'])) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Jenis Kelamin:</div>
                <div class="info-value"><?= htmlspecialchars($data['jenis_kelamin']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Agama:</div>
                <div class="info-value"><?= htmlspecialchars($data['agama']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Alamat:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($data['alamat'])) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">No. Telepon:</div>
                <div class="info-value"><?= htmlspecialchars($data['no_telepon']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?= htmlspecialchars($data['email']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">NIK:</div>
                <div class="info-value"><?= htmlspecialchars($data['nik']) ?></div>
            </div>
            
            <?php if ($data['pas_foto']): ?>
            <div class="info-row">
                <div class="info-label">Pas Foto:</div>
                <div class="info-value">
                    <a href="uploads/<?= htmlspecialchars($data['pas_foto']) ?>" target="_blank" class="btn btn-primary">📎 Lihat Foto</a>
                    <div class="file-preview">
                        <img src="uploads/<?= htmlspecialchars($data['pas_foto']) ?>" alt="Pas Foto">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Data Pendidikan -->
            <h3 class="section-title">Data Pendidikan</h3>
            
            <div class="info-row">
                <div class="info-label">Asal Sekolah:</div>
                <div class="info-value"><?= htmlspecialchars($data['asal_sekolah']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Jurusan:</div>
                <div class="info-value"><?= htmlspecialchars($data['jurusan_sekolah']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tahun Lulus:</div>
                <div class="info-value"><?= htmlspecialchars($data['tahun_lulus']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">No. Ijazah:</div>
                <div class="info-value"><?= htmlspecialchars($data['no_ijazah']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Nilai Rata-rata:</div>
                <div class="info-value"><?= htmlspecialchars($data['nilai_rata']) ?></div>
            </div>

            <!-- Data Orang Tua -->
            <h3 class="section-title">Data Orang Tua</h3>
            
            <div class="info-row">
                <div class="info-label">Nama Ayah:</div>
                <div class="info-value"><?= htmlspecialchars($data['nama_ayah']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Nama Ibu:</div>
                <div class="info-value"><?= htmlspecialchars($data['nama_ibu']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Pekerjaan Ayah:</div>
                <div class="info-value"><?= htmlspecialchars($data['pekerjaan_ayah']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Pekerjaan Ibu:</div>
                <div class="info-value"><?= htmlspecialchars($data['pekerjaan_ibu']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Pendidikan Ayah:</div>
                <div class="info-value"><?= htmlspecialchars($data['pendidikan_ayah']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Pendidikan Ibu:</div>
                <div class="info-value"><?= htmlspecialchars($data['pendidikan_ibu']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Alamat Orang Tua:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($data['alamat_ortu'])) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">No. Telepon Orang Tua:</div>
                <div class="info-value"><?= htmlspecialchars($data['no_telepon_ortu']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Penghasilan:</div>
                <div class="info-value"><?= htmlspecialchars($data['penghasilan']) ?></div>
            </div>
            
            <?php if ($data['nama_wali']): ?>
            <div class="info-row">
                <div class="info-label">Nama Wali:</div>
                <div class="info-value"><?= htmlspecialchars($data['nama_wali']) ?></div>
            </div>
            <?php endif; ?>

            <!-- Program Studi -->
            <h3 class="section-title">Program Studi</h3>
            
            <div class="info-row">
                <div class="info-label">Pilihan 1:</div>
                <div class="info-value"><strong><?= htmlspecialchars($data['prodi1']) ?></strong></div>
            </div>
            
            <?php if ($data['prodi2']): ?>
            <div class="info-row">
                <div class="info-label">Pilihan 2:</div>
                <div class="info-value"><?= htmlspecialchars($data['prodi2']) ?></div>
            </div>
            <?php endif; ?>
            
            <div class="info-row">
                <div class="info-label">Jalur Pendaftaran:</div>
                <div class="info-value"><?= htmlspecialchars($data['jalur_pendaftaran']) ?></div>
            </div>

            <!-- Data Pendukung -->
            <h3 class="section-title">Data Pendukung</h3>
            
            <?php if ($data['prestasi']): ?>
            <div class="info-row">
                <div class="info-label">Prestasi:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($data['prestasi'])) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($data['organisasi']): ?>
            <div class="info-row">
                <div class="info-label">Organisasi:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($data['organisasi'])) ?></div>
            </div>
            <?php endif; ?>

            <!-- Dokumen -->
            <h3 class="section-title">Dokumen Pendukung</h3>
            
            <?php if ($data['upload_ijazah']): ?>
            <div class="info-row">
                <div class="info-label">Ijazah:</div>
                <div class="info-value">
                    <a href="uploads/<?= htmlspecialchars($data['upload_ijazah']) ?>" target="_blank" class="btn btn-primary">📄 Lihat Dokumen</a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($data['upload_rapor']): ?>
            <div class="info-row">
                <div class="info-label">Rapor:</div>
                <div class="info-value">
                    <a href="uploads/<?= htmlspecialchars($data['upload_rapor']) ?>" target="_blank" class="btn btn-primary">📄 Lihat Dokumen</a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($data['surat_sehat']): ?>
            <div class="info-row">
                <div class="info-label">Surat Sehat:</div>
                <div class="info-value">
                    <a href="uploads/<?= htmlspecialchars($data['surat_sehat']) ?>" target="_blank" class="btn btn-primary">📄 Lihat Dokumen</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tanggal Pendaftaran -->
            <div class="info-row">
                <div class="info-label">Tanggal Pendaftaran:</div>
                <div class="info-value"><?= date('d F Y H:i:s', strtotime($data['tanggal_daftar'])) ?></div>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 40px; display: flex; gap: 10px;">
                <a href="list_mahasiswa.php" class="btn btn-secondary">← Kembali</a>
                <a href="edit_mahasiswa.php?id=<?= $id ?>" class="btn btn-warning">✏️ Edit Data</a>
                <a href="cetak_kartu.php?id=<?= $id ?>" class="btn btn-primary" target="_blank">🖨️ Cetak Kartu</a>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(id, status) {
            const statusText = {
                'approved': 'APPROVE',
                'rejected': 'REJECT',
                'pending': 'PENDING'
            };
            
            const statusColor = {
                'approved': '#28a745',
                'rejected': '#dc3545',
                'pending': '#ffc107'
            };

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin mengubah status menjadi ${statusText[status]}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: statusColor[status],
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Mengupdate...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create FormData
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('status', status);

                    // Send AJAX request
                    fetch('update_status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Status berhasil diupdate',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan koneksi: ' + error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }
        
        // Debug function to test if script is loaded
        console.log('View mahasiswa script loaded successfully');
    </script>
</body>
</html>
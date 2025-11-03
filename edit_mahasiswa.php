<?php
// edit_mahasiswa.php - Edit data mahasiswa

require_once 'config.php';
$conn = getConnection();

// Get ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header("Location: list_mahasiswa.php?error=invalid_id");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload new files if provided
    $pas_foto = $_POST['old_pas_foto'];
    if (isset($_FILES['pasFoto']) && $_FILES['pasFoto']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['pasFoto']);
        if ($result['success']) {
            deleteFile($pas_foto); // Delete old file
            $pas_foto = $result['filename'];
        }
    }
    
    $upload_ijazah = $_POST['old_upload_ijazah'];
    if (isset($_FILES['uploadIjazah']) && $_FILES['uploadIjazah']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['uploadIjazah']);
        if ($result['success']) {
            deleteFile($upload_ijazah);
            $upload_ijazah = $result['filename'];
        }
    }
    
    $upload_rapor = $_POST['old_upload_rapor'];
    if (isset($_FILES['uploadRapor']) && $_FILES['uploadRapor']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['uploadRapor']);
        if ($result['success']) {
            deleteFile($upload_rapor);
            $upload_rapor = $result['filename'];
        }
    }
    
    $surat_sehat = $_POST['old_surat_sehat'];
    if (isset($_FILES['suratSehat']) && $_FILES['suratSehat']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['suratSehat']);
        if ($result['success']) {
            deleteFile($surat_sehat);
            $surat_sehat = $result['filename'];
        }
    }
    
    // Update query - FIXED: Count parameters correctly (34 fields + 1 id)
    $sql = "UPDATE pendaftaran SET 
        nama_lengkap = ?, tempat_lahir = ?, tanggal_lahir = ?, 
        jenis_kelamin = ?, agama = ?, alamat = ?, no_telepon = ?, 
        email = ?, nik = ?, pas_foto = ?,
        asal_sekolah = ?, jurusan_sekolah = ?, tahun_lulus = ?, 
        no_ijazah = ?, nilai_rata = ?,
        nama_ayah = ?, nama_ibu = ?, pekerjaan_ayah = ?, pekerjaan_ibu = ?,
        pendidikan_ayah = ?, pendidikan_ibu = ?, alamat_ortu = ?, 
        no_telepon_ortu = ?, penghasilan = ?, nama_wali = ?,
        prodi1 = ?, prodi2 = ?, jalur_pendaftaran = ?,
        prestasi = ?, organisasi = ?, 
        upload_ijazah = ?, upload_rapor = ?, surat_sehat = ?,
        status = ?
        WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    // Prepare variables
    $namaLengkap = $_POST['namaLengkap'];
    $tempatLahir = $_POST['tempatLahir'];
    $tanggalLahir = $_POST['tanggalLahir'];
    $jenisKelamin = $_POST['jenisKelamin'];
    $agama = $_POST['agama'];
    $alamat = $_POST['alamat'];
    $noTelepon = $_POST['noTelepon'];
    $email = $_POST['email'];
    $nik = $_POST['nik'];
    $asalSekolah = $_POST['asalSekolah'];
    $jurusanSekolah = $_POST['jurusanSekolah'];
    $tahunLulus = (int)$_POST['tahunLulus'];
    $noIjazah = $_POST['noIjazah'];
    $nilaiRata = (float)$_POST['nilaiRata'];
    $namaAyah = $_POST['namaAyah'];
    $namaIbu = $_POST['namaIbu'];
    $pekerjaanAyah = $_POST['pekerjaanAyah'];
    $pekerjaanIbu = $_POST['pekerjaanIbu'];
    $pendidikanAyah = $_POST['pendidikanAyah'];
    $pendidikanIbu = $_POST['pendidikanIbu'];
    $alamatOrtu = $_POST['alamatOrtu'];
    $noTeleponOrtu = $_POST['noTeleponOrtu'];
    $penghasilan = $_POST['penghasilan'];
    $namaWali = $_POST['namaWali'];
    $prodi1 = $_POST['prodi1'];
    $prodi2 = $_POST['prodi2'];
    $jalurPendaftaran = $_POST['jalurPendaftaran'];
    $prestasi = $_POST['prestasi'];
    $organisasi = $_POST['organisasi'];
    $status = $_POST['status'];
    
    // 35 parameters: 34 SET + 1 WHERE (types: s=string, i=integer, d=double)
    $stmt->bind_param(
        "ssssssssssssisdsssssssssssssssssssi",
        $namaLengkap,
        $tempatLahir,
        $tanggalLahir,
        $jenisKelamin,
        $agama,
        $alamat,
        $noTelepon,
        $email,
        $nik,
        $pas_foto,
        $asalSekolah,
        $jurusanSekolah,
        $tahunLulus,
        $noIjazah,
        $nilaiRata,
        $namaAyah,
        $namaIbu,
        $pekerjaanAyah,
        $pekerjaanIbu,
        $pendidikanAyah,
        $pendidikanIbu,
        $alamatOrtu,
        $noTeleponOrtu,
        $penghasilan,
        $namaWali,
        $prodi1,
        $prodi2,
        $jalurPendaftaran,
        $prestasi,
        $organisasi,
        $upload_ijazah,
        $upload_rapor,
        $surat_sehat,
        $status,
        $id
    );
    
    if ($stmt->execute()) {
        header("Location: list_mahasiswa.php?success=updated");
        exit();
    } else {
        $error = "Gagal update data: " . $stmt->error;
    }
    
    $stmt->close();
}

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
    <title>Edit Data Mahasiswa</title>
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .section-title {
            font-size: 20px;
            color: #667eea;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .file-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Data Mahasiswa</h1>
            <p>Nomor Registrasi: <strong><?= htmlspecialchars($data['nomor_registrasi']) ?></strong></p>
        </div>

        <div class="content">
            <?php if (isset($error)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    ❌ <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- Hidden fields for old file names -->
                <input type="hidden" name="old_pas_foto" value="<?= htmlspecialchars($data['pas_foto']) ?>">
                <input type="hidden" name="old_upload_ijazah" value="<?= htmlspecialchars($data['upload_ijazah']) ?>">
                <input type="hidden" name="old_upload_rapor" value="<?= htmlspecialchars($data['upload_rapor']) ?>">
                <input type="hidden" name="old_surat_sehat" value="<?= htmlspecialchars($data['surat_sehat']) ?>">

                <!-- Data Pribadi -->
                <h3 class="section-title">Data Pribadi</h3>
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="namaLengkap" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempatLahir" value="<?= htmlspecialchars($data['tempat_lahir']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggalLahir" value="<?= htmlspecialchars($data['tanggal_lahir']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenisKelamin" required>
                            <option value="Laki-laki" <?= $data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Agama</label>
                        <select name="agama" required>
                            <option value="Islam" <?= $data['agama'] === 'Islam' ? 'selected' : '' ?>>Islam</option>
                            <option value="Kristen" <?= $data['agama'] === 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                            <option value="Katolik" <?= $data['agama'] === 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                            <option value="Hindu" <?= $data['agama'] === 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                            <option value="Buddha" <?= $data['agama'] === 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                            <option value="Konghucu" <?= $data['agama'] === 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" required><?= htmlspecialchars($data['alamat']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="tel" name="noTelepon" value="<?= htmlspecialchars($data['no_telepon']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" name="nik" value="<?= htmlspecialchars($data['nik']) ?>" maxlength="16" required>
                    </div>
                    <div class="form-group">
                        <label>Pas Foto (Kosongkan jika tidak diganti)</label>
                        <input type="file" name="pasFoto" accept="image/*">
                        <?php if ($data['pas_foto']): ?>
                            <div class="file-info">📎 File saat ini: <?= htmlspecialchars($data['pas_foto']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Data Pendidikan -->
                <h3 class="section-title">Data Pendidikan</h3>

                <div class="form-group">
                    <label>Asal Sekolah</label>
                    <input type="text" name="asalSekolah" value="<?= htmlspecialchars($data['asal_sekolah']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jurusan Sekolah</label>
                        <input type="text" name="jurusanSekolah" value="<?= htmlspecialchars($data['jurusan_sekolah']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tahun Lulus</label>
                        <input type="number" name="tahunLulus" value="<?= htmlspecialchars($data['tahun_lulus']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>No. Ijazah</label>
                        <input type="text" name="noIjazah" value="<?= htmlspecialchars($data['no_ijazah']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nilai Rata-rata</label>
                        <input type="number" name="nilaiRata" value="<?= htmlspecialchars($data['nilai_rata']) ?>" step="0.01" required>
                    </div>
                </div>

                <!-- Data Orang Tua -->
                <h3 class="section-title">Data Orang Tua</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Ayah</label>
                        <input type="text" name="namaAyah" value="<?= htmlspecialchars($data['nama_ayah']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Ibu</label>
                        <input type="text" name="namaIbu" value="<?= htmlspecialchars($data['nama_ibu']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaanAyah" value="<?= htmlspecialchars($data['pekerjaan_ayah']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaanIbu" value="<?= htmlspecialchars($data['pekerjaan_ibu']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pendidikan Ayah</label>
                        <select name="pendidikanAyah" required>
                            <?php foreach(['SD','SMP','SMA','D3','S1','S2','S3'] as $pend): ?>
                                <option value="<?= $pend ?>" <?= $data['pendidikan_ayah'] === $pend ? 'selected' : '' ?>><?= $pend ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pendidikan Ibu</label>
                        <select name="pendidikanIbu" required>
                            <?php foreach(['SD','SMP','SMA','D3','S1','S2','S3'] as $pend): ?>
                                <option value="<?= $pend ?>" <?= $data['pendidikan_ibu'] === $pend ? 'selected' : '' ?>><?= $pend ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Orang Tua</label>
                    <textarea name="alamatOrtu" required><?= htmlspecialchars($data['alamat_ortu']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>No. Telepon Orang Tua</label>
                        <input type="tel" name="noTeleponOrtu" value="<?= htmlspecialchars($data['no_telepon_ortu']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Penghasilan</label>
                        <select name="penghasilan" required>
                            <option value="< 1 juta" <?= $data['penghasilan'] === '< 1 juta' ? 'selected' : '' ?>>< Rp 1.000.000</option>
                            <option value="1-3 juta" <?= $data['penghasilan'] === '1-3 juta' ? 'selected' : '' ?>>Rp 1-3 juta</option>
                            <option value="3-5 juta" <?= $data['penghasilan'] === '3-5 juta' ? 'selected' : '' ?>>Rp 3-5 juta</option>
                            <option value="5-10 juta" <?= $data['penghasilan'] === '5-10 juta' ? 'selected' : '' ?>>Rp 5-10 juta</option>
                            <option value="> 10 juta" <?= $data['penghasilan'] === '> 10 juta' ? 'selected' : '' ?>>> Rp 10 juta</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Wali (Opsional)</label>
                    <input type="text" name="namaWali" value="<?= htmlspecialchars($data['nama_wali'] ?? '') ?>">
                </div>

                <!-- Program Studi -->
                <h3 class="section-title">Program Studi</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Program Studi 1</label>
                        <select name="prodi1" required>
                            <?php 
                            $prodi_list = ['Teknik Informatika','Sistem Informasi','Teknik Elektro','Teknik Mesin','Teknik Sipil','Manajemen','Akuntansi','Hukum','Psikologi','Arsitektur'];
                            foreach($prodi_list as $prodi): 
                            ?>
                                <option value="<?= $prodi ?>" <?= $data['prodi1'] === $prodi ? 'selected' : '' ?>><?= $prodi ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Program Studi 2 (Opsional)</label>
                        <select name="prodi2">
                            <option value="">-- Tidak ada --</option>
                            <?php foreach($prodi_list as $prodi): ?>
                                <option value="<?= $prodi ?>" <?= $data['prodi2'] === $prodi ? 'selected' : '' ?>><?= $prodi ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jalur Pendaftaran</label>
                        <select name="jalurPendaftaran" required>
                            <?php foreach(['Reguler','Beasiswa','Undangan','Prestasi'] as $jalur): ?>
                                <option value="<?= $jalur ?>" <?= $data['jalur_pendaftaran'] === $jalur ? 'selected' : '' ?>><?= $jalur ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="pending" <?= $data['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $data['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $data['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Data Pendukung -->
                <h3 class="section-title">Data Pendukung</h3>

                <div class="form-group">
                    <label>Prestasi (Opsional)</label>
                    <textarea name="prestasi"><?= htmlspecialchars($data['prestasi'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Organisasi (Opsional)</label>
                    <textarea name="organisasi"><?= htmlspecialchars($data['organisasi'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Upload Ijazah (Kosongkan jika tidak diganti)</label>
                    <input type="file" name="uploadIjazah" accept=".pdf,.jpg,.jpeg,.png">
                    <?php if ($data['upload_ijazah']): ?>
                        <div class="file-info">📎 File saat ini: <?= htmlspecialchars($data['upload_ijazah']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Upload Rapor (Kosongkan jika tidak diganti)</label>
                    <input type="file" name="uploadRapor" accept=".pdf,.jpg,.jpeg,.png">
                    <?php if ($data['upload_rapor']): ?>
                        <div class="file-info">📎 File saat ini: <?= htmlspecialchars($data['upload_rapor']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Surat Sehat (Kosongkan jika tidak diganti)</label>
                    <input type="file" name="suratSehat" accept=".pdf,.jpg,.jpeg,.png">
                    <?php if ($data['surat_sehat']): ?>
                        <div class="file-info">📎 File saat ini: <?= htmlspecialchars($data['surat_sehat']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="list_mahasiswa.php" class="btn btn-secondary">← Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show success message if redirected after save
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('saved')) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data mahasiswa berhasil diupdate',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
    </script>
</body>
</html>
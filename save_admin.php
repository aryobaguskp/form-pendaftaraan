<?php
// save_admin.php - Menyimpan data dan redirect ke list mahasiswa (ADMIN VERSION)
// Gunakan ini jika ingin redirect langsung ke halaman admin

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $conn = getConnection();
    
    if (!$conn) {
        die("Koneksi database gagal");
    }
    
    // Generate nomor registrasi
    $year = date('Y');
    $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $nomor_registrasi = "PMB{$year}{$random}";
    
    // Upload files
    $pas_foto = null;
    if (isset($_FILES['pasFoto']) && $_FILES['pasFoto']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['pasFoto']);
        if ($result['success']) {
            $pas_foto = $result['filename'];
        }
    }
    
    $upload_ijazah = null;
    if (isset($_FILES['uploadIjazah']) && $_FILES['uploadIjazah']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['uploadIjazah']);
        if ($result['success']) {
            $upload_ijazah = $result['filename'];
        }
    }
    
    $upload_rapor = null;
    if (isset($_FILES['uploadRapor']) && $_FILES['uploadRapor']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['uploadRapor']);
        if ($result['success']) {
            $upload_rapor = $result['filename'];
        }
    }
    
    $surat_sehat = null;
    if (isset($_FILES['suratSehat']) && $_FILES['suratSehat']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['suratSehat']);
        if ($result['success']) {
            $surat_sehat = $result['filename'];
        }
    }
    
    // Prepare statement
    $sql = "INSERT INTO pendaftaran (
        nomor_registrasi, nama_lengkap, tempat_lahir, tanggal_lahir, 
        jenis_kelamin, agama, alamat, no_telepon, email, nik, pas_foto,
        asal_sekolah, jurusan_sekolah, tahun_lulus, no_ijazah, nilai_rata,
        nama_ayah, nama_ibu, pekerjaan_ayah, pekerjaan_ibu,
        pendidikan_ayah, pendidikan_ibu, alamat_ortu, no_telepon_ortu,
        penghasilan, nama_wali, prodi1, prodi2, jalur_pendaftaran,
        prestasi, organisasi, upload_ijazah, upload_rapor, surat_sehat, tanggal_daftar
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    
    // Get POST values
    $nama_wali = isset($_POST['namaWali']) ? trim($_POST['namaWali']) : '';
    $prodi2 = isset($_POST['prodi2']) ? trim($_POST['prodi2']) : '';
    $prestasi = isset($_POST['prestasi']) ? trim($_POST['prestasi']) : '';
    $organisasi = isset($_POST['organisasi']) ? trim($_POST['organisasi']) : '';
    
    // Clean data
    $namaLengkap = trim($_POST['namaLengkap']);
    $tempatLahir = trim($_POST['tempatLahir']);
    $tanggalLahir = trim($_POST['tanggalLahir']);
    $jenisKelamin = trim($_POST['jenisKelamin']);
    $agama = trim($_POST['agama']);
    $alamat = trim($_POST['alamat']);
    $noTelepon = trim($_POST['noTelepon']);
    $email = trim($_POST['email']);
    $nik = trim($_POST['nik']);
    $asalSekolah = trim($_POST['asalSekolah']);
    $jurusanSekolah = trim($_POST['jurusanSekolah']);
    $tahunLulus = trim($_POST['tahunLulus']);
    $noIjazah = trim($_POST['noIjazah']);
    $nilaiRata = trim($_POST['nilaiRata']);
    $namaAyah = trim($_POST['namaAyah']);
    $namaIbu = trim($_POST['namaIbu']);
    $pekerjaanAyah = trim($_POST['pekerjaanAyah']);
    $pekerjaanIbu = trim($_POST['pekerjaanIbu']);
    $pendidikanAyah = trim($_POST['pendidikanAyah']);
    $pendidikanIbu = trim($_POST['pendidikanIbu']);
    $alamatOrtu = trim($_POST['alamatOrtu']);
    $noTeleponOrtu = trim($_POST['noTeleponOrtu']);
    $penghasilan = trim($_POST['penghasilan']);
    $prodi1 = trim($_POST['prodi1']);
    $jalurPendaftaran = trim($_POST['jalurPendaftaran']);
    
    $stmt->bind_param(
        "sssssssssssssissssssssssssssssssss",
        $nomor_registrasi,
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
        $nama_wali,
        $prodi1,
        $prodi2,
        $jalurPendaftaran,
        $prestasi,
        $organisasi,
        $upload_ijazah,
        $upload_rapor,
        $surat_sehat
    );
    
    if ($stmt->execute()) {
        // Redirect to list with success message
        header("Location: list_mahasiswa.php?success=registered&reg=" . urlencode($nomor_registrasi));
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
} else {
    die("Invalid request method");
}
?>
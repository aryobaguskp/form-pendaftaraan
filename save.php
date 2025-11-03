<?php
// save.php - Menyimpan data pendaftaran mahasiswa (FIXED VERSION)

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log untuk debug
file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - Script started\n", FILE_APPEND);

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - POST received\n", FILE_APPEND);
    file_put_contents('debug_log.txt', "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);
    file_put_contents('debug_log.txt', "FILES Data: " . print_r($_FILES, true) . "\n", FILE_APPEND);
    
    $conn = getConnection();
    
    // Cek koneksi
    if (!$conn) {
        die(json_encode(['success' => false, 'message' => 'Koneksi database gagal']));
    }
    
    // Generate nomor registrasi
    $year = date('Y');
    $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $nomor_registrasi = "PMB{$year}{$random}";
    
    file_put_contents('debug_log.txt', "Nomor registrasi: {$nomor_registrasi}\n", FILE_APPEND);
    
    // Upload files dengan error handling
    $pas_foto = null;
    if (isset($_FILES['pasFoto']) && $_FILES['pasFoto']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['pasFoto']);
        if ($result['success']) {
            $pas_foto = $result['filename'];
            file_put_contents('debug_log.txt', "Pas foto uploaded: {$pas_foto}\n", FILE_APPEND);
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
    
    // Validasi data required
    $required_fields = [
        'namaLengkap', 'tempatLahir', 'tanggalLahir', 'jenisKelamin', 
        'agama', 'alamat', 'noTelepon', 'email', 'nik',
        'asalSekolah', 'jurusanSekolah', 'tahunLulus', 'noIjazah', 'nilaiRata',
        'namaAyah', 'namaIbu', 'pekerjaanAyah', 'pekerjaanIbu',
        'pendidikanAyah', 'pendidikanIbu', 'alamatOrtu', 'noTeleponOrtu',
        'penghasilan', 'prodi1', 'jalurPendaftaran'
    ];
    
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        file_put_contents('debug_log.txt', "Missing fields: " . implode(', ', $missing_fields) . "\n", FILE_APPEND);
        die(json_encode([
            'success' => false, 
            'message' => 'Field berikut tidak boleh kosong: ' . implode(', ', $missing_fields)
        ]));
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
        file_put_contents('debug_log.txt', "Prepare error: " . $conn->error . "\n", FILE_APPEND);
        die(json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]));
    }
    
    // Ambil nilai POST dengan default empty string untuk field optional
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
    
    file_put_contents('debug_log.txt', "Executing query...\n", FILE_APPEND);
    
    if ($stmt->execute()) {
        file_put_contents('debug_log.txt', "SUCCESS! Data saved with ID: " . $stmt->insert_id . "\n", FILE_APPEND);
        
        // Redirect to success page
        header("Location: list_mahasiswa.php?success=registered&reg=" . urlencode($nomor_registrasi));
        exit();
    } else {
        file_put_contents('debug_log.txt', "Execute error: " . $stmt->error . "\n", FILE_APPEND);
        
        echo json_encode([
            'success' => false,
            'message' => 'Error saat menyimpan data',
            'error' => $stmt->error,
            'errno' => $stmt->errno
        ]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    file_put_contents('debug_log.txt', "Invalid request method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    die(json_encode(['success' => false, 'message' => 'Invalid request method. Gunakan POST.']));
}
?>
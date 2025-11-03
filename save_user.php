<?php
// save_user.php - Menyimpan data pendaftaran (USER VERSION - Return JSON)

error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display errors for JSON response

header('Content-Type: application/json');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        $conn = getConnection();
        
        if (!$conn) {
            throw new Exception('Koneksi database gagal');
        }
        
        // Generate nomor registrasi
        $year = date('Y');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $nomor_registrasi = "PMB{$year}{$random}";
        
        // Upload files dengan error handling
        $pas_foto = null;
        if (isset($_FILES['pasFoto']) && $_FILES['pasFoto']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFile($_FILES['pasFoto']);
            if ($result['success']) {
                $pas_foto = $result['filename'];
            } else {
                throw new Exception($result['message']);
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
            throw new Exception('Field berikut tidak boleh kosong: ' . implode(', ', $missing_fields));
        }
        
        // Prepare statement
        $sql = "INSERT INTO pendaftaran (
            nomor_registrasi, nama_lengkap, tempat_lahir, tanggal_lahir, 
            jenis_kelamin, agama, alamat, no_telepon, email, nik, pas_foto,
            asal_sekolah, jurusan_sekolah, tahun_lulus, no_ijazah, nilai_rata,
            nama_ayah, nama_ibu, pekerjaan_ayah, pekerjaan_ibu,
            pendidikan_ayah, pendidikan_ibu, alamat_ortu, no_telepon_ortu,
            penghasilan, nama_wali, prodi1, prodi2, jalur_pendaftaran,
            prestasi, organisasi, upload_ijazah, upload_rapor, surat_sehat, 
            status, tanggal_daftar
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Error preparing statement: ' . $conn->error);
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
        
        if ($stmt->execute()) {
            // Success response
            echo json_encode([
                'success' => true,
                'message' => 'Pendaftaran berhasil!',
                'nomor_registrasi' => $nomor_registrasi,
                'email' => $email,
                'nama' => $namaLengkap
            ]);
        } else {
            throw new Exception('Error saat menyimpan data: ' . $stmt->error);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        // Error response
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
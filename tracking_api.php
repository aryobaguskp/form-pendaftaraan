<?php
// tracking_api.php - API untuk tracking status pendaftaran

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config.php';

// Cek apakah parameter nomor ada
if (!isset($_GET['nomor']) || empty($_GET['nomor'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Nomor registrasi harus diisi'
    ]);
    exit();
}

$nomor_registrasi = sanitize($_GET['nomor']);
$conn = getConnection();

// Query untuk mendapatkan data berdasarkan nomor registrasi
$sql = "SELECT 
    nomor_registrasi,
    nama_lengkap,
    email,
    prodi1,
    tanggal_daftar,
    status
FROM pendaftaran 
WHERE nomor_registrasi = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nomor_registrasi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => 'Data ditemukan',
        'data' => [
            'nomor_registrasi' => $data['nomor_registrasi'],
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'],
            'prodi1' => $data['prodi1'],
            'tanggal_daftar' => $data['tanggal_daftar'],
            'status' => $data['status']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nomor registrasi tidak ditemukan. Pastikan nomor registrasi yang Anda masukkan benar.'
    ]);
}

$stmt->close();
$conn->close();
?>
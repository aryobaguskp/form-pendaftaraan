<?php
// search.php - API untuk search mahasiswa (AJAX)

require_once 'config.php';

if (isset($_GET['q'])) {
    $search = sanitize($_GET['q']);
    $conn = getConnection();
    
    $sql = "SELECT id, nomor_registrasi, nama_lengkap, email, prodi1, status 
            FROM pendaftaran 
            WHERE nama_lengkap LIKE ? OR nomor_registrasi LIKE ?
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $search_term = "%{$search}%";
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);
    
    $stmt->close();
    $conn->close();
}
?>
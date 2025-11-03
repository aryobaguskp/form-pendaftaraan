<?php
// update_status.php - Update status pendaftaran (pending/approved/rejected)

header('Content-Type: application/json');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    
    // Validasi status
    if (!in_array($status, ['pending', 'approved', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit();
    }
    
    $conn = getConnection();
    
    $sql = "UPDATE pendaftaran SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan atau status sudah sama']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update status: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>
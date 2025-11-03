<?php
// delete_mahasiswa.php - Menghapus data mahasiswa

require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: list_mahasiswa.php?error=no_id");
    exit();
}

$id = (int)$_GET['id'];
$conn = getConnection();

// Get file names before deleting
$sql = "SELECT pas_foto, upload_ijazah, upload_rapor, surat_sehat FROM pendaftaran WHERE id = ?";
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

// Delete the record
$sql = "DELETE FROM pendaftaran WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete associated files
    deleteFile($data['pas_foto']);
    deleteFile($data['upload_ijazah']);
    deleteFile($data['upload_rapor']);
    deleteFile($data['surat_sehat']);
    
    header("Location: list_mahasiswa.php?success=deleted");
} else {
    header("Location: list_mahasiswa.php?error=delete_failed");
}

$stmt->close();
$conn->close();
?>
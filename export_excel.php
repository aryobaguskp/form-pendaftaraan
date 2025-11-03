<?php
// export_excel.php - Export data mahasiswa ke Excel

require_once 'config.php';
$conn = getConnection();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$prodi = isset($_GET['prodi']) ? $_GET['prodi'] : '';

$where = [];
if ($status) {
    $where[] = "status = '" . $conn->real_escape_string($status) . "'";
}
if ($prodi) {
    $where[] = "prodi1 = '" . $conn->real_escape_string($prodi) . "'";
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT * FROM pendaftaran $where_clause ORDER BY tanggal_daftar DESC";
$result = $conn->query($sql);

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Data_Mahasiswa_' . date('Y-m-d_His') . '.xls"');
header('Cache-Control: max-age=0');

// Start output
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { 
            border-collapse: collapse; 
            width: 100%; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #667eea; 
            color: white; 
            font-weight: bold; 
        }
    </style>
</head>
<body>

<h2>Data Pendaftaran Mahasiswa Baru</h2>
<p>Tanggal Export: <?= date('d F Y H:i:s') ?> WIB</p>
<p>Total Data: <?= $result->num_rows ?> mahasiswa</p>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Registrasi</th>
                <th>Nama Lengkap</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Agama</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Email</th>
                <th>NIK</th>
                <th>Asal Sekolah</th>
                <th>Jurusan Sekolah</th>
                <th>Tahun Lulus</th>
                <th>No. Ijazah</th>
                <th>Nilai Rata-rata</th>
                <th>Nama Ayah</th>
                <th>Nama Ibu</th>
                <th>Pekerjaan Ayah</th>
                <th>Pekerjaan Ibu</th>
                <th>Pendidikan Ayah</th>
                <th>Pendidikan Ibu</th>
                <th>Alamat Orang Tua</th>
                <th>No. Telepon Orang Tua</th>
                <th>Penghasilan</th>
                <th>Nama Wali</th>
                <th>Program Studi 1</th>
                <th>Program Studi 2</th>
                <th>Jalur Pendaftaran</th>
                <th>Prestasi</th>
                <th>Organisasi</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $result->fetch_assoc()): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nomor_registrasi']) ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['tempat_lahir']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_lahir']) ?></td>
                <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                <td><?= htmlspecialchars($row['agama']) ?></td>
                <td><?= htmlspecialchars($row['alamat']) ?></td>
                <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['nik']) ?></td>
                <td><?= htmlspecialchars($row['asal_sekolah']) ?></td>
                <td><?= htmlspecialchars($row['jurusan_sekolah']) ?></td>
                <td><?= htmlspecialchars($row['tahun_lulus']) ?></td>
                <td><?= htmlspecialchars($row['no_ijazah']) ?></td>
                <td><?= htmlspecialchars($row['nilai_rata']) ?></td>
                <td><?= htmlspecialchars($row['nama_ayah']) ?></td>
                <td><?= htmlspecialchars($row['nama_ibu']) ?></td>
                <td><?= htmlspecialchars($row['pekerjaan_ayah']) ?></td>
                <td><?= htmlspecialchars($row['pekerjaan_ibu']) ?></td>
                <td><?= htmlspecialchars($row['pendidikan_ayah']) ?></td>
                <td><?= htmlspecialchars($row['pendidikan_ibu']) ?></td>
                <td><?= htmlspecialchars($row['alamat_ortu']) ?></td>
                <td><?= htmlspecialchars($row['no_telepon_ortu']) ?></td>
                <td><?= htmlspecialchars($row['penghasilan']) ?></td>
                <td><?= htmlspecialchars($row['nama_wali']) ?></td>
                <td><?= htmlspecialchars($row['prodi1']) ?></td>
                <td><?= htmlspecialchars($row['prodi2']) ?></td>
                <td><?= htmlspecialchars($row['jalur_pendaftaran']) ?></td>
                <td><?= htmlspecialchars($row['prestasi']) ?></td>
                <td><?= htmlspecialchars($row['organisasi']) ?></td>
                <td><?= strtoupper(htmlspecialchars($row['status'])) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['tanggal_daftar'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><strong>Tidak ada data untuk di-export</strong></p>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
```

---

## 📊 **FITUR export_excel.php:**

1. ✅ Export semua data mahasiswa ke format Excel (.xls)
2. ✅ Nama file otomatis dengan timestamp: `Data_Mahasiswa_2024-01-15_143025.xls`
3. ✅ Include header dengan tanggal export dan jumlah data
4. ✅ Semua field dari database (33 kolom)
5. ✅ Styling tabel dengan border dan warna header
6. ✅ Filter opsional berdasarkan status dan prodi (via URL)

---

## 🎯 **CARA PENGGUNAAN:**

### **Export Semua Data:**
```
http://localhost/pendaftaran-mahasiswa/export_excel.php
```

### **Export dengan Filter Status:**
```
http://localhost/pendaftaran-mahasiswa/export_excel.php?status=approved
http://localhost/pendaftaran-mahasiswa/export_excel.php?status=pending
http://localhost/pendaftaran-mahasiswa/export_excel.php?status=rejected
```

### **Export dengan Filter Program Studi:**
```
http://localhost/pendaftaran-mahasiswa/export_excel.php?prodi=Teknik Informatika
http://localhost/pendaftaran-mahasiswa/export_excel.php?prodi=Sistem Informasi
```

### **Export dengan 2 Filter Sekaligus:**
```
http://localhost/pendaftaran-mahasiswa/export_excel.php?status=approved&prodi=Teknik Informatika
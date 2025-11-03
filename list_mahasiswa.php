<?php
// list_mahasiswa.php - Menampilkan daftar mahasiswa

require_once 'config.php';
$conn = getConnection();

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where_clause = $search ? "WHERE nama_lengkap LIKE '%{$search}%' OR nomor_registrasi LIKE '%{$search}%'" : '';

// Total records
$count_sql = "SELECT COUNT(*) as total FROM pendaftaran $where_clause";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Get data
$sql = "SELECT * FROM pendaftaran $where_clause ORDER BY tanggal_daftar DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-edit {
            background: #ffc107;
            color: #000;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .search-box {
            padding: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        .search-box input {
            width: 300px;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
        }
        .pagination {
            padding: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        .pagination a.active {
            background: #667eea;
            color: white;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-pending {
            background: #ffc107;
            color: #000;
        }
        .badge-approved {
            background: #28a745;
            color: white;
        }
        .badge-rejected {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Daftar Mahasiswa Terdaftar</h1>
            <p>Kelola data pendaftaran mahasiswa baru</p>
        </div>

        <div class="search-box">
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" placeholder="Cari nama atau nomor registrasi..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn" style="background: #667eea; color: white;">🔍 Cari</button>
                <a href="list_mahasiswa.php" class="btn" style="background: #6c757d; color: white;">Reset</a>
                <a href="dashboard.php" class="btn" style="background: #17a2b8; color: white; margin-left: auto;">🏠 Dashboard</a>
                <a href="index.html" class="btn" style="background: #28a745; color: white;">➕ Tambah Pendaftaran</a>
                <a href="export_excel.php" class="btn" style="background: #28a745; color: white;">📊 Export Excel</a>
            </form>
        </div>

        <div style="padding: 20px; overflow-x: auto;">
            <?php if(isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <?php
                    if($_GET['success'] == 'deleted') echo '✅ Data berhasil dihapus!';
                    if($_GET['success'] == 'updated') echo '✅ Data berhasil diupdate!';
                    if($_GET['success'] == 'registered') echo '✅ Pendaftaran berhasil! No. Registrasi: ' . htmlspecialchars($_GET['reg']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    ❌ Terjadi kesalahan!
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Registrasi</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Program Studi</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result->num_rows > 0):
                        $no = $offset + 1;
                        while($row = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['nomor_registrasi']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['prodi1']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_daftar'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $row['status'] ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_mahasiswa.php?id=<?= $row['id'] ?>" class="btn btn-view">👁️ Lihat</a>
                                <a href="edit_mahasiswa.php?id=<?= $row['id'] ?>" class="btn btn-edit">✏️ Edit</a>
                                <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-delete">🗑️ Hapus</button>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                                Tidak ada data mahasiswa
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_mahasiswa.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
<?php
// dashboard.php - Dashboard admin dengan statistik

require_once 'config.php';
$conn = getConnection();

// Get statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'today' => 0
];

$sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN DATE(tanggal_daftar) = CURDATE() THEN 1 ELSE 0 END) as today
FROM pendaftaran";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
}

// Get recent registrations
$sql_recent = "SELECT id, nomor_registrasi, nama_lengkap, email, prodi1, status, tanggal_daftar 
               FROM pendaftaran 
               ORDER BY tanggal_daftar DESC 
               LIMIT 10";
$recent = $conn->query($sql_recent);

// Get prodi statistics
$sql_prodi = "SELECT prodi1, COUNT(*) as jumlah 
              FROM pendaftaran 
              GROUP BY prodi1 
              ORDER BY jumlah DESC 
              LIMIT 5";
$prodi_stats = $conn->query($sql_prodi);

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pendaftaran Mahasiswa</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .card-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-pending { background: #ffc107; color: #000; }
        .badge-approved { background: #28a745; color: white; }
        .badge-rejected { background: #dc3545; color: white; }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1 style="font-size: 28px; font-weight: bold; color: #333; margin-bottom: 5px;">
                        📊 Dashboard Admin
                    </h1>
                    <p style="color: #666;">Sistem Pendaftaran Mahasiswa Baru</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="index.html" class="btn btn-primary">➕ Pendaftaran Baru</a>
                    <a href="list_mahasiswa.php" class="btn" style="background: #6c757d; color: white;">📋 Lihat Semua Data</a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd;">👥</div>
                <div class="stat-number" style="color: #2196f3;"><?= number_format($stats['total']) ?></div>
                <div class="stat-label">Total Pendaftar</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3cd;">⏳</div>
                <div class="stat-number" style="color: #ffc107;"><?= number_format($stats['pending']) ?></div>
                <div class="stat-label">Menunggu Review</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #d4edda;">✅</div>
                <div class="stat-number" style="color: #28a745;"><?= number_format($stats['approved']) ?></div>
                <div class="stat-label">Disetujui</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #f8d7da;">❌</div>
                <div class="stat-number" style="color: #dc3545;"><?= number_format($stats['rejected']) ?></div>
                <div class="stat-label">Ditolak</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #e1bee7;">📅</div>
                <div class="stat-number" style="color: #9c27b0;"><?= number_format($stats['today']) ?></div>
                <div class="stat-label">Pendaftar Hari Ini</div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card">
                <h2 class="card-title">📝 Pendaftaran Terbaru</h2>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Registrasi</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent && $recent->num_rows > 0): ?>
                                <?php while($row = $recent->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['nomor_registrasi']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                    <td><?= htmlspecialchars($row['prodi1']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $row['status'] ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_daftar'])) ?></td>
                                    <td>
                                        <a href="view_mahasiswa.php?id=<?= $row['id'] ?>" 
                                           style="color: #667eea; text-decoration: none;">
                                            👁️ Lihat
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: #999;">
                                        Belum ada pendaftaran
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="card" style="margin-bottom: 20px;">
                    <h2 class="card-title">📈 Statistik Status</h2>
                    <canvas id="statusChart"></canvas>
                </div>

                <div class="card">
                    <h2 class="card-title">🎓 Top 5 Program Studi</h2>
                    <?php if ($prodi_stats && $prodi_stats->num_rows > 0): ?>
                        <?php while($row = $prodi_stats->fetch_assoc()): ?>
                            <div style="margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span style="font-size: 14px; color: #333;"><?= htmlspecialchars($row['prodi1']) ?></span>
                                    <span style="font-weight: 600; color: #667eea;"><?= $row['jumlah'] ?></span>
                                </div>
                                <div style="background: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                    <?php $percentage = $stats['total'] > 0 ? ($row['jumlah'] / $stats['total'] * 100) : 0; ?>
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; width: <?= $percentage ?>%; border-radius: 4px;"></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999;">Belum ada data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('statusChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    data: [
                        <?= $stats['pending'] ?>, 
                        <?= $stats['approved'] ?>, 
                        <?= $stats['rejected'] ?>
                    ],
                    backgroundColor: ['#ffc107', '#28a745', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>
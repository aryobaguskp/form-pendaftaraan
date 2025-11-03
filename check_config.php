<?php
// check_config.php - System checker untuk debugging
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>System Configuration Check</title>
    <style>
        body { 
            font-family: Arial; 
            max-width: 900px; 
            margin: 30px auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .check-box {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <h1>🔧 System Configuration Check</h1>

    <!-- Check 1: PHP Version -->
    <div class="check-box">
        <h3>1. PHP Version</h3>
        <?php
        $phpVersion = phpversion();
        $minVersion = '7.0.0';
        if (version_compare($phpVersion, $minVersion, '>=')) {
            echo "<p class='success'>✅ PHP Version: $phpVersion (OK)</p>";
        } else {
            echo "<p class='error'>❌ PHP Version: $phpVersion (Minimum required: $minVersion)</p>";
        }
        ?>
    </div>

    <!-- Check 2: Required Files -->
    <div class="check-box">
        <h3>2. Required Files</h3>
        <table>
            <tr><th>File</th><th>Status</th></tr>
            <?php
            $files = [
                'config.php',
                'update_status.php',
                'edit_mahasiswa.php',
                'delete_mahasiswa.php',
                'view_mahasiswa.php',
                'list_mahasiswa.php'
            ];
            
            foreach($files as $file) {
                $exists = file_exists($file);
                $readable = $exists ? is_readable($file) : false;
                
                if ($exists && $readable) {
                    echo "<tr><td>$file</td><td class='success'>✅ OK</td></tr>";
                } elseif ($exists) {
                    echo "<tr><td>$file</td><td class='warning'>⚠️ Not Readable</td></tr>";
                } else {
                    echo "<tr><td>$file</td><td class='error'>❌ Not Found</td></tr>";
                }
            }
            ?>
        </table>
    </div>

    <!-- Check 3: Database Connection -->
    <div class="check-box">
        <h3>3. Database Connection</h3>
        <?php
        if (file_exists('config.php')) {
            require_once 'config.php';
            
            try {
                $conn = getConnection();
                echo "<p class='success'>✅ Database connection successful</p>";
                
                // Check if table exists
                $result = $conn->query("SHOW TABLES LIKE 'pendaftaran'");
                if ($result->num_rows > 0) {
                    echo "<p class='success'>✅ Table 'pendaftaran' exists</p>";
                    
                    // Count records
                    $result = $conn->query("SELECT COUNT(*) as total FROM pendaftaran");
                    $row = $result->fetch_assoc();
                    echo "<p class='success'>✅ Total records: {$row['total']}</p>";
                } else {
                    echo "<p class='error'>❌ Table 'pendaftaran' not found</p>";
                }
                
                $conn->close();
            } catch (Exception $e) {
                echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>❌ config.php not found</p>";
        }
        ?>
    </div>

    <!-- Check 4: Folder Permissions -->
    <div class="check-box">
        <h3>4. Folder Permissions</h3>
        <table>
            <tr><th>Folder</th><th>Writable</th><th>Permissions</th></tr>
            <?php
            $folders = ['uploads'];
            
            foreach($folders as $folder) {
                if (!file_exists($folder)) {
                    echo "<tr><td>$folder</td><td class='error'>❌ Not Found</td><td>-</td></tr>";
                } else {
                    $writable = is_writable($folder);
                    $perms = substr(sprintf('%o', fileperms($folder)), -4);
                    
                    if ($writable) {
                        echo "<tr><td>$folder</td><td class='success'>✅ Writable</td><td>$perms</td></tr>";
                    } else {
                        echo "<tr><td>$folder</td><td class='error'>❌ Not Writable</td><td>$perms</td></tr>";
                    }
                }
            }
            ?>
        </table>
    </div>

    <!-- Check 5: PHP Extensions -->
    <div class="check-box">
        <h3>5. PHP Extensions</h3>
        <table>
            <tr><th>Extension</th><th>Status</th></tr>
            <?php
            $extensions = ['mysqli', 'json', 'fileinfo'];
            
            foreach($extensions as $ext) {
                $loaded = extension_loaded($ext);
                if ($loaded) {
                    echo "<tr><td>$ext</td><td class='success'>✅ Loaded</td></tr>";
                } else {
                    echo "<tr><td>$ext</td><td class='error'>❌ Not Loaded</td></tr>";
                }
            }
            ?>
        </table>
    </div>

    <!-- Check 6: Error Display Settings -->
    <div class="check-box">
        <h3>6. PHP Error Settings</h3>
        <table>
            <tr><th>Setting</th><th>Value</th></tr>
            <tr>
                <td>display_errors</td>
                <td><?= ini_get('display_errors') ? 'On' : 'Off' ?></td>
            </tr>
            <tr>
                <td>error_reporting</td>
                <td><?= error_reporting() ?></td>
            </tr>
            <tr>
                <td>log_errors</td>
                <td><?= ini_get('log_errors') ? 'On' : 'Off' ?></td>
            </tr>
        </table>
        <p><strong>Note:</strong> For debugging, enable display_errors in php.ini or add at the top of your PHP files:</p>
        <code style="display:block; background:#f5f5f5; padding:10px; margin:10px 0;">
            error_reporting(E_ALL);<br>
            ini_set('display_errors', 1);
        </code>
    </div>

    <!-- Check 7: Sample Data -->
    <div class="check-box">
        <h3>7. Sample Data (First 3 records)</h3>
        <?php
        if (file_exists('config.php')) {
            require_once 'config.php';
            $conn = getConnection();
            
            $sql = "SELECT id, nomor_registrasi, nama_lengkap, status FROM pendaftaran LIMIT 3";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>No. Reg</th><th>Nama</th><th>Status</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['nomor_registrasi']}</td>";
                    echo "<td>{$row['nama_lengkap']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='warning'>⚠️ No data found in database</p>";
            }
            
            $conn->close();
        }
        ?>
    </div>

    <div class="check-box">
        <h3>8. Test Links</h3>
        <p><a href="test_update_status.php" style="color: #007bff; text-decoration: none; font-weight: bold;">🧪 Test Update Status Function</a></p>
        <p><a href="list_mahasiswa.php" style="color: #007bff; text-decoration: none; font-weight: bold;">📋 Go to List Mahasiswa</a></p>
    </div>

    <div style="margin-top: 30px; padding: 20px; background: #d1ecf1; border-radius: 8px; border-left: 4px solid #0c5460;">
        <h3 style="margin-top: 0;">💡 Troubleshooting Tips</h3>
        <ul>
            <li><strong>If edit button error:</strong> Check bind_param type definition matches number of parameters</li>
            <li><strong>If delete button fails:</strong> Check file permissions on uploads folder</li>
            <li><strong>If status update fails:</strong> Open browser console (F12) to see JavaScript errors</li>
            <li><strong>If nothing works:</strong> Enable error display in PHP and check error logs</li>
        </ul>
    </div>
</body>
</html>
<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data semua pengajuan pinjaman
$query = "SELECT pp.id, pp.jumlah, pp.tenor_id, pp.status, pp.tanggal_pengajuan, pp.tanggal_pelunasan, pp.file_ktp, p.nama, t.durasi_bulan, t.bunga 
          FROM pengajuan_pinjaman pp 
          JOIN pengguna p ON pp.pengguna_id = p.id 
          JOIN tenor t ON pp.tenor_id = t.id 
          ORDER BY pp.tanggal_pengajuan DESC";

$riwayat_pengajuan = mysqli_query($conn, $query);

if (!$riwayat_pengajuan) {
    echo "<p>Gagal mengambil data pengajuan: " . mysqli_error($conn) . "</p>";
    exit();
}

// Ambil data tenor untuk dropdown
$tenor_query = "SELECT id, durasi_bulan FROM tenor";
$tenor_result = mysqli_query($conn, $tenor_query);
$tenors = [];
if ($tenor_result) {
    while ($row = mysqli_fetch_assoc($tenor_result)) {
        $tenors[$row['id']] = $row['durasi_bulan'];
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Daftar Pengajuan Pinjaman</title>
    <!-- Tambahkan link CSS untuk Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Arial', sans-serif;
        }
        h1, h2 {
            color: #2E8B57;
        }
        .table {
            margin-top: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .modal-content {
            background-color: #f9f9f9;
        }
        .btn-info {
            background-color: #2E8B57;
            border-color: #2E8B57;
        }
        .btn-info:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
        .modal-header {
            background-color: #2E8B57;
            color: white;
        }
        .modal-footer button {
            background-color: #2E8B57;
            border-color: #2E8B57;
        }
        .modal-footer button:hover {
            background-color: #45a049;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .back-link {
            margin-top: 30px;
            font-size: 1.2em;
        }
        .back-link a {
            color: #2E8B57;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">Daftar Pengajuan Pinjaman</h1>
    <h2>Riwayat Pengajuan Pinjaman</h2>

    <?php if (mysqli_num_rows($riwayat_pengajuan) > 0): ?>
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Pengguna</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Tenor (Bulan)</th>
                    <th>Bunga (%)</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Pelunasan</th>
                    <th>KTP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($riwayat_pengajuan)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 2, ',', '.') . "</td>";
                    echo "<td>
                            <form action='ubah_tenor.php' method='POST'>
                                <input type='hidden' name='pengajuan_id' value='" . $row['id'] . "'>
                                <select name='tenor_id' class='form-control'>";
                    foreach ($tenors as $tenor_id => $durasi) {
                        $selected = ($tenor_id == $row['tenor_id']) ? 'selected' : '';
                        echo "<option value='$tenor_id' $selected>$durasi bulan</option>";
                    }
                    echo "    </select>
                                <input type='submit' value='Ubah' class='btn btn-sm btn-success mt-2'>
                            </form>
                          </td>";
                    echo "<td>" . $row['bunga'] . "%</td>";
                    echo "<td>" . ucfirst($row['status']) . "</td>";
                    echo "<td>" . $row['tanggal_pengajuan'] . "</td>";
                    echo "<td>" . ($row['tanggal_pelunasan'] ? $row['tanggal_pelunasan'] : 'Belum ditentukan') . "</td>";
                    echo "<td>
                            <button class='btn btn-info' data-toggle='modal' data-target='#ktpModal" . $row['id'] . "'>Lihat KTP</button>
                          </td>";
                    echo "<td>
                            <a href='ubah_status.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Ubah Status</a> |
                            <a href='hapus_pengajuan.php?id=" . $row['id'] . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');\" class='btn btn-danger btn-sm'>Hapus</a>
                          </td>";
                    echo "</tr>";

                    // Modal KTP
                    echo "
                    <div class='modal fade' id='ktpModal" . $row['id'] . "' tabindex='-1' role='dialog' aria-labelledby='ktpModalLabel' aria-hidden='true'>
                        <div class='modal-dialog' role='document'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='ktpModalLabel'>KTP Pengguna " . htmlspecialchars($row['nama']) . "</h5>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>
                                <div class='modal-body'>
                                    <img src='uploads/" . htmlspecialchars($row['file_ktp']) . "' alt='KTP' class='img-fluid'>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    ";
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada pengajuan pinjaman saat ini.</p>
    <?php endif; ?>

    <div class="back-link">
        <a href="admin_dashboard.php">Kembali ke Halaman Utama</a>
    </div>

</div>

</body>
</html>

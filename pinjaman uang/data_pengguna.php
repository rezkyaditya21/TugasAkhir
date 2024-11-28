<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari database hanya untuk user (bukan admin)
$query = "SELECT id, nama, email, tanggal_lahir, alamat FROM pengguna WHERE role != 'admin' ORDER BY nama ASC";
$riwayat_pengguna = mysqli_query($conn, $query);

if (!$riwayat_pengguna) {
    echo "<p>Gagal mengambil data pengguna: " . mysqli_error($conn) . "</p>";
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
    <!-- Link Bootstrap untuk desain yang lebih baik -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        h1 {
            color: #007bff;
            text-align: center;
            margin-top: 20px;
        }

        table {
            margin-top: 30px;
        }

        table th, table td {
            text-align: center;
        }

        nav {
            margin: 20px 0;
            text-align: center;
        }

        nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Data Pengguna</h1>

        <nav>
            <a href="admin_dashboard.php" class="btn btn-link">Beranda</a>
            <a href="data_pengajuan.php" class="btn btn-link">Data Pengajuan</a>
            <a href="data_pengguna.php" class="btn btn-link">Data Pengguna</a>
            <a href="tambah_pengguna.php" class="btn btn-link">Tambah Pengguna Admin</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </nav>

        <?php if (mysqli_num_rows($riwayat_pengguna) > 0): ?>
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Lahir</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($riwayat_pengguna)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_lahir']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-warning">Tidak ada pengguna terdaftar.</p>
        <?php endif; ?>
    </div>

    <!-- Script untuk Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna
$user_id = $_SESSION['user_id'];
$admin_name = $_SESSION['nama'];

// Ambil jumlah pengajuan pinjaman
$query = "SELECT COUNT(*) as total_pengajuan FROM pengajuan_pinjaman";
$result = mysqli_query($conn, $query);
$total_pengajuan = mysqli_fetch_assoc($result)['total_pengajuan'];

// Ambil jumlah pengguna
$query = "SELECT COUNT(*) as total_pengguna FROM pengguna";
$result = mysqli_query($conn, $query);
$total_pengguna = mysqli_fetch_assoc($result)['total_pengguna'];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e8f7fc; /* Latar belakang biru muda */
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #01579b; /* Biru tua */
            margin-top: 30px;
            font-size: 2.5em;
        }

        nav {
            margin: 20px;
            background-color: #ffffff;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            display: flex;
            justify-content: space-around;
        }

        nav a {
            text-decoration: none;
            color: #01579b;
            font-size: 1.1em;
            font-weight: bold;
            padding: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        nav a:hover {
            color: white;
            background-color: #01579b;
            border-radius: 5px;
            transform: scale(1.1);
        }

        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .greeting {
            font-size: 1.4em;
            color: #8e24aa; /* Ungu */
            margin-bottom: 20px;
        }

        .info {
            margin-top: 30px;
            padding: 20px;
            background-color: #f1f8e9;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .info h2 {
            color: #388e3c; /* Hijau */
            font-size: 1.6em;
        }

        .info p {
            font-size: 1.1em;
            color: #333;
        }

        .info strong {
            color: #388e3c;
        }

        .actions {
            margin-top: 40px;
            text-align: center;
        }

        .actions a {
            text-decoration: none;
            font-weight: bold;
            padding: 12px 20px;
            border: 2px solid #01579b;
            border-radius: 5px;
            background-color: #01579b;
            color: white;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .actions a:hover {
            background-color: #ffffff;
            color: #01579b;
            border-color: #01579b;
            transform: scale(1.05);
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 0.9em;
            color: #777;
            padding: 20px 0;
            background-color: #01579b;
            color: white;
        }
    </style>
</head>
<body>

    <h1>Dashboard Admin</h1>

    <div class="container">
        <p class="greeting">Selamat datang, <?php echo htmlspecialchars($admin_name); ?>!</p>

        <nav>
            <a href="admin_dashboard.php">Beranda</a>
            <a href="data_pengajuan.php">Data Pengajuan</a>
            <a href="data_pengguna.php">Data Pengguna</a>
            <a href="tambah_pengguna.php">Tambah Pengguna Admin</a>
            <a href="logout.php">Logout</a>
        </nav>

        <div class="info">
            <h2>Informasi Utama</h2>
            <p><strong>Total Pengajuan Pinjaman:</strong> <?php echo $total_pengajuan; ?></p>
            <p><strong>Total Pengguna:</strong> <?php echo $total_pengguna; ?></p>
        </div>

        <div class="actions">
            <a href="data_pengajuan.php">Lihat semua pengajuan pinjaman</a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Pinjaman Uang Online. All rights reserved.</p>
    </div>

</body>
</html>

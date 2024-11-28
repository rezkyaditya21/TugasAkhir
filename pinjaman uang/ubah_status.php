<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Cek jika ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data pengajuan
    $query = "SELECT * FROM pengajuan_pinjaman WHERE id = '$id'";
    $pengajuan = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($pengajuan);
} else {
    echo "ID tidak ditemukan.";
    exit();
}

// Ubah status pengajuan jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $update_query = "UPDATE pengajuan_pinjaman SET status = '$status' WHERE id = '$id'";
    if (mysqli_query($conn, $update_query)) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<p>Gagal mengubah status: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Status Pengajuan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        select, button {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #007BFF;
            font-size: 18px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Ubah Status Pengajuan</h1>

        <form method="POST" action="">
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="pending" <?php if ($data['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="disetujui" <?php if ($data['status'] == 'disetujui') echo 'selected'; ?>>Disetujui</option>
                <option value="ditolak" <?php if ($data['status'] == 'ditolak') echo 'selected'; ?>>Ditolak</option>
            </select>
            <button type="submit">Ubah Status</button>
        </form>

        <div class="back-link">
            <a href="admin_dashboard.php">Kembali ke Dashboard</a>
        </div>
    </div>

</body>
</html>

<?php
mysqli_close($conn);
?>

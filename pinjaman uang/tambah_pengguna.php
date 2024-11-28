<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Proses penambahan pengguna baru
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'] ?? ''; // Menggunakan null coalescing operator
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Menggunakan hashing untuk password
    $role = 'admin'; // Set role sebagai admin

    // Cek apakah email sudah ada
    $check_email_query = "SELECT * FROM pengguna WHERE email='$email'";
    $result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($result) > 0) {
        echo "<div class='alert alert-danger'>Email sudah terdaftar. Silakan gunakan email lain.</div>";
    } else {
        // Menyimpan data pengguna baru ke database
        $insert_query = "INSERT INTO pengguna (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";

        if (mysqli_query($conn, $insert_query)) {
            echo "<div class='alert alert-success'>Pengguna baru berhasil ditambahkan.</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menambahkan pengguna: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna Admin</title>
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
            max-width: 450px; /* Mengurangi lebar card */
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px; /* Menyesuaikan padding agar lebih kompak */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px; /* Mengurangi margin antar form */
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .btn-link {
            font-size: 1.2rem;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Tambah Pengguna Admin</h1>

        <!-- Form Tambah Pengguna Admin -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-custom btn-block">Tambah Pengguna</button>
        </form>

        <h2 class="text-center mt-4"><a href="admin_dashboard.php" class="btn btn-link">Kembali ke Halaman Utama</a></h2>
    </div>

    <!-- Script untuk Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>

<?php
mysqli_close($conn);
?>

<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Ambil informasi pengguna dari database berdasarkan user_id di session
$user_id = $_SESSION['user_id'];
$query = "SELECT nama, email, alamat, tanggal_lahir, created_at FROM pengguna WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Gagal mengambil data pengguna.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #66d9a0, #42a5f5, #fdd835);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 500px; /* Ukuran lebih kecil */
            margin: 20px auto;
            padding: 10px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); /* Efek bayangan lebih besar */
            overflow: hidden;
            margin-bottom: 20px;
            border: 2px solid #28a745; /* Menambahkan border dengan warna hijau */
            background-color: #ffffff; /* Memberikan latar belakang putih pada card */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Animasi saat hover */
        }

        .card:hover {
            transform: translateY(-8px); /* Efek hover - sedikit mengangkat */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25); /* Bayangan lebih kuat saat hover */
        }

        .card-header {
            background-color: #28a745;
            color: white;
            text-align: center;
            padding: 8px;
        }

        .card-body {
            font-size: 0.9rem;
            padding: 10px;
        }

        .user-info {
            margin: 6px 0;
            display: flex;
            align-items: center;
            font-size: 0.85rem;
        }

        .user-info i {
            font-size: 1.1rem;
            color: #28a745;
            margin-right: 8px;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
            padding: 8px;
            width: 100%;
            font-size: 0.9rem;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        .btn-link {
            background-color: #007bff;
            color: white;
            padding: 8px;
            width: 100%;
            font-size: 0.9rem;
        }

        .btn-link:hover {
            background-color: #0056b3;
        }

        .logout-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 30px;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        .greeting-message {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            opacity: 0;
            animation: fadeIn 2s forwards;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="logout.php" class="btn logout-button"><i class="fas fa-sign-out-alt"></i> Logout</a>

        <!-- Greeting Message -->
        <div id="greeting" class="greeting-message"></div>

        <div class="card">
            <div class="card-header">
                <h2>Halo, <?php echo htmlspecialchars($user['nama']); ?>!</h2>
            </div>
            <div class="card-body">
                <div class="user-info">
                    <i class="fas fa-envelope"></i>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="user-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Alamat: <?php echo htmlspecialchars($user['alamat']); ?></p>
                </div>
                <div class="user-info">
                    <i class="fas fa-calendar-alt"></i>
                    <p>Tanggal Lahir: <?php echo htmlspecialchars($user['tanggal_lahir']); ?></p>
                </div>
                <div class="user-info">
                    <i class="fas fa-user-clock"></i>
                    <p>Bergabung Sejak: <?php echo htmlspecialchars($user['created_at']); ?></p>
                </div>
            </div>
            <div class="card-footer text-center">
                <h5>Ajukan Pinjaman?</h5>
                <a href="ajukan_pinjaman.php" class="btn btn-custom"><i class="fas fa-plus-circle"></i> Ajukan Pinjaman</a>
                <a href="riwayat.php" class="btn btn-link"><i class="fas fa-history"></i> Riwayat Pinjaman</a>
            </div>
        </div>
    </div>

    <script>
        function greetUser() {
            const now = new Date();
            const hour = now.getHours();
            let greetingMessage = '';

            if (hour < 12) {
                greetingMessage = 'Selamat Pagi';
            } else if (hour < 18) {
                greetingMessage = 'Selamat Siang';
            } else {
                greetingMessage = 'Selamat Malam';
            }

            document.getElementById('greeting').innerText = greetingMessage + ", " + "<?php echo htmlspecialchars($user['nama']); ?>!";
        }

        window.onload = greetUser;
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

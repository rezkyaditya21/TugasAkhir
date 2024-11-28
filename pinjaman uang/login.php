<?php
session_start();
include 'koneksi.php';

// Definisikan kredensial admin
$admin_email = "admin@example.com"; // Ganti dengan email admin yang diinginkan
$admin_password = password_hash("admin123", PASSWORD_DEFAULT); // Ganti dengan password yang diinginkan

// Cek apakah akun admin sudah ada
$query = "SELECT * FROM pengguna WHERE email='$admin_email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    // Jika tidak ada, masukkan akun admin ke dalam database
    $insert_query = "INSERT INTO pengguna (nama, email, password, role) VALUES ('Admin', '$admin_email', '$admin_password', 'admin')";
    mysqli_query($conn, $insert_query);
}

// Proses login jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek kredensial
    $query = "SELECT * FROM pengguna WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['nama'] = $row['nama'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php"); // Halaman admin
            } else {
                header("Location: user_dashboard.php"); // Halaman user
            }
            exit;
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Email tidak ditemukan!";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Pinjaman Uang Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styling umum */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #66d9a0, #42a5f5, #fdd835);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animasi Koin */
        .coin {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255, 223, 0, 0.8);
            animation: floatCoins 10s linear infinite;
            background-image: radial-gradient(circle, #ffd700, #ffea00);
        }

        @keyframes floatCoins {
            0% { transform: translateY(0) translateX(0); }
            100% { transform: translateY(-100vh) translateX(20vw); }
        }

        /* Animasi variasi koin */
        .coin:nth-child(1) { width: 25px; height: 25px; animation-duration: 8s; left: 10%; top: 30%; }
        .coin:nth-child(2) { width: 15px; height: 15px; animation-duration: 12s; left: 40%; top: 70%; }
        .coin:nth-child(3) { width: 30px; height: 30px; animation-duration: 15s; left: 80%; top: 20%; }
        .coin:nth-child(4) { width: 10px; height: 10px; animation-duration: 20s; left: 60%; top: 90%; }

        /* Kontainer Login */
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 350px;
            text-align: center;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            margin-bottom: 10px;
            color: #2e7d32;
            font-weight: bold;
            font-size: 1.8em;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            margin-bottom: 5px;
            color: #555;
            display: block;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #2e7d32;
            font-size: 1.2em;
        }

        input[type="email"],
        input[type="password"] {
            width: calc(100% - 40px);
            padding: 8px 10px 8px 40px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 1em;
        }

        input:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 5px rgba(46, 125, 50, 0.5);
            outline: none;
        }

        /* Tombol Login */
        .button {
            width: 100%;
            padding: 12px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.2s;
        }

        .button:hover {
            background-color: #1b5e20;
            transform: translateY(-2px);
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }

        .error {
            color: red;
            margin-top: 10px;
            font-size: 0.9em;
            animation: shake 0.5s ease;
        }

        .register-link {
            margin-top: 15px;
            font-size: 0.9em;
            color: #555;
        }

        .register-link a {
            color: #2e7d32;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Animasi Koin Bergerak -->
<div class="coin"></div>
<div class="coin"></div>
<div class="coin"></div>
<div class="coin"></div>

<div class="login-container">
    <h2>Masuk</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <div class="input-icon">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" required>
            </div>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" required>
            </div>
        </div>
        <button type="submit" class="button">Login</button>
    </form>

    <?php if (isset($error_message)) { ?>
        <div class="error"><?= htmlspecialchars($error_message) ?></div>
    <?php } ?>

    <div class="register-link">
        <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
    </div>
</div>

</body>
</html>

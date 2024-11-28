<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek jika email sudah terdaftar
    $check_email = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error_message = "Email sudah terdaftar!";
    } else {
        // Simpan data pengguna baru ke dalam tabel
        $query = "INSERT INTO pengguna (nama, email, password, alamat, tanggal_lahir) 
                  VALUES ('$nama', '$email', '$hashed_password', '$alamat', " . ($tanggal_lahir ? "'$tanggal_lahir'" : "NULL") . ")";
        if (mysqli_query($conn, $query)) {
            $success_message = "Registrasi berhasil! Silakan login.";
        } else {
            $error_message = "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Pinjaman Uang Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #66bb6a, #42a5f5); /* Hijau lembut dan biru */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 300px; /* Ukuran lebih kecil */
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #1976D2;
            margin-bottom: 15px; /* Mengurangi jarak */
            font-size: 1.5em; /* Ukuran font lebih kecil */
        }

        .form-group {
            margin-bottom: 12px; /* Mengurangi jarak antar input */
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 0.9em; /* Ukuran font lebih kecil */
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #1976D2;
            font-size: 1.1em;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        textarea {
            width: calc(100% - 40px);
            padding: 8px 10px 8px 35px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9em; /* Ukuran font lebih kecil */
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus {
            border-color: #1976D2;
            outline: none;
        }

        textarea {
            resize: vertical;
            height: 60px; /* Lebih pendek */
        }

        .button {
            width: 100%;
            padding: 10px;
            background-color: #1976D2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0d47a1;
        }

        .error,
        .success {
            font-size: 0.85em;
            margin-top: 12px; /* Mengurangi jarak */
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        .login-link {
            margin-top: 15px;
            font-size: 0.85em; /* Ukuran font lebih kecil */
            color: #555;
        }

        .login-link a {
            color: #1976D2;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Registrasi</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="nama">Nama:</label>
            <div class="input-icon">
                <i class="fas fa-user"></i>
                <input type="text" name="nama" id="nama" required>
            </div>
        </div>
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
        <div class="form-group">
            <label for="alamat">Alamat:</label>
            <div class="input-icon">
                <i class="fas fa-map-marker-alt"></i>
                <textarea name="alamat" id="alamat" required></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <div class="input-icon">
                <i class="fas fa-calendar-alt"></i>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir">
            </div>
        </div>
        <button type="submit" class="button">Daftar</button>
        <div class="error">
            <?php if (isset($error_message)) echo $error_message; ?>
        </div>
        <div class="success">
            <?php if (isset($success_message)) echo $success_message; ?>
        </div>
    </form>
    <div class="login-link">
        Sudah punya akun? <a href="login.php">Login Sekarang</a>
    </div>
</div>

</body>
</html>

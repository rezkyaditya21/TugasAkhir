<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Ambil data tenor dan jumlah pinjaman dari database
$tenor_options = mysqli_query($conn, "SELECT id, durasi_bulan, bunga FROM tenor");
$jumlah_pinjaman_options = mysqli_query($conn, "SELECT id, jumlah FROM jumlah_pinjaman");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $user_id = $_SESSION['user_id'];
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $tenor_id = mysqli_real_escape_string($conn, $_POST['tenor_id']);

    // Proses upload file KTP
    $target_dir = "uploads/";
    $file_ktp_name = basename($_FILES["file_ktp"]["name"]);
    $file_ktp_path = $target_dir . $file_ktp_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($file_ktp_path, PATHINFO_EXTENSION));

    // Validasi file (misalnya, hanya tipe gambar yang diperbolehkan)
    $check = getimagesize($_FILES["file_ktp"]["tmp_name"]);
    if ($check === false) {
        echo "<p>File bukan gambar KTP yang valid.</p>";
        $uploadOk = 0;
    }   

    // Cek jika file sudah ada
    if (file_exists($file_ktp_path)) {
        echo "<p>Maaf, file sudah ada.</p>";
        $uploadOk = 0;
    }

    // Batasi tipe file yang diperbolehkan
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "<p>Hanya file JPG, JPEG, dan PNG yang diperbolehkan.</p>";
        $uploadOk = 0;
    }

    // Cek jika $uploadOk adalah 0 karena kesalahan
    if ($uploadOk == 0) {
        echo "<p>Maaf, file Anda tidak berhasil diunggah.</p>";
    } else {
        if (move_uploaded_file($_FILES["file_ktp"]["tmp_name"], $file_ktp_path)) {
            // Query untuk menyimpan data pengajuan pinjaman
            $query = "INSERT INTO pengajuan_pinjaman (pengguna_id, jumlah, tenor_id, status, tanggal_pengajuan, file_ktp) 
                      VALUES ('$user_id', '$jumlah', '$tenor_id', 'pending', NOW(), '$file_ktp_name')";

            if (mysqli_query($conn, $query)) {
                // Arahkan ke halaman apply-loan.php setelah berhasil mengajukan pinjaman
                header("Location: apply-loan.php");
                exit();
            } else {
                echo "<p>Gagal mengajukan pinjaman: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p>Maaf, terjadi kesalahan saat mengunggah file KTP Anda.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Pinjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-top: 50px;
            font-size: 2.5rem;
            color: #007bff;
        }
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            font-weight: bold;
        }
        .form-container select, .form-container input {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .form-container select:focus, .form-container input:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 1.2rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .back-btn {
            text-align: center;
            margin-top: 30px;
        }
        .back-btn a {
            color: #007bff;
            font-size: 1.2rem;
            text-decoration: none;
        }
        .back-btn a:hover {
            text-decoration: underline;
        }
        #estimasi_pembayaran {
            font-size: 1.2rem;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Ajukan Pinjaman</h1>
        <div class="form-container">
            <form method="post" action="" enctype="multipart/form-data">
                <label for="jumlah">Pilih Jumlah Pinjaman:</label>
                <select name="jumlah" id="jumlah" required onchange="hitungPembayaranBulanan()">
                    <?php while ($row = mysqli_fetch_assoc($jumlah_pinjaman_options)) { ?>
                        <option value="<?php echo $row['jumlah']; ?>">
                            Rp <?php echo number_format($row['jumlah'], 2, ',', '.'); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="tenor_id">Pilih Tenor:</label>
                <select name="tenor_id" id="tenor_id" required onchange="hitungPembayaranBulanan()">
                    <?php while ($row = mysqli_fetch_assoc($tenor_options)) { ?>
                        <option value="<?php echo $row['id']; ?>" data-durasi="<?php echo $row['durasi_bulan']; ?>" data-bunga="<?php echo $row['bunga']; ?>">
                            <?php echo $row['durasi_bulan'] . " bulan - Bunga " . $row['bunga'] . "%"; ?>
                        </option>
                    <?php } ?>
                </select>

                <p id="estimasi_pembayaran">Pilih jumlah pinjaman dan tenor untuk melihat estimasi pembayaran per bulan.</p>

                <label for="file_ktp">Unggah KTP:</label>
                <input type="file" name="file_ktp" id="file_ktp" required>

                <button type="submit">Ajukan Pinjaman</button>
            </form>
        </div>

        <div class="back-btn">
            <a href="user_dashboard.php">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        // Fungsi untuk menghitung pembayaran per bulan
        function hitungPembayaranBulanan() {
            const jumlah = parseFloat(document.getElementById("jumlah").value);
            const tenorSelect = document.getElementById("tenor_id");
            const tenorOption = tenorSelect.options[tenorSelect.selectedIndex];
            const durasiBulan = parseInt(tenorOption.getAttribute("data-durasi"));
            const bunga = parseFloat(tenorOption.getAttribute("data-bunga"));

            if (!isNaN(jumlah) && !isNaN(durasiBulan) && !isNaN(bunga)) {
                const bungaBulanan = (jumlah * (bunga / 100)) / durasiBulan;
                const pembayaranPerBulan = (jumlah / durasiBulan) + bungaBulanan;
                document.getElementById("estimasi_pembayaran").innerText = 
                    "Estimasi pembayaran per bulan: Rp " + pembayaranPerBulan.toLocaleString("id-ID", {minimumFractionDigits: 2});
            } else {
                document.getElementById("estimasi_pembayaran").innerText = "Pilih jumlah pinjaman dan tenor terlebih dahulu.";
            }
        }
    </script>

</body>
</html>

<?php
mysqli_close($conn);
?>

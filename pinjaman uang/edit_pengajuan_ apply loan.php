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

// Ambil data pengajuan pinjaman yang sudah ada (jika ada)
$pengajuan_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : null;
$pengajuan_data = null;
if ($pengajuan_id) {
    $result = mysqli_query($conn, "SELECT * FROM pengajuan_pinjaman WHERE id = '$pengajuan_id' AND pengguna_id = '{$_SESSION['user_id']}'");
    $pengajuan_data = mysqli_fetch_assoc($result);
    if (!$pengajuan_data) {
        echo "<p>Pengajuan tidak ditemukan atau Anda tidak memiliki akses ke pengajuan ini.</p>";
        exit();
    }
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $user_id = $_SESSION['user_id'];
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $tenor_id = mysqli_real_escape_string($conn, $_POST['tenor_id']);

    // Proses upload file KTP (jika ada file yang diupload)
    $file_ktp_name = "";
    if (isset($_FILES["file_ktp"]) && $_FILES["file_ktp"]["error"] == 0) {
        $target_dir = "uploads/";
        $file_ktp_name = basename($_FILES["file_ktp"]["name"]);
        $file_ktp_path = $target_dir . $file_ktp_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($file_ktp_path, PATHINFO_EXTENSION));

        // Validasi file KTP (hanya gambar yang diperbolehkan)
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

        // Jika file upload berhasil, simpan data
        if ($uploadOk == 1 && move_uploaded_file($_FILES["file_ktp"]["tmp_name"], $file_ktp_path)) {
            // File berhasil diupload
        } else {
            echo "<p>Maaf, terjadi kesalahan saat mengunggah file KTP Anda.</p>";
        }
    }

    // Update data pengajuan jika sudah ada
    $query = "";
    if ($pengajuan_id) {
        $query = "UPDATE pengajuan_pinjaman SET jumlah = '$jumlah', tenor_id = '$tenor_id', file_ktp = '$file_ktp_name', tanggal_pengajuan = NOW() WHERE id = '$pengajuan_id' AND pengguna_id = '$user_id'";
    } else {
        $query = "INSERT INTO pengajuan_pinjaman (pengguna_id, jumlah, tenor_id, status, tanggal_pengajuan, file_ktp) 
                  VALUES ('$user_id', '$jumlah', '$tenor_id', 'pending', NOW(), '$file_ktp_name')";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: apply-loan.php");
        exit();
    } else {
        echo "<p>Gagal mengajukan pinjaman: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan atau Edit Pinjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container label {
            font-weight: bold;
        }

        .form-container select, .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .back-btn {
            margin-top: 20px;
            text-align: center;
        }

        .back-btn a {
            text-decoration: none;
            color: #007bff;
            font-size: 16px;
        }

        .back-btn a:hover {
            text-decoration: underline;
        }

        #estimasi_pembayaran {
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $pengajuan_id ? "Edit Pinjaman" : "Ajukan Pinjaman"; ?></h1>
        <div class="form-container">
            <form method="post" action="" enctype="multipart/form-data">
                <label for="jumlah">Pilih Jumlah Pinjaman:</label>
                <select name="jumlah" id="jumlah" required onchange="hitungPembayaranBulanan()">
                    <?php while ($row = mysqli_fetch_assoc($jumlah_pinjaman_options)) { ?>
                        <option value="<?php echo $row['jumlah']; ?>" <?php echo ($pengajuan_data && $row['jumlah'] == $pengajuan_data['jumlah']) ? 'selected' : ''; ?>>
                            Rp <?php echo number_format($row['jumlah'], 2, ',', '.'); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="tenor_id">Pilih Tenor:</label>
                <select name="tenor_id" id="tenor_id" required onchange="hitungPembayaranBulanan()">
                    <?php while ($row = mysqli_fetch_assoc($tenor_options)) { ?>
                        <option value="<?php echo $row['id']; ?>" 
                            data-durasi="<?php echo $row['durasi_bulan']; ?>" 
                            data-bunga="<?php echo $row['bunga']; ?>"
                            <?php echo ($pengajuan_data && $row['id'] == $pengajuan_data['tenor_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['durasi_bulan'] . " bulan - Bunga " . $row['bunga'] . "%"; ?>
                        </option>
                    <?php } ?>
                </select>

                <p id="estimasi_pembayaran">Pilih jumlah pinjaman dan tenor untuk melihat estimasi pembayaran per bulan.</p>

                <label for="file_ktp">Unggah KTP:</label>
                <input type="file" name="file_ktp" id="file_ktp" <?php echo !$pengajuan_data ? 'required' : ''; ?>>

                <button type="submit"><?php echo $pengajuan_id ? "Perbarui Pengajuan" : "Ajukan Pinjaman"; ?></button>
            </form>
        </div>

        <div class="back-btn">
            <a href="user_dashboard.php">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        function hitungPembayaranBulanan() {
            const jumlah = parseFloat(document.getElementById("jumlah").value);
            const tenorSelect = document.getElementById("tenor_id");
            const tenorOption = tenorSelect.options[tenorSelect.selectedIndex];
            const durasiBulan = parseInt(tenorOption.getAttribute("data-durasi"));
            const bunga = parseFloat(tenorOption.getAttribute("data-bunga"));
            const bungaTotal = jumlah * (bunga / 100);
            const totalPembayaran = jumlah + bungaTotal;
            const angsuranBulanan = totalPembayaran / durasiBulan;

            document.getElementById("estimasi_pembayaran").innerHTML = "Estimasi Pembayaran Bulanan: Rp " + angsuranBulanan.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    </script>
</body>
</html>

<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna
$user_id = $_SESSION['user_id'];

// Ambil data riwayat pengajuan pinjaman dari database
$query = "SELECT pp.id, pp.jumlah, pp.tenor_id, pp.status, pp.tanggal_pengajuan, pp.tanggal_pelunasan, pp.file_ktp, t.durasi_bulan, t.bunga 
          FROM pengajuan_pinjaman pp 
          JOIN tenor t ON pp.tenor_id = t.id 
          WHERE pp.pengguna_id = '$user_id' 
          ORDER BY pp.tanggal_pengajuan DESC";
$riwayat_pengajuan = mysqli_query($conn, $query);

if (!$riwayat_pengajuan) {
    echo "<p>Gagal mengambil data riwayat pengajuan: " . mysqli_error($conn) . "</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan Pinjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1000px;
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            text-align: center;
        }

        .modal img {
            width: 100%;
            max-width: 500px;
            border-radius: 4px;
        }

        .modal span {
            cursor: pointer;
            color: red;
            float: right;
            font-size: 20px;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Riwayat Pengajuan Pinjaman</h1>

    <?php if (mysqli_num_rows($riwayat_pengajuan) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Tenor (Bulan)</th>
                    <th>Bunga (%)</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Pelunasan</th>
                    <th>KTP</th>
                    <th>Aksi</th> <!-- Kolom untuk tombol aksi -->
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($riwayat_pengajuan)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 2, ',', '.') . "</td>";
                    echo "<td>" . $row['durasi_bulan'] . " bulan</td>";
                    echo "<td>" . $row['bunga'] . "%</td>";
                    echo "<td>" . ucfirst($row['status']) . "</td>";
                    echo "<td>" . $row['tanggal_pengajuan'] . "</td>";
                    echo "<td>" . ($row['tanggal_pelunasan'] ? $row['tanggal_pelunasan'] : 'Belum ditentukan') . "</td>";

                    // Menampilkan KTP dengan modal
                    $file_ktp = htmlspecialchars($row['file_ktp']);
                    echo "<td><a href='#' onclick=\"showModal('./uploads/$file_ktp')\"><img src='./uploads/$file_ktp' alt='KTP' style='width: 40px; height: auto;'></a></td>";

                    // Tombol menuju halaman cicilan hanya jika statusnya "disetujui"
                    if ($row['status'] == 'disetujui') {
                        echo "<td><a href='cicilan.php?pinjaman_id=" . $row['id'] . "' class='btn'>Lihat Cicilan</a></td>";
                    } else {
                        // Menampilkan pesan status jika pengajuan masih pending atau belum disetujui
                        echo "<td><span class='text-warning'>Status: " . ucfirst($row['status']) . "</span></td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning">Anda belum mengajukan pinjaman.</p>
    <?php endif; ?>

    <div class="text-center">
        <a href="user_dashboard.php" class="btn">Kembali ke Dashboard</a>
    </div>
</div>

<!-- Modal untuk menampilkan gambar KTP -->
<div id="ktpModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal()">&times;</span>
        <img id="ktpImage" src="" alt="KTP">
    </div>
</div>

<script>
    // Fungsi untuk menampilkan modal
    function showModal(imageSrc) {
        document.getElementById('ktpImage').src = imageSrc;
        document.getElementById('ktpModal').style.display = 'block';
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        document.getElementById('ktpModal').style.display = 'none';
    }

    // Menutup modal ketika klik di luar modal
    window.onclick = function (event) {
        if (event.target == document.getElementById('ktpModal')) {
            closeModal();
        }
    }
</script>

</body>
</html>

<?php
mysqli_close($conn);
?>

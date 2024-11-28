<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login dan berperan sebagai user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Inisialisasi variabel $pengajuan_terakhir
$pengajuan_terakhir = null;

// Query untuk mengambil data pengajuan terakhir dari pengguna
$pengajuan_terakhir_query = mysqli_query($conn, "SELECT * FROM pengajuan_pinjaman WHERE pengguna_id = '$user_id' ORDER BY tanggal_pengajuan DESC LIMIT 1");

// Cek apakah pengajuan ditemukan
if ($pengajuan_terakhir_query && mysqli_num_rows($pengajuan_terakhir_query) > 0) {
    $pengajuan_terakhir = mysqli_fetch_assoc($pengajuan_terakhir_query);
    $tenor_id = $pengajuan_terakhir['tenor_id'];

    // Query untuk mengambil detail tenor berdasarkan tenor_id
    $tenor_details_query = mysqli_query($conn, "SELECT durasi_bulan, bunga FROM tenor WHERE id = '$tenor_id'");

    if ($tenor_details_query && mysqli_num_rows($tenor_details_query) > 0) {
        $tenor_details = mysqli_fetch_assoc($tenor_details_query);

        // Data untuk ditampilkan di halaman
        $jumlah_pinjam = $pengajuan_terakhir['jumlah'];
        $bunga = $tenor_details['bunga'];
        $durasi_bulan = $tenor_details['durasi_bulan'];
        $total_pelunasan = $jumlah_pinjam + ($jumlah_pinjam * $bunga / 100);

        // Menghitung tanggal pelunasan berdasarkan durasi tenor
        $tanggal_pengajuan = new DateTime($pengajuan_terakhir['tanggal_pengajuan']);
        $tanggal_pelunasan = clone $tanggal_pengajuan;
        $tanggal_pelunasan->modify("+$durasi_bulan month");

        // Update tanggal pelunasan di database
        $update_query = "UPDATE pengajuan_pinjaman SET tanggal_pelunasan = '" . $tanggal_pelunasan->format("Y-m-d") . "' WHERE id = '" . $pengajuan_terakhir['id'] . "'";
        $update_result = mysqli_query($conn, $update_query);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan Pinjaman</title>
    <style>
        /* Style CSS sesuai kebutuhan */
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 500px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            color: #4a4a4a;
            line-height: 1.6;
            margin: 8px 0;
        }

        .detail {
            color: #3498db;
            font-weight: bold;
        }

        .icon {
            margin-right: 8px;
        }

        .btn {
            background-color: #3498db;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
            display: inline-block;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .ktp-image {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }

        /* Style untuk tombol lihat KTP */
        .btn-ktp {
            background-color: #2ecc71;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            margin-top: 10px;
            text-decoration: none;
            /* Menghapus garis bawah pada link */
        }

        .btn-ktp:hover {
            background-color: #27ae60;
            transform: scale(1.05);
        }

        .btn-ktp .icon {
            font-size: 16px;
            margin-right: 8px;
            /* Menambahkan sedikit jarak antara ikon dan teks */
        }

        .btn-ktp span {
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }

        .modal-content {
            width: 80%;
            max-width: 350px;
            padding: 15px;
            border-radius: 10px;
        }

        /* Gambar KTP dalam modal */
        .ktp-image {
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            max-height: 500px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Detail Pengajuan Pinjaman</h1>
        <?php if ($pengajuan_terakhir): ?>
            <p><strong><span class="icon">💵</span>Jumlah Pinjaman:</strong> <span class="detail">Rp <?php echo number_format($jumlah_pinjam, 2, ',', '.'); ?></span></p>
            <p><strong><span class="icon">📅</span>Tenor:</strong> <span class="detail"><?php echo $durasi_bulan; ?> bulan</span> dengan bunga <span class="detail"><?php echo $bunga; ?>%</span></p>
            <p><strong><span class="icon">🗓️</span>Tanggal Pengajuan:</strong> <span class="detail"><?php echo $pengajuan_terakhir['tanggal_pengajuan']; ?></span></p>
            <p><strong><span class="icon">📅</span>Tanggal Pelunasan:</strong> <span class="detail"><?php echo $tanggal_pelunasan->format("Y-m-d"); ?></span></p>
            <p><strong><span class="icon">💰</span>Total Pelunasan:</strong> <span class="detail">Rp <?php echo number_format($total_pelunasan, 2, ',', '.'); ?></span></p>
            <p><strong><span class="icon">📈</span>Estimasi Pembayaran Bulanan:</strong> <span class="detail">Rp <?php echo number_format($total_pelunasan / $durasi_bulan, 2, ',', '.'); ?></span></p>

            <a href="javascript:void(0);" class="btn-ktp" onclick="showModal('uploads/<?php echo $pengajuan_terakhir['file_ktp']; ?>')">
                <span class="icon">🆔</span> Lihat KTP
                <a href="riwayat.php" class="btn">Riwayat Pengajuan</a>
                <a href="user_dashboard.php" class="btn">Kembali ke Dashboard</a>
                <a href="edit_pengajuan_ apply loan.php?id=<?php echo $pengajuan_terakhir['id']; ?>" class="btn">Edit Pengajuan</a>

            <?php else: ?>
                <p>Anda belum mengajukan pinjaman.</p>
            <?php endif; ?>
    </div>

    <div id="ktpModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="ktpImage" class="ktp-image" src="" alt="KTP Image">
        </div>
    </div>

    <script>
        // Menampilkan modal dengan gambar KTP
        function showModal(imageUrl) {
            document.getElementById('ktpImage').src = imageUrl;
            document.getElementById('ktpModal').style.display = 'block';
        }

        // Menutup modal
        function closeModal() {
            document.getElementById('ktpModal').style.display = 'none';
        }

        // Menutup modal jika user klik di luar area modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('ktpModal')) {
                closeModal();
            }
        }
    </script>
</body>

</html>
<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data dari URL
if (!isset($_GET['pinjaman_id']) || !isset($_GET['bulan'])) {
    echo "<p>Data pembayaran tidak lengkap.</p>";
    exit();
}

$pinjaman_id = intval($_GET['pinjaman_id']);
$bulan = intval($_GET['bulan']);

// Cek apakah pinjaman ada dan disetujui
$query = "SELECT pp.jumlah, t.durasi_bulan, t.bunga 
          FROM pengajuan_pinjaman pp 
          JOIN tenor t ON pp.tenor_id = t.id 
          WHERE pp.id = '$pinjaman_id' AND pp.status = 'disetujui'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<p>Pinjaman tidak ditemukan atau belum disetujui.</p>";
    exit();
}

$data = mysqli_fetch_assoc($result);
$jumlah_pinjaman = $data['jumlah'];
$durasi_bulan = $data['durasi_bulan'];
$bunga = $data['bunga'];

// Hitung cicilan per bulan
$total_bunga = ($jumlah_pinjaman * $bunga) / 100;
$total_pinjaman = $jumlah_pinjaman + $total_bunga;
$cicilan_per_bulan = $total_pinjaman / $durasi_bulan;

// Simpan pembayaran ke dalam database
$tanggal_pembayaran = date('Y-m-d');
$query_bayar = "INSERT INTO pembayaran (pinjaman_id, jumlah_pembayaran, tanggal_pembayaran, bulan_ke) 
                VALUES ('$pinjaman_id', '$cicilan_per_bulan', '$tanggal_pembayaran', '$bulan')";

// Tampilkan notifikasi menggunakan Bootstrap
$notifikasi = "";

if (mysqli_query($conn, $query_bayar)) {
    $notifikasi = "
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Pembayaran Berhasil!</strong> Cicilan bulan ke-$bulan sebesar Rp " . number_format($cicilan_per_bulan, 2, ',', '.') . " berhasil dibayarkan.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    ";
} else {
    $notifikasi = "
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>Gagal Membayar!</strong> " . mysqli_error($conn) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    ";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <?= $notifikasi ?>
        <div class="text-center mt-4">
            <a href="cicilan.php?pinjaman_id=<?= $pinjaman_id ?>" class="btn btn-primary">Kembali ke Rincian Cicilan</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

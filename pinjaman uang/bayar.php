<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah pinjaman_id dan bulan diberikan
if (!isset($_GET['pinjaman_id']) || !isset($_GET['bulan'])) {
    echo "<p>ID pinjaman atau bulan tidak ditemukan.</p>";
    exit();
}

$pinjaman_id = $_GET['pinjaman_id'];
$bulan = $_GET['bulan'];

// Ambil data pinjaman
$query_pinjaman = "SELECT pp.jumlah, t.durasi_bulan, t.bunga, pp.tanggal_pengajuan
                   FROM pengajuan_pinjaman pp
                   JOIN tenor t ON pp.tenor_id = t.id
                   WHERE pp.id = '$pinjaman_id'";
$result_pinjaman = mysqli_query($conn, $query_pinjaman);

if (!$result_pinjaman) {
    echo "<p>Gagal mengambil data pinjaman: " . mysqli_error($conn) . "</p>";
    exit();
}

$pinjaman = mysqli_fetch_assoc($result_pinjaman);

// Hitung jumlah cicilan
$jumlah_pinjaman = $pinjaman['jumlah'];
$tenor = $pinjaman['durasi_bulan'];
$bunga = $pinjaman['bunga'];
$jumlah_cicilan = ($jumlah_pinjaman + ($jumlah_pinjaman * $bunga / 100)) / $tenor;

// Tentukan tanggal pembayaran berdasarkan bulan cicilan
$tanggal_pengajuan = new DateTime($pinjaman['tanggal_pengajuan']);
$tanggal_jatuh_tempo = clone $tanggal_pengajuan;
$tanggal_jatuh_tempo->modify("+$bulan month");
$tanggal_pembayaran = $tanggal_jatuh_tempo->format('Y-m-d');

// Cek apakah cicilan sudah dibayar
$query_check = "SELECT id FROM cicilan WHERE pengajuan_id = '$pinjaman_id' AND tanggal_pembayaran = '$tanggal_pembayaran'";
$result_check = mysqli_query($conn, $query_check);

if (mysqli_num_rows($result_check) > 0) {
    echo "<p>Cicilan bulan ini sudah dibayar.</p>";
    echo "<a href='pembayaran_cicilan.php?pinjaman_id=$pinjaman_id' class='btn btn-secondary'>Kembali ke halaman pembayaran cicilan</a>";
    exit();
}

// Masukkan data pembayaran ke tabel cicilan
$query_insert = "INSERT INTO cicilan (pengajuan_id, tanggal_pembayaran, jumlah_pembayaran) 
                 VALUES ('$pinjaman_id', '$tanggal_pembayaran', '$jumlah_cicilan')";

if (mysqli_query($conn, $query_insert)) {
    echo "<p>Pembayaran cicilan bulan ke-$bulan berhasil!</p>";
    echo "<a href='pembayaran_cicilan.php?pinjaman_id=$pinjaman_id' class='btn btn-success'>Kembali ke halaman pembayaran cicilan</a>";
} else {
    echo "<p>Gagal melakukan pembayaran: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>

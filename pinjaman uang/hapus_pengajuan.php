<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Cek jika ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mulai transaksi untuk memastikan integritas data
    mysqli_begin_transaction($conn);

    try {
        // Hapus data terkait di tabel pembayaran
        $delete_payments_query = "DELETE FROM pembayaran WHERE pinjaman_id = '$id'";
        if (!mysqli_query($conn, $delete_payments_query)) {
            throw new Exception("Gagal menghapus data pembayaran: " . mysqli_error($conn));
        }

        // Hapus pengajuan
        $delete_query = "DELETE FROM pengajuan_pinjaman WHERE id = '$id'";
        if (!mysqli_query($conn, $delete_query)) {
            throw new Exception("Gagal menghapus pengajuan: " . mysqli_error($conn));
        }

        // Commit transaksi jika semua query berhasil
        mysqli_commit($conn);
        header("Location: admin_dashboard.php");
        exit();

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($conn);
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>ID tidak ditemukan.</p>";
}

mysqli_close($conn);
?>

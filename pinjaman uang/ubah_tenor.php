<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data dari form
$pengajuan_id = $_POST['pengajuan_id'];
$tenor_id = $_POST['tenor_id'];

// Ambil durasi_bulan dari tenor yang dipilih
$tenor_query = "SELECT durasi_bulan FROM tenor WHERE id = ?";
$stmt = $conn->prepare($tenor_query);
$stmt->bind_param("i", $tenor_id);
$stmt->execute();
$result = $stmt->get_result();
$tenor_data = $result->fetch_assoc();

if ($tenor_data) {
    $durasi_bulan = $tenor_data['durasi_bulan'];

    // Ambil tanggal pengajuan
    $pengajuan_query = "SELECT tanggal_pengajuan FROM pengajuan_pinjaman WHERE id = ?";
    $stmt = $conn->prepare($pengajuan_query);
    $stmt->bind_param("i", $pengajuan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pengajuan_data = $result->fetch_assoc();

    if ($pengajuan_data) {
        $tanggal_pengajuan = new DateTime($pengajuan_data['tanggal_pengajuan']);
        // Tambahkan durasi_bulan ke tanggal_pengajuan
        $tanggal_pengajuan->modify("+$durasi_bulan month");
        $tanggal_pelunasan = $tanggal_pengajuan->format('Y-m-d');

        // Update pengajuan pinjaman dengan tenor_id dan tanggal pelunasan
        $update_query = "UPDATE pengajuan_pinjaman SET tenor_id = ?, tanggal_pelunasan = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $tenor_id, $tanggal_pelunasan, $pengajuan_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            header("Location: admin_dashboard.php?msg=Tenor berhasil diubah");
        } else {
            echo "<p>Gagal mengubah tenor: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>Data pengajuan tidak ditemukan.</p>";
    }
} else {
    echo "<p>Data tenor tidak ditemukan.</p>";
}

$stmt->close();
$conn->close();
?>

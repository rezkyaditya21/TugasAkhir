<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID pinjaman dari URL
if (!isset($_GET['pinjaman_id']) || empty($_GET['pinjaman_id'])) {
    echo "<p>ID pinjaman tidak ditemukan.</p>";
    exit();
}

$pinjaman_id = intval($_GET['pinjaman_id']);

// Ambil data pinjaman berdasarkan ID
$query = "SELECT pp.jumlah, t.durasi_bulan, t.bunga 
          FROM pengajuan_pinjaman pp 
          JOIN tenor t ON pp.tenor_id = t.id 
          WHERE pp.id = '$pinjaman_id' AND pp.status = 'disetujui'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<p>Data pinjaman tidak ditemukan atau belum disetujui.</p>";
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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Cicilan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-disabled {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Rincian Cicilan</h1>
    <p><strong>Jumlah Pinjaman:</strong> Rp <?= number_format($jumlah_pinjaman, 2, ',', '.') ?></p>
    <p><strong>Bunga:</strong> <?= $bunga ?>%</p>
    <p><strong>Durasi:</strong> <?= $durasi_bulan ?> bulan</p>
    <p><strong>Total Pinjaman (termasuk bunga):</strong> Rp <?= number_format($total_pinjaman, 2, ',', '.') ?></p>
    <p><strong>Cicilan per Bulan:</strong> Rp <?= number_format($cicilan_per_bulan, 2, ',', '.') ?></p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Jumlah Cicilan</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($bulan = 1; $bulan <= $durasi_bulan; $bulan++):
                // Cek status pembayaran dari tabel pembayaran
                $query_status = "SELECT * FROM pembayaran 
                                 WHERE pinjaman_id = '$pinjaman_id' AND bulan_ke = '$bulan'";
                $result_status = mysqli_query($conn, $query_status);
                $is_paid = mysqli_num_rows($result_status) > 0;
            ?>
                <tr>
                    <td>Bulan <?= $bulan ?></td>
                    <td>Rp <?= number_format($cicilan_per_bulan, 2, ',', '.') ?></td>
                    <td>
                        <?php if ($is_paid): ?>
                            <span class="btn btn-disabled">Lunas</span>
                        <?php else: ?>
                            <a href="bayar_cicilan.php?pinjaman_id=<?= $pinjaman_id ?>&bulan=<?= $bulan ?>" class="btn btn-success">Bayar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="riwayat.php" class="btn btn-primary">Kembali ke Riwayat</a>
    </div>
</div>

</body>
</html>

<?php
mysqli_close($conn);
?>

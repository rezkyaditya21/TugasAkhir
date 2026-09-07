# 💰 Sistem Informasi Manajemen Pinjaman Dana & Cicilan Online

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%20%7C%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-UI%20Responsive-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/License-Academic%20Project-success?style=for-the-badge" alt="License">
</p>

Aplikasi sistem informasi berbasis web untuk tata kelola pengajuan pinjaman dana, verifikasi berkas nasabah, simulasi tenor angsuran, serta pencatatan pembayaran cicilan keuangan secara otomatis dan transparan.

---

## 🌟 Fitur Utama Sistem

### 👤 Portal Nasabah / Pengguna
- **Pendaftaran & Otentikasi:** Pembuatan akun nasabah baru dan login aman dengan session PHP.
- **Formulir Pengajuan Pinjaman:** Input nominal dana, alasan peminjaman, dan unggah berkas persyaratan digital.
- **Pilihan Tenor & Simulasi Cicilan:** Perhitungan otomatis besaran angsuran bulanan sesuai periode tenor yang dipilih.
- **Pelacakan Status Real-Time:** Monitoring status pengajuan (*Menunggu Verifikasi*, *Disetujui*, *Ditolak*).
- **Riwayat & Pembayaran:** Catatan riwayat pembayaran cicilan yang transparan dan bukti bayar.

### 🛡️ Portal Administrator
- **Dashboard Eksekutif:** Ringkasan statistik pengajuan masuk, total dana terdistribusi, dan nasabah aktif.
- **Verifikasi & Approval Pengajuan:** Menyetujui atau menolak berkas pinjaman yang diajukan oleh peminjam.
- **Manajemen Tenor & Bunga:** Pengaturan skema jangka waktu pengembalian dana.
- **Manajemen Data Nasabah:** Kelola akun, status keaktifan nasabah, dan pencatatan riwayat transaksi keuangan.

---

## 📁 Struktur Direktori

`
pinjaman uang/
├── koneksi.php              # Konfigurasi database MySQL
├── login.php / logout.php   # Manajemen sesi otentikasi
├── register.php             # Pendaftaran nasabah baru
├── user_dashboard.php       # Antarmuka beranda nasabah
├── ajukan_pinjaman.php      # Formulir permohonan pinjaman baru
├── cicilan.php              # Informasi jadwal angsuran
├── bayar_cicilan.php        # Input pembayaran cicilan
├── riwayat.php              # Histori transaksi peminjam
├── admin_dashboard.php      # Panel kendali administrator
├── data_pengajuan.php       # Tabel verifikasi permohonan dana
├── data_pengguna.php        # Manajemen basis data nasabah
└── uploads/                 # Direktori penyimpanan berkas dokumen pendukung
`

---

## 🚀 Panduan Instalasi Lokal

1. **Persiapan Lingkungan:** Pastikan **XAMPP** atau **Laragon** (Apache & MySQL) sudah terpasang dan berjalan.
2. **Kloning Repositori:**
   `ash
   git clone https://github.com/rezkyaditya21/TugasAkhir.git
   `
3. Pindahkan folder pinjaman uang ke dalam direktori htdocs/ (XAMPP) atau www/ (Laragon).
4. **Konfigurasi Basis Data:**
   - Buka phpMyAdmin di http://localhost/phpmyadmin/.
   - Buat database baru (misal: db_pinjaman).
   - Sesuaikan kredensial username dan password database di berkas koneksi.php.
5. **Jalankan Aplikasi:**
   Buka peramban dan akses http://localhost/pinjaman%20uang/login.php.

---

## 👨‍💻 Pengembang
Dikembangkan oleh **Rezky Aditya Yunansyah** ([@rezkyaditya21](https://github.com/rezkyaditya21)) sebagai implementasi proyek aplikasi sistem informasi finansial.
# Aplikasi Simpan Pinjam Berbasis PHP Native

Ini adalah aplikasi web untuk manajemen simpan pinjam yang dibangun menggunakan PHP Native, tanpa menggunakan framework eksternal. Aplikasi ini dirancang dengan fokus pada keamanan, fungsionalitas lengkap, dan antarmuka yang ramah pengguna.

## Fitur Utama
- Manajemen Anggota (CRUD, Status, Profil)
- Manajemen Simpanan (Pokok, Wajib, Sukarela)
- Manajemen Pinjaman (Pengajuan, Persetujuan, Angsuran)
- Laporan Keuangan dan Administrasi
- Sistem Notifikasi (Dasar)
- Peran Pengguna (Admin & Anggota)

## Teknologi
- **Backend**: PHP Native (7.4+)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Arsitektur**: MVC Sederhana (buatan sendiri)

## Struktur Direktori
```
.
├── config/         # File konfigurasi (database, dll.)
├── controllers/    # Logika bisnis
├── core/           # Kelas inti (Router, Controller, Request)
├── models/         # Interaksi dengan database
├── public/         # Aset publik (CSS, JS) dan titik masuk (index.php)
├── utils/          # Fungsi bantuan
└── views/          # File tampilan (HTML)
```

## Instalasi
1. Clone repositori ini.
2. Buat database baru (misalnya, `simpan_pinjam`).
3. Impor file `database.sql` ke dalam database Anda.
4. Konfigurasikan koneksi database di `config/database.php`.
5. Arahkan web server Anda ke direktori `public/`.

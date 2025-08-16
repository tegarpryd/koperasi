<?php

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Ganti dengan password database Anda
define('DB_NAME', 'simpan_pinjam');

// Fungsi untuk membuat koneksi PDO
function getDbConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Pada aplikasi produksi, sebaiknya log error ini dan tampilkan pesan generik
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}

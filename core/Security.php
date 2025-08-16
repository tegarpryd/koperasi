<?php

class Security
{
    /**
     * Membuat dan menyimpan token CSRF di dalam session.
     * Jika token sudah ada, token yang sama akan dikembalikan.
     *
     * @return string
     */
    public static function generateToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Memvalidasi token CSRF yang dikirimkan.
     * Menggunakan hash_equals untuk perbandingan yang aman dari timing attack.
     *
     * @param string $token Token yang dikirim dari form.
     * @return bool True jika valid, false jika tidak.
     */
    public static function validateToken($token)
    {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            // Hapus token setelah digunakan untuk mencegah replay attacks
            unset($_SESSION['csrf_token']);
            return true;
        }
        return false;
    }
}

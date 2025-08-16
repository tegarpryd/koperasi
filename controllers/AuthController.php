<?php

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        $csrfToken = Security::generateToken();
        $this->render('auth/login', ['title' => 'Login', 'csrf_token' => $csrfToken]);
    }

    /**
     * Memproses data login.
     */
    public function login(Request $request)
    {
        // Proteksi Brute-Force
        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
            $lastAttemptTime = $_SESSION['last_login_attempt'] ?? 0;
            if (time() - $lastAttemptTime < 900) { // Blokir selama 15 menit
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit.'];
                $this->redirect('/login');
                return;
            } else {
                // Reset percobaan setelah 15 menit
                unset($_SESSION['login_attempts']);
                unset($_SESSION['last_login_attempt']);
            }
        }

        $data = $request->getBody();

        // Validasi CSRF Token
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Sesi tidak valid. Silakan coba lagi.'];
            $this->redirect('/login');
            return;
        }

        $userModel = new User();

        // Cari pengguna berdasarkan email
        $user = $userModel->findByEmail($data['email']);

        if ($user && password_verify($data['password'], $user['password'])) {
            // Jika login berhasil, hapus counter percobaan login
            unset($_SESSION['login_attempts']);
            unset($_SESSION['last_login_attempt']);

            // Simpan data pengguna ke session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            AuditLogger::log('LOGIN_SUCCESS', "User ID: {$user['id']} logged in successfully.");

            // Redirect ke dashboard yang sesuai
            if ($user['role'] === 'admin') {
                $this->redirect('/admin');
            } else {
                $this->redirect('/dashboard'); // Asumsi ada dashboard untuk member
            }
        } else {
            // Jika gagal, catat percobaan login
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_login_attempt'] = time();

            AuditLogger::log('LOGIN_FAILURE', "Failed login attempt for email: {$data['email']}.");

            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Email atau password salah.'
            ];
            $this->redirect('/login');
        }
    }

    /**
     * Menampilkan form registrasi.
     */
    public function showRegistrationForm()
    {
        $csrfToken = Security::generateToken();
        $this->render('auth/register', ['title' => 'Register', 'csrf_token' => $csrfToken]);
    }

    /**
     * Memproses data registrasi.
     */
    public function register(Request $request)
    {
        $data = $request->getBody();

        // Validasi CSRF Token
        if (!isset($data['csrf_token']) || !Security::validateToken($data['csrf_token'])) {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Sesi tidak valid. Silakan coba lagi.'];
            $this->redirect('/register');
            return;
        }

        $userModel = new User();

        // Validasi input yang lebih kompleks
        $errors = [];
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if (strlen($data['password']) < 8) {
            $errors[] = 'Password harus memiliki minimal 8 karakter.';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => implode('<br>', $errors)
            ];
            $this->redirect('/register');
            return;
        }

        // Cek jika email sudah terdaftar
        if ($userModel->findByEmail($data['email'])) {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Email sudah terdaftar.'
            ];
            $this->redirect('/register');
            return;
        }

        // Buat pengguna baru
        $userId = $userModel->create($data);

        if ($userId) {
            // Jika registrasi berhasil, redirect ke halaman login
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Registrasi berhasil! Silakan login.'
            ];
            $this->redirect('/login');
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Registrasi gagal. Silakan coba lagi.'
            ];
            $this->redirect('/register');
        }
    }

    /**
     * Proses logout.
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
}

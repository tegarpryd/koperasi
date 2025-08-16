<?php

class Controller
{
    protected $notificationData = [];
    protected $settings = [];

    public function __construct()
    {
        // Load settings for all pages
        require_once '../models/Setting.php';
        $settingModel = new Setting();
        $this->settings = $settingModel->getAllAsAssoc();

        // Load notifications for logged-in users
        if (isset($_SESSION['user']['id'])) {
            require_once '../models/Notification.php';
            $notificationModel = new Notification();
            $this->notificationData['unread_count'] = $notificationModel->countUnreadByUserId($_SESSION['user']['id']);
            $this->notificationData['unread_list'] = $notificationModel->getUnreadByUserId($_SESSION['user']['id']);
        }
    }

    /**
     * Render sebuah view dengan layout.
     *
     * @param string $view Nama file view (tanpa .php)
     * @param array $data Data yang akan di-pass ke view
     */
    public function render($view, $data = [])
    {
        // Gabungkan data view dengan data notifikasi dan pengaturan global
        $viewData = array_merge($data, [
            'notificationData' => $this->notificationData,
            'settings' => $this->settings,
            'admin_path' => $this->settings['admin_path'] ?? 'admin'
        ]);

        // Ekstrak data menjadi variabel
        extract($viewData);

        // Mulai output buffering
        ob_start();

        // Muat file view
        require_once "../views/{$view}.php";

        // Ambil konten dari buffer
        $content = ob_get_clean();

        // Muat layout utama dan masukkan konten ke dalamnya
        require_once '../views/layouts/main.php';
    }

    /**
     * Redirect ke URL lain.
     *
     * @param string $path
     */
    public function redirect($path)
    {
        header("Location: {$path}");
        exit();
    }

    /**
     * Memeriksa hak akses pengguna.
     *
     * @param array $roles Daftar peran yang diizinkan.
     */
    protected function authorize(array $roles)
    {
        // Cek apakah pengguna sudah login
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
            exit();
        }

        // Cek apakah peran pengguna diizinkan
        $userRole = $_SESSION['user']['role'];
        if (!in_array($userRole, $roles)) {
            // Jika tidak diizinkan, tampilkan halaman error 403
            http_response_code(403);
            $this->render('errors/403', ['title' => 'Akses Ditolak']);
            exit();
        }
    }
}

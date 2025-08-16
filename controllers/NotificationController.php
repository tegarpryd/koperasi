<?php

require_once '../models/Notification.php';

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        parent::__construct(); // Panggil parent constructor untuk data notifikasi
        $this->notificationModel = new Notification();
    }

    /**
     * Menampilkan semua notifikasi untuk pengguna.
     */
    public function index(Request $request)
    {
        $this->authorize(['member', 'admin']); // Bisa diakses semua user yang login

        $page = (int)($request->getQuery('page', 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $userId = $_SESSION['user']['id'];
        $notifications = $this->notificationModel->getAllByUserId($userId, $limit, $offset);
        $totalNotifications = $this->notificationModel->countAllByUserId($userId);
        $totalPages = ceil($totalNotifications / $limit);

        $this->render('notifications/index', [
            'title' => 'Semua Notifikasi',
            'notifications' => $notifications,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, $id)
    {
        $this->authorize(['member', 'admin']);
        $this->notificationModel->markAsRead($id, $_SESSION['user']['id']);
        // Redirect kembali ke halaman sebelumnya atau ke halaman notifikasi
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/notifications';
        $this->redirect($redirectUrl);
    }
}

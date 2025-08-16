<?php

require_once 'Model.php';

class Notification extends Model
{
    protected $table = 'notifications';

    /**
     * Membuat notifikasi baru untuk seorang pengguna.
     */
    public function create(int $user_id, string $message, string $link = '#')
    {
        $sql = "INSERT INTO {$this->table} (user_id, message, link) VALUES (:user_id, :message, :link)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':message' => $message,
            ':link' => $link
        ]);
    }

    /**
     * Mengambil notifikasi yang belum dibaca untuk seorang pengguna.
     */
    public function getUnreadByUserId(int $user_id, int $limit = 5)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mengambil semua notifikasi untuk seorang pengguna, dengan paginasi.
     */
    public function getAllByUserId(int $user_id, $limit, $offset)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menghitung total semua notifikasi untuk seorang pengguna.
     */
    public function countAllByUserId(int $user_id)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchColumn();
    }

    /**
     * Menghitung jumlah notifikasi yang belum dibaca.
     */
    public function countUnreadByUserId(int $user_id)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchColumn();
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(int $notification_id, int $user_id)
    {
        // user_id ditambahkan untuk memastikan user hanya bisa menandai notifikasinya sendiri
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $notification_id, ':user_id' => $user_id]);
    }
}

<?php

require_once 'Model.php';

class Member extends Model
{
    protected $table = 'members';

    /**
     * Mengambil semua data anggota dengan informasi user terkait, dengan paginasi.
     */
    public function findAll($limit, $offset)
    {
        $sql = "SELECT m.*, u.email, u.status
                FROM {$this->table} m
                JOIN users u ON m.user_id = u.id
                ORDER BY m.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menghitung total semua anggota.
     */
    public function countAll()
    {
        return $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    /**
     * Mengambil satu data anggota berdasarkan user_id.
     */
    public function findByUserId($user_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch();
    }

    /**
     * Mengambil satu data anggota berdasarkan ID.
     */
    public function findById($id)
    {
        $sql = "SELECT m.*, u.email, u.status
                FROM {$this->table} m
                JOIN users u ON m.user_id = u.id
                WHERE m.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Membuat anggota baru (user dan member) dalam satu transaksi.
     */
    public function create(array $data)
    {
        $this->db->beginTransaction();

        try {
            // 1. Buat record di tabel 'users'
            $userSql = "INSERT INTO users (email, password, role, status) VALUES (:email, :password, 'member', 'active')";
            $userStmt = $this->db->prepare($userSql);
            $userStmt->execute([
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_BCRYPT)
            ]);
            $userId = $this->db->lastInsertId();

            // 2. Buat record di tabel 'members'
            $memberSql = "INSERT INTO {$this->table} (user_id, member_code, full_name, address, phone_number, join_date)
                          VALUES (:user_id, :member_code, :full_name, :address, :phone_number, :join_date)";
            $memberStmt = $this->db->prepare($memberSql);
            $memberStmt->execute([
                ':user_id' => $userId,
                ':member_code' => 'MBR-' . time(), // Contoh kode anggota sederhana
                ':full_name' => $data['full_name'],
                ':address' => $data['address'],
                ':phone_number' => $data['phone_number'],
                ':join_date' => $data['join_date']
            ]);

            $this->db->commit();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->db->rollBack();
            // Sebaiknya log error $e->getMessage()
            return false;
        }
    }

    /**
     * Mengupdate data anggota dan status user dalam satu transaksi.
     */
    public function update($id, array $data)
    {
        $this->db->beginTransaction();
        try {
            // 1. Update tabel 'members'
            $memberSql = "UPDATE {$this->table} SET
                            full_name = :full_name,
                            address = :address,
                            phone_number = :phone_number,
                            join_date = :join_date
                          WHERE id = :id";
            $memberStmt = $this->db->prepare($memberSql);
            $memberStmt->execute([
                ':full_name' => $data['full_name'],
                ':address' => $data['address'],
                ':phone_number' => $data['phone_number'],
                ':join_date' => $data['join_date'],
                ':id' => $id
            ]);

            // 2. Update tabel 'users'
            if (isset($data['status'])) {
                $userSql = "UPDATE users u
                            JOIN {$this->table} m ON u.id = m.user_id
                            SET u.status = :status
                            WHERE m.id = :member_id";
                $userStmt = $this->db->prepare($userSql);
                $userStmt->execute([
                    ':status' => $data['status'],
                    ':member_id' => $id
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            // Sebaiknya log error $e->getMessage()
            return false;
        }
    }

    /**
     * Menghapus anggota (akan menghapus user terkait karena ON DELETE CASCADE).
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}

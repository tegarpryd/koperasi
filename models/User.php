<?php

require_once 'Model.php';

class User extends Model
{
    protected $table = 'users';

    /**
     * Mencari pengguna berdasarkan alamat email.
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Membuat pengguna baru dan menyimpan ke database.
     *
     * @param array $data
     * @return string|false
     */
    public function create($data)
    {
        // Pastikan password di-hash dengan aman
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO {$this->table} (email, password, role, status) VALUES (:email, :password, :role, :status)";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':email' => $data['email'],
            ':password' => $hashedPassword,
            ':role' => $data['role'] ?? 'member', // Default role is 'member'
            ':status' => $data['status'] ?? 'pending' // Default status is 'pending'
        ];

        if ($stmt->execute($params)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Menemukan semua pengguna dengan peran admin.
     */
    public function findAllAdmins()
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE role = 'admin'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

<?php

require_once 'Model.php';

class Setting extends Model
{
    protected $table = 'settings';

    /**
     * Mengambil semua pengaturan dan mengembalikannya sebagai associative array.
     */
    public function getAllAsAssoc()
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM {$this->table}");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $settings;
    }

    /**
     * Mengupdate beberapa pengaturan sekaligus.
     * Menerima array asosiatif [key => value].
     */
    public function updateAll(array $settings)
    {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} (setting_key, setting_value) VALUES (:key, :value)
                    ON DUPLICATE KEY UPDATE setting_value = :value";
            $stmt = $this->db->prepare($sql);

            foreach ($settings as $key => $value) {
                $stmt->execute([':key' => $key, ':value' => $value]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            // Log error $e->getMessage()
            return false;
        }
    }
}

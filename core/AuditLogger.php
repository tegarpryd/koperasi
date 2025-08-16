<?php

class AuditLogger
{
    /**
     * Mencatat sebuah aksi ke dalam tabel audit_logs.
     *
     * @param string $action Deskripsi aksi yang dilakukan.
     * @param string $details Detail tambahan, bisa berupa data JSON atau teks.
     */
    public static function log($action, $details = '')
    {
        try {
            $db = getDbConnection();
            $sql = "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip_address)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $_SESSION['user']['id'] ?? null,
                ':action' => $action,
                ':details' => $details,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
            ]);
        } catch (Exception $e) {
            // Jika logging gagal, jangan hentikan aplikasi. Cukup catat error di log server.
            error_log('AuditLogger failed: ' . $e->getMessage());
        }
    }
}

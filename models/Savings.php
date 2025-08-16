<?php

require_once 'Model.php';

class Savings extends Model
{
    protected $accounts_table = 'savings_accounts';
    protected $transactions_table = 'savings_transactions';

    public function findAccountsByMemberId($member_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->accounts_table} WHERE member_id = :member_id ORDER BY saving_type");
        $stmt->execute(['member_id' => $member_id]);
        return $stmt->fetchAll();
    }

    public function findTransactionsByMemberId($member_id, $limit = 25, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->transactions_table} WHERE member_id = :member_id ORDER BY transaction_date DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countTransactionsByMemberId($member_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->transactions_table} WHERE member_id = :member_id");
        $stmt->execute(['member_id' => $member_id]);
        return $stmt->fetchColumn();
    }

    public function createTransaction(array $data)
    {
        $this->db->beginTransaction();

        try {
            // 1. Get or create the savings account and lock the row for update
            $account = $this->getOrCreateAccountForUpdate($data['member_id'], $data['saving_type']);

            // 2. Validate transaction
            if ($data['transaction_type'] === 'withdrawal') {
                if ($account['balance'] < $data['amount']) {
                    throw new PDOException("Saldo tidak mencukupi untuk penarikan.");
                }
                $newBalance = $account['balance'] - $data['amount'];
            } else { // deposit
                $newBalance = $account['balance'] + $data['amount'];
            }

            // 3. Insert into savings_transactions
            $transSql = "INSERT INTO {$this->transactions_table} (member_id, transaction_type, saving_type, amount, description, admin_id)
                         VALUES (:member_id, :transaction_type, :saving_type, :amount, :description, :admin_id)";
            $transStmt = $this->db->prepare($transSql);
            $transStmt->execute([
                ':member_id' => $data['member_id'],
                ':transaction_type' => $data['transaction_type'],
                ':saving_type' => $data['saving_type'],
                ':amount' => $data['amount'],
                ':description' => $data['description'],
                ':admin_id' => $_SESSION['user']['id'] // Assuming admin ID is in session
            ]);

            // 4. Update the balance in savings_accounts
            $updateSql = "UPDATE {$this->accounts_table} SET balance = :balance WHERE id = :id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([':balance' => $newBalance, ':id' => $account['id']]);

            // 5. Log to audit trail (optional but good practice)
            // TODO: Implement a proper logging service

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            // In a real app, log this error: $e->getMessage()
            return false;
        }
    }

    private function getOrCreateAccountForUpdate($member_id, $saving_type)
    {
        // Find account and lock it
        $stmt = $this->db->prepare("SELECT * FROM {$this->accounts_table} WHERE member_id = :member_id AND saving_type = :saving_type FOR UPDATE");
        $stmt->execute([':member_id' => $member_id, ':saving_type' => $saving_type]);
        $account = $stmt->fetch();

        if (!$account) {
            // Create account if it doesn't exist
            $createStmt = $this->db->prepare("INSERT INTO {$this->accounts_table} (member_id, saving_type, balance) VALUES (:member_id, :saving_type, 0.00)");
            $createStmt->execute([':member_id' => $member_id, ':saving_type' => $saving_type]);

            // Fetch the newly created account and lock it
            $stmt->execute([':member_id' => $member_id, ':saving_type' => $saving_type]);
            $account = $stmt->fetch();
        }

        return $account;
    }
}

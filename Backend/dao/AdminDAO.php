<?php
require_once __DIR__ . '/BaseDAO.php';

class AdminDAO extends BaseDAO {

    public function getByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM admin WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getById(int $cod): ?array {
        $stmt = $this->db->prepare('SELECT * FROM admin WHERE cod = ? LIMIT 1');
        $stmt->execute([$cod]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function update(int $cod, array $data): bool {
        $stmt = $this->db->prepare('UPDATE admin SET nome = ?, email = ?, senha = ? WHERE cod = ?');
        return $stmt->execute([$data['nome'], $data['email'], $data['senha'], $cod]);
    }
}
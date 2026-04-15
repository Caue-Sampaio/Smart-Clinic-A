<?php
require_once __DIR__ . '/BaseDAO.php';

class MedicamentoDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM medicamento');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM medicamento WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO medicamento (nome, dosagem, forma, descricao) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['nome'],
            $data['dosagem'] ?? null,
            $data['forma'] ?? null,
            $data['descricao'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE medicamento SET nome = ?, dosagem = ?, forma = ?, descricao = ? WHERE cod = ?');
        return $stmt->execute([
            $data['nome'],
            $data['dosagem'] ?? null,
            $data['forma'] ?? null,
            $data['descricao'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM medicamento WHERE cod = ?');
        return $stmt->execute([$cod]);
    }
}

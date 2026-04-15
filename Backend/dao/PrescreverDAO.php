<?php
require_once __DIR__ . '/BaseDAO.php';

class PrescreverDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM prescrever');
        return $stmt->fetchAll();
    }

    public function getByIds($fk_receita_cod, $fk_medicamento_cod) {
        $stmt = $this->db->prepare('SELECT * FROM prescrever WHERE fk_receita_cod = ? AND fk_medicamento_cod = ?');
        $stmt->execute([$fk_receita_cod, $fk_medicamento_cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO prescrever (descricao, modo_uso, fk_receita_cod, fk_medicamento_cod) VALUES (?, ?, ?, ?)');
        return $stmt->execute([
            $data['descricao'] ?? null,
            $data['modo_uso'] ?? null,
            $data['fk_receita_cod'],
            $data['fk_medicamento_cod']
        ]);
    }

    public function update($fk_receita_cod, $fk_medicamento_cod, $data) {
        $stmt = $this->db->prepare('UPDATE prescrever SET descricao = ?, modo_uso = ? WHERE fk_receita_cod = ? AND fk_medicamento_cod = ?');
        return $stmt->execute([
            $data['descricao'] ?? null,
            $data['modo_uso'] ?? null,
            $fk_receita_cod,
            $fk_medicamento_cod
        ]);
    }

    public function delete($fk_receita_cod, $fk_medicamento_cod) {
        $stmt = $this->db->prepare('DELETE FROM prescrever WHERE fk_receita_cod = ? AND fk_medicamento_cod = ?');
        return $stmt->execute([$fk_receita_cod, $fk_medicamento_cod]);
    }
}

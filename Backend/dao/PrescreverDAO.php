<?php
require_once __DIR__ . '/BaseDAO.php';

class PrescreverDAO extends BaseDAO {

    public function getByReceita($receitaCod) {
        $stmt = $this->db->prepare('SELECT fk_medicamento_cod FROM prescrever WHERE fk_receita_cod = ?');
        $stmt->execute([$receitaCod]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getMedicamentosByReceita($receitaCod) {
        $stmt = $this->db->prepare('
            SELECT m.*, p.descricao as prescricao_descricao, p.modo_uso
            FROM prescrever p
            LEFT JOIN medicamento m ON p.fk_medicamento_cod = m.cod
            WHERE p.fk_receita_cod = ?
        ');
        $stmt->execute([$receitaCod]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO prescrever (fk_receita_cod, fk_medicamento_cod, descricao, modo_uso) VALUES (?, ?, ?, ?)');
        return $stmt->execute([
            $data['fk_receita_cod'],
            $data['fk_medicamento_cod'],
            $data['descricao'] ?? null,
            $data['modo_uso'] ?? null
        ]);
    }

    public function deleteByReceita($receitaCod) {
        $stmt = $this->db->prepare('DELETE FROM prescrever WHERE fk_receita_cod = ?');
        return $stmt->execute([$receitaCod]);
    }
}
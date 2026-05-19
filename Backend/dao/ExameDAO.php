<?php
require_once __DIR__ . '/BaseDAO.php';

class ExameDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT e.*, s.tipo as solicitacao_tipo, s.motivo as solicitacao_motivo FROM exame e LEFT JOIN solicitacao s ON e.fk_solicitacao_cod = s.cod');
        return $stmt->fetchAll();
    }

    public function getByPaciente($pacienteCod) {
        $stmt = $this->db->prepare('SELECT e.*, s.tipo as solicitacao_tipo, s.motivo as solicitacao_motivo FROM exame e LEFT JOIN solicitacao s ON e.fk_solicitacao_cod = s.cod WHERE s.fk_paciente_cod = ?');
        $stmt->execute([$pacienteCod]);
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM exame WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO exame (fk_solicitacao_cod, arquivo) VALUES (?, ?)');
        $stmt->execute([
            $data['fk_solicitacao_cod'],
            $data['arquivo'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE exame SET fk_solicitacao_cod = ?, arquivo = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_solicitacao_cod'],
            $data['arquivo'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM exame WHERE cod = ?');
        return $stmt->execute([$cod]);
    }

    public function getBySolicitacaoCod($solicitacaoCod) {
        $stmt = $this->db->prepare('SELECT * FROM exame WHERE fk_solicitacao_cod = ?');
        $stmt->execute([$solicitacaoCod]);
        return $stmt->fetchAll();
    }
}

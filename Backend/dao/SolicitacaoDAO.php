<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/BaseDAO.php';

class SolicitacaoDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM solicitacao');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM solicitacao WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO solicitacao (fk_paciente_cod, fk_medico_cod, tipo, motivo, regime, resposta, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['fk_paciente_cod'],
            $data['fk_medico_cod'] ?? null,
            $data['tipo'],
            $data['motivo'],
            $data['regime'] ?? null,
            $data['resposta'] ?? null,
            $data['status'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE solicitacao SET fk_paciente_cod = ?, fk_medico_cod = ?, tipo = ?, motivo = ?, regime = ?, resposta = ?, status = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_paciente_cod'],
            $data['fk_medico_cod'] ?? null,
            $data['tipo'],
            $data['motivo'],
            $data['regime'] ?? null,
            $data['resposta'] ?? null,
            $data['status'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM solicitacao WHERE cod = ?');
        return $stmt->execute([$cod]);
    }

    public function getByPacienteCod($pacienteCod) {
        $stmt = $this->db->prepare('SELECT * FROM solicitacao WHERE fk_paciente_cod = ?');
        $stmt->execute([$pacienteCod]);
        return $stmt->fetchAll();
    }
}

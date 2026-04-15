<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/dao/BaseDAO.php';

class AgendamentoDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT a.*, s.tipo as solicitacao_tipo, s.motivo as solicitacao_motivo FROM agendamento a LEFT JOIN solicitacao s ON a.fk_solicitacao_cod = s.cod');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT a.*, s.tipo as solicitacao_tipo, s.motivo as solicitacao_motivo FROM agendamento a LEFT JOIN solicitacao s ON a.fk_solicitacao_cod = s.cod WHERE a.cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO agendamento (fk_solicitacao_cod, data_agendamento) VALUES (?, ?)');
        $stmt->execute([
            $data['fk_solicitacao_cod'],
            $data['data_agendamento']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE agendamento SET fk_solicitacao_cod = ?, data_agendamento = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_solicitacao_cod'],
            $data['data_agendamento'],
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM agendamento WHERE cod = ?');
        return $stmt->execute([$cod]);
    }

    public function getBySolicitacaoCod($solicitacaoCod) {
        $stmt = $this->db->prepare('SELECT * FROM agendamento WHERE fk_solicitacao_cod = ?');
        $stmt->execute([$solicitacaoCod]);
        return $stmt->fetchAll();
    }
}

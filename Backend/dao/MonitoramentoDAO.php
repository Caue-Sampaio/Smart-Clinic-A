<?php
require_once __DIR__ . '/BaseDAO.php';

class MonitoramentoDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT m.*, p.nome as paciente_nome FROM monitoramento m LEFT JOIN paciente p ON m.fk_paciente_cpf = p.cpf');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM monitoramento WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO monitoramento (fk_paciente_cpf, altura, peso, imc) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['fk_paciente_cpf'],
            $data['altura'] ?? null,
            $data['peso'] ?? null,
            $data['imc'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE monitoramento SET fk_paciente_cpf = ?, altura = ?, peso = ?, imc = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_paciente_cpf'],
            $data['altura'] ?? null,
            $data['peso'] ?? null,
            $data['imc'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM monitoramento WHERE cod = ?');
        return $stmt->execute([$cod]);
    }

    public function getByPacienteCpf($cpf) {
        $stmt = $this->db->prepare('SELECT * FROM monitoramento WHERE fk_paciente_cpf = ?');
        $stmt->execute([$cpf]);
        return $stmt->fetch();
    }
}

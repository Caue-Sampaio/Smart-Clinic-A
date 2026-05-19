<?php
require_once __DIR__ . '/BaseDAO.php';

class DeclaracaoDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT d.*, p.nome as paciente_nome, m.nome as medico_nome FROM declaracao d LEFT JOIN paciente p ON d.fk_paciente_cod = p.cod LEFT JOIN medico m ON d.fk_medico_cod = m.cod');
        return $stmt->fetchAll();
    }

    public function getByPaciente($pacienteCod) {
        $stmt = $this->db->prepare('SELECT d.*, p.nome as paciente_nome, m.nome as medico_nome FROM declaracao d LEFT JOIN paciente p ON d.fk_paciente_cod = p.cod LEFT JOIN medico m ON d.fk_medico_cod = m.cod WHERE d.fk_paciente_cod = ?');
        $stmt->execute([$pacienteCod]);
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM declaracao WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO declaracao (fk_paciente_cod, fk_medico_cod, tipo, motivo, validade, data_hora) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['fk_paciente_cod'],
            $data['fk_medico_cod'],
            $data['tipo'],
            $data['motivo'],
            $data['validade'] ?? null,
            $data['data_hora'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE declaracao SET fk_paciente_cod = ?, fk_medico_cod = ?, tipo = ?, motivo = ?, validade = ?, data_hora = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_paciente_cod'],
            $data['fk_medico_cod'],
            $data['tipo'],
            $data['motivo'],
            $data['validade'] ?? null,
            $data['data_hora'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM declaracao WHERE cod = ?');
        return $stmt->execute([$cod]);
    }
}

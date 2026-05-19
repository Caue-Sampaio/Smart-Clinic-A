<?php
require_once __DIR__ . '/BaseDAO.php';

class ReceitaDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT r.*, p.nome as paciente_nome, m.nome as medico_nome FROM receita r LEFT JOIN paciente p ON r.fk_paciente_cod = p.cod LEFT JOIN medico m ON r.fk_medico_cod = m.cod');
        return $stmt->fetchAll();
    }

    public function getByPaciente($pacienteCod) {
        $stmt = $this->db->prepare('SELECT r.*, p.nome as paciente_nome, m.nome as medico_nome FROM receita r LEFT JOIN paciente p ON r.fk_paciente_cod = p.cod LEFT JOIN medico m ON r.fk_medico_cod = m.cod WHERE r.fk_paciente_cod = ?');
        $stmt->execute([$pacienteCod]);
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM receita WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO receita (fk_paciente_cod, fk_medico_cod, data_receita, tipo) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['fk_paciente_cod'],
            $data['fk_medico_cod'],
            $data['data_receita'] ?? null,
            $data['tipo'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE receita SET fk_paciente_cod = ?, fk_medico_cod = ?, data_receita = ?, tipo = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_paciente_cod'],
            $data['fk_medico_cod'],
            $data['data_receita'] ?? null,
            $data['tipo'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM receita WHERE cod = ?');
        return $stmt->execute([$cod]);
    }
}

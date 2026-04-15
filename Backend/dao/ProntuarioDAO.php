<?php
require_once __DIR__ . '/BaseDAO.php';

class ProntuarioDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT p.*, pac.nome as paciente_nome FROM prontuario p LEFT JOIN paciente pac ON p.fk_paciente_cpf = pac.cpf');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM prontuario WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO prontuario (fk_paciente_cpf, foto, tipo_sanguineo, doencas_cronicas, doencas_geneticas, doencas_autoimunes, outros) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['fk_paciente_cpf'],
            $data['foto'] ?? null,
            $data['tipo_sanguineo'] ?? null,
            $data['doencas_cronicas'] ?? null,
            $data['doencas_geneticas'] ?? null,
            $data['doencas_autoimunes'] ?? null,
            $data['outros'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE prontuario SET fk_paciente_cpf = ?, foto = ?, tipo_sanguineo = ?, doencas_cronicas = ?, doencas_geneticas = ?, doencas_autoimunes = ?, outros = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_paciente_cpf'],
            $data['foto'] ?? null,
            $data['tipo_sanguineo'] ?? null,
            $data['doencas_cronicas'] ?? null,
            $data['doencas_geneticas'] ?? null,
            $data['doencas_autoimunes'] ?? null,
            $data['outros'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM prontuario WHERE cod = ?');
        return $stmt->execute([$cod]);
    }

    public function getByPacienteCpf($cpf) {
        $stmt = $this->db->prepare('SELECT * FROM prontuario WHERE fk_paciente_cpf = ?');
        $stmt->execute([$cpf]);
        return $stmt->fetch();
    }
}

<?php
require_once __DIR__ . '/BaseDAO.php';

class ConsultaDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM consulta');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM consulta WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO consulta (fk_agendamento_cod, data_consulta, sintese) VALUES (?, ?, ?)');
        $stmt->execute([
            $data['fk_agendamento_cod'],
            $data['data_consulta'],
            $data['sintese'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE consulta SET fk_agendamento_cod = ?, data_consulta = ?, sintese = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_agendamento_cod'],
            $data['data_consulta'],
            $data['sintese'] ?? null,
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM consulta WHERE cod = ?');
        return $stmt->execute([$cod]);
    }
}

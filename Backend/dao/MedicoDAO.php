<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/dao/BaseDAO.php';

class MedicoDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT m.*, i.nome as instituicao_nome FROM medico m LEFT JOIN instituicao i ON m.fk_instituicao_cod = i.cod');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT m.*, i.nome as instituicao_nome FROM medico m LEFT JOIN instituicao i ON m.fk_instituicao_cod = i.cod WHERE m.cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO medico (fk_instituicao_cod, cpf, crm, rqe, foto, nome, email, senha, especialidade, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['fk_instituicao_cod'],
            $data['cpf'],
            $data['crm'],
            $data['rqe'] ?? null,
            $data['foto'] ?? null,
            $data['nome'],
            $data['email'],
            $data['senha'],
            $data['especialidade'] ?? null,
            $data['telefone'],
            $data['endereco']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE medico SET fk_instituicao_cod = ?, cpf = ?, crm = ?, rqe = ?, foto = ?, nome = ?, email = ?, senha = ?, especialidade = ?, telefone = ?, endereco = ? WHERE cod = ?');
        return $stmt->execute([
            $data['fk_instituicao_cod'],
            $data['cpf'],
            $data['crm'],
            $data['rqe'] ?? null,
            $data['foto'] ?? null,
            $data['nome'],
            $data['email'],
            $data['senha'],
            $data['especialidade'] ?? null,
            $data['telefone'],
            $data['endereco'],
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM medico WHERE cod = ?');
        return $stmt->execute([$cod]);
    }
}

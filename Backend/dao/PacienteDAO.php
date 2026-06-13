<?php
require_once __DIR__ . '/../controller/Database.php';
require_once __DIR__ . '/BaseDAO.php';

class PacienteDAO extends BaseDAO {

    public function getAll() {
        $stmt = $this->db->query('
            SELECT p.*, i.nome as instituicao_nome
            FROM paciente p
            LEFT JOIN instituicao i ON p.fk_instituicao_cod = i.cod
        ');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('
            SELECT p.*, i.nome as instituicao_nome
            FROM paciente p
            LEFT JOIN instituicao i ON p.fk_instituicao_cod = i.cod
            WHERE p.cod = ?
        ');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('
            INSERT INTO paciente
                (fk_instituicao_cod, cpf, nome, data_nascimento, email, senha, foto, endereco)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['fk_instituicao_cod'],
            $data['cpf'],
            $data['nome'],
            $data['data_nascimento'],
            $data['email'],
            $data['senha'],
            $data['foto'] ?? null,
            $data['endereco']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('
            UPDATE paciente SET
                fk_instituicao_cod = ?,
                cpf                = ?,
                nome               = ?,
                data_nascimento    = ?,
                email              = ?,
                senha              = ?,
                foto               = ?,
                endereco           = ?
            WHERE cod = ?
        ');
        return $stmt->execute([
            $data['fk_instituicao_cod'],
            $data['cpf'],
            $data['nome'],
            $data['data_nascimento'],
            $data['email'],
            $data['senha'],
            $data['foto'] ?? null,
            $data['endereco'],
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM paciente WHERE cod = ?');
        return $stmt->execute([$cod]);
    }
}
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/BaseDAO.php';

class InstituicaoDAO extends BaseDAO {
    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM instituicao');
        return $stmt->fetchAll();
    }

    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT * FROM instituicao WHERE cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO instituicao (cnpj, logo, email, senha, nome, telefone, endereco, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['cnpj'],
            $data['logo'],
            $data['email'],
            $data['senha'],
            $data['nome'],
            $data['telefone'],
            $data['endereco'],
            $data['status']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($cod, $data) {
        $stmt = $this->db->prepare('UPDATE instituicao SET cnpj = ?, logo = ?, email = ?, senha = ?, nome = ?, telefone = ?, endereco = ?, status = ? WHERE cod = ?');
        return $stmt->execute([
            $data['cnpj'],
            $data['logo'],
            $data['email'],
            $data['senha'],
            $data['nome'],
            $data['telefone'],
            $data['endereco'],
            $data['status'],
            $cod
        ]);
    }

    public function delete($cod) {
        $stmt = $this->db->prepare('DELETE FROM instituicao WHERE cod = ?');
        return $stmt->execute([$cod]);
    }
}

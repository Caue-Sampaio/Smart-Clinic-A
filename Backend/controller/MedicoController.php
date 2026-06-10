<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/BaseController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/MedicoDAO.php';

class MedicoController extends BaseController {
    private $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new MedicoDAO();
    }

    public function getAll() {
        return $this->dao->getAll();
    }

    public function getById($cod) {
        return $this->dao->getById($cod);
    }

    public function create($data) {
        // 1. Validação de CPF único
        if ($this->dao->getByCpf($data['cpf'])) {
            throw new Exception("Erro: O CPF '" . $data['cpf'] . "' já está cadastrado.");
        }

        // 2. Validação de CRM único
        if ($this->dao->getByCrm($data['crm'])) {
            throw new Exception("Erro: O CRM '" . $data['crm'] . "' já está cadastrado.");
        }

        // 3. Validação de E-mail único
        if ($this->dao->getByEmail($data['email'])) {
            throw new Exception("Erro: O e-mail '" . $data['email'] . "' já está em uso.");
        }

        // Se passar por todas as validações, realiza a inserção
        return $this->dao->create($data);
    }

    public function update($cod, $data) {
        return $this->dao->update($cod, $data);
    }

    public function delete($cod) {
        return $this->dao->delete($cod);
    }
}

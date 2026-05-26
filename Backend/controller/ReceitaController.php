<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../dao/ReceitaDAO.php';

class ReceitaController extends BaseController {
    private $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new ReceitaDAO();
    }

    public function getAll() {
        return $this->dao->getAll();
    }

    public function getByPaciente($pacienteCod) {
        return $this->dao->getByPaciente($pacienteCod);
    }

    public function getById($cod) {
        return $this->dao->getById($cod);
    }

    public function getDetailedById($cod) {
        return $this->dao->getDetailedById($cod);
    }

    public function create($data) {
        return $this->dao->create($data);
    }

    public function update($cod, $data) {
        return $this->dao->update($cod, $data);
    }

    public function delete($cod) {
        return $this->dao->delete($cod);
    }
}

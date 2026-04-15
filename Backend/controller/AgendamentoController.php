<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/BaseController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/dao/AgendamentoDAO.php';

class AgendamentoController extends BaseController {
    private $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new AgendamentoDAO();
    }

    public function getAll() {
        return $this->dao->getAll();
    }

    public function getById($cod) {
        return $this->dao->getById($cod);
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

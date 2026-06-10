<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/BaseController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/InstituicaoDAO.php';

class InstituicaoController extends BaseController {
    private $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new InstituicaoDAO();
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

<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../dao/PrescreverDAO.php';

class PrescreverController extends BaseController {
    private $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new PrescreverDAO();
    }

    public function getAll() {
        return $this->dao->getAll();
    }

    public function getByIds($fk_receita_cod, $fk_medicamento_cod) {
        return $this->dao->getByIds($fk_receita_cod, $fk_medicamento_cod);
    }

    public function create($data) {
        return $this->dao->create($data);
    }

    public function update($fk_receita_cod, $fk_medicamento_cod, $data) {
        return $this->dao->update($fk_receita_cod, $fk_medicamento_cod, $data);
    }

    public function delete($fk_receita_cod, $fk_medicamento_cod) {
        return $this->dao->delete($fk_receita_cod, $fk_medicamento_cod);
    }
}

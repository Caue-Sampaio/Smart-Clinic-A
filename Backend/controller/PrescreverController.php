<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../dao/PrescreverDAO.php';

class PrescreverController extends BaseController {
    private $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new PrescreverDAO();
    }

    public function getByReceita($receitaCod) {
        return $this->dao->getByReceita($receitaCod);
    }

    public function getMedicamentosByReceita($receitaCod) {
        return $this->dao->getMedicamentosByReceita($receitaCod);
    }

    public function create($data) {
        return $this->dao->create($data);
    }

    public function deleteByReceita($receitaCod) {
        return $this->dao->deleteByReceita($receitaCod);
    }
}
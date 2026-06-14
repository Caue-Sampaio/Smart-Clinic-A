<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../dao/AdminDAO.php';

class AdminController extends BaseController {
    private AdminDAO $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new AdminDAO();
    }

    public function getByEmail(string $email): ?array {
        return $this->dao->getByEmail($email);
    }

    public function getById(int $cod): ?array {
        return $this->dao->getById($cod);
    }

    public function update(int $cod, array $data): bool {
        return $this->dao->update($cod, $data);
    }
}
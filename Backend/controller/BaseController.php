<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/Database.php';

class BaseController {
    protected $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
}

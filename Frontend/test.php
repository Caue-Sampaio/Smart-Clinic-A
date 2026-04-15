<?php
require_once __DIR__ . '/../Backend/controller/PacienteController.php';
$controller = new PacienteController();
$pacientes = $controller->getAll();
var_dump($pacientes);
?>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/BaseController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/PacienteDAO.php';

class PacienteController extends BaseController {
    private $dao;

    public function __construct() {
        parent::__construct();
        $this->dao = new PacienteDAO();
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
        // Delete dependencies first
        require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/SolicitacaoDAO.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/ExameDAO.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/AgendamentoDAO.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/ProntuarioDAO.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/dao/MonitoramentoDAO.php';

        $solicitacaoDAO = new SolicitacaoDAO();
        $exameDAO = new ExameDAO();
        $agendamentoDAO = new AgendamentoDAO();
        $prontuarioDAO = new ProntuarioDAO();
        $monitoramentoDAO = new MonitoramentoDAO();

        // Get paciente to get cpf
        $paciente = $this->dao->getById($cod);
        if ($paciente) {
            $cpf = $paciente['cpf'];

            // Delete solicitacoes and their dependencies
            $solicitacoes = $solicitacaoDAO->getByPacienteCod($cod);
            foreach ($solicitacoes as $sol) {
                // Delete exames
                $exames = $exameDAO->getBySolicitacaoCod($sol['cod']);
                foreach ($exames as $ex) {
                    $exameDAO->delete($ex['cod']);
                }
                // Delete agendamentos
                $agendamentos = $agendamentoDAO->getBySolicitacaoCod($sol['cod']);
                foreach ($agendamentos as $ag) {
                    $agendamentoDAO->delete($ag['cod']);
                }
                // Delete solicitacao
                $solicitacaoDAO->delete($sol['cod']);
            }

            // Delete prontuario
            $prontuario = $prontuarioDAO->getByPacienteCpf($cpf);
            if ($prontuario) {
                $prontuarioDAO->delete($prontuario['cod']);
            }

            // Delete monitoramento
            $monitoramento = $monitoramentoDAO->getByPacienteCpf($cpf);
            if ($monitoramento) {
                $monitoramentoDAO->delete($monitoramento['cod']);
            }
        }

        // Now delete the paciente
        return $this->dao->delete($cod);
    }
}

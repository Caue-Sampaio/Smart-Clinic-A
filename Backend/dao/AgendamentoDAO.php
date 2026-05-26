<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/dao/BaseDAO.php';

class AgendamentoDAO extends BaseDAO {
    // Busca todas as consultas que foram agendadas com paciente e motivo
    public function getAll() {
        $stmt = $this->db->query('SELECT c.*, 
                                        ag.cod as agendamento_cod, 
                                        ag.data_agendamento,
                                        s.motivo, 
                                        s.fk_paciente_cod, 
                                        p.nome as paciente_nome
                                 FROM consulta c 
                                 LEFT JOIN agendamento ag ON c.fk_agendamento_cod = ag.cod 
                                 LEFT JOIN solicitacao s ON ag.fk_solicitacao_cod = s.cod 
                                 LEFT JOIN paciente p ON s.fk_paciente_cod = p.cod
                                 WHERE ag.cod IS NOT NULL');
        return $stmt->fetchAll();
    }

    // NOVO MÉTODO: Busca agendamentos apenas de um paciente específico
    public function getByPaciente($pacienteCod) {
        $stmt = $this->db->prepare('SELECT c.*, 
                                        ag.cod as agendamento_cod, 
                                        ag.data_agendamento,
                                        s.motivo, 
                                        s.fk_paciente_cod, 
                                        p.nome as paciente_nome
                                 FROM consulta c 
                                 LEFT JOIN agendamento ag ON c.fk_agendamento_cod = ag.cod 
                                 LEFT JOIN solicitacao s ON ag.fk_solicitacao_cod = s.cod 
                                 LEFT JOIN paciente p ON s.fk_paciente_cod = p.cod
                                 WHERE ag.cod IS NOT NULL AND s.fk_paciente_cod = ?');
        $stmt->execute([$pacienteCod]);
        return $stmt->fetchAll();
    }

    // Busca uma consulta agendada pelo ID
    public function getById($cod) {
        $stmt = $this->db->prepare('SELECT c.*, 
                                        ag.cod as agendamento_cod, 
                                        ag.data_agendamento,
                                        s.motivo, 
                                        s.fk_paciente_cod, 
                                        p.nome as paciente_nome
                                    FROM consulta c 
                                    LEFT JOIN agendamento ag ON c.fk_agendamento_cod = ag.cod 
                                    LEFT JOIN solicitacao s ON ag.fk_solicitacao_cod = s.cod 
                                    LEFT JOIN paciente p ON s.fk_paciente_cod = p.cod
                                    WHERE c.cod = ?');
        $stmt->execute([$cod]);
        return $stmt->fetch();
    }

    // Cria um novo agendamento
    public function create($data) {
        try {
            $stmt = $this->db->prepare('INSERT INTO consulta (fk_agendamento_cod, data_consulta, sintese) VALUES (?, ?, ?)');
            $stmt->execute([
                null, 
                $data['data_consulta'] ?? $data['data_agendamento'],
                $data['sintese'] ?? null
            ]);
            $consultaId = $this->db->lastInsertId();

            $stmtSol = $this->db->prepare('INSERT INTO solicitacao (fk_paciente_cod, tipo, motivo, status) VALUES (?, ?, ?, ?)');
            $stmtSol->execute([
                $data['fk_paciente_cod'],
                'Agendamento',
                $data['motivo'] ?? '',
                'Agendado'
            ]);
            $solicitacaoId = $this->db->lastInsertId();

            $stmt = $this->db->prepare('INSERT INTO agendamento (fk_solicitacao_cod, data_agendamento) VALUES (?, ?)');
            $stmt->execute([
                $solicitacaoId,
                $data['data_agendamento']
            ]);
            $agendamentoId = $this->db->lastInsertId();

            $stmt = $this->db->prepare('UPDATE consulta SET fk_agendamento_cod = ? WHERE cod = ?');
            $stmt->execute([$agendamentoId, $consultaId]);

            return $consultaId;
        } catch (Exception $e) {
            return false;
        }
    }

    // Atualiza um agendamento
    public function update($cod, $data) {
        try {
            $stmt = $this->db->prepare('UPDATE consulta SET data_consulta = ?, sintese = ? WHERE cod = ?');
            $stmt->execute([
                $data['data_consulta'] ?? $data['data_agendamento'],
                $data['sintese'] ?? null,
                $cod
            ]);

            $consultaStmt = $this->db->prepare('SELECT fk_agendamento_cod FROM consulta WHERE cod = ?');
            $consultaStmt->execute([$cod]);
            $consulta = $consultaStmt->fetch();

            if ($consulta && $consulta['fk_agendamento_cod']) {
                $stmt = $this->db->prepare('UPDATE agendamento SET data_agendamento = ? WHERE cod = ?');
                $stmt->execute([
                    $data['data_agendamento'],
                    $consulta['fk_agendamento_cod']
                ]);

                $agendamentoStmt = $this->db->prepare('SELECT fk_solicitacao_cod FROM agendamento WHERE cod = ?');
                $agendamentoStmt->execute([$consulta['fk_agendamento_cod']]);
                $agendamento = $agendamentoStmt->fetch();

                if ($agendamento && $agendamento['fk_solicitacao_cod']) {
                    $stmt = $this->db->prepare('UPDATE solicitacao SET fk_paciente_cod = ?, motivo = ? WHERE cod = ?');
                    $stmt->execute([
                        $data['fk_paciente_cod'] ?? null,
                        $data['motivo'] ?? null,
                        $agendamento['fk_solicitacao_cod']
                    ]);
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Deleta um agendamento
    public function delete($cod) {
        try {
            $consultaStmt = $this->db->prepare('SELECT fk_agendamento_cod FROM consulta WHERE cod = ?');
            $consultaStmt->execute([$cod]);
            $consulta = $consultaStmt->fetch();

            if ($consulta && $consulta['fk_agendamento_cod']) {
                $stmt = $this->db->prepare('DELETE FROM agendamento WHERE cod = ?');
                $stmt->execute([$consulta['fk_agendamento_cod']]);
            }

            $stmt = $this->db->prepare('DELETE FROM consulta WHERE cod = ?');
            return $stmt->execute([$cod]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getBySolicitacaoCod($solicitacaoCod) {
        $stmt = $this->db->prepare('SELECT c.* FROM consulta c 
                                    LEFT JOIN agendamento ag ON c.fk_agendamento_cod = ag.cod 
                                    WHERE ag.fk_solicitacao_cod = ?');
        $stmt->execute([$solicitacaoCod]);
        return $stmt->fetchAll();
    }
}
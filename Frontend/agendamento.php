<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
require_once __DIR__ . '/navbar.php'; 

// === AJUSTE AQUI CONFORME SEU LOGIN ===
// Compatibilidade de sessão para paciente logado
$isPaciente = (isset($_SESSION['role']) && $_SESSION['role'] === 'paciente')
    || (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'paciente');
$pacienteLogadoCod = $_SESSION['user_id'] ?? $_SESSION['paciente_cod'] ?? null;
$pacienteLogadoNome = $_SESSION['user_name'] ?? null;
// ======================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Agendamentos</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --azul: #2563eb;
            --azul-escuro: #1e40af;
            --verde: #22c55e;
            --fundo: #f1f5f9;
        }

        body {
            background: var(--fundo);
            padding-top: 80px;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, var(--azul), var(--azul-escuro));
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .card-modern {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .title {
            font-weight: 600;
            color: #0f172a;
        }

        .btn-verde {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 10px 18px;
            transition: 0.2s;
        }
        .btn-verde:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34,197,94,0.4);
        }

        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .table-modern thead {
            background: #f1f5f9;
        }
        .table-modern th {
            color: #475569;
        }
        .table-modern tr:hover {
            background: #eef2ff;
        }

        footer {
            background: transparent;
            color: #64748b;
        }
    </style>
</head>

<body>

<?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>

<?php renderNavbar(); ?>

<section class="py-5">
<div class="container-lg" style="max-width: 1200px;">

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/AgendamentoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/PacienteController.php';

$controller = new AgendamentoController();
$pacienteController = new PacienteController();

$pacientes = $pacienteController->getAll();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$agendamento = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $agendamento = $controller->getById($_GET['id']);
    if (!$agendamento) {
        echo "<p>Agendamento não encontrado.</p>";
        exit;
    }
    if ($isPaciente && !empty($pacienteLogadoCod) && $agendamento['fk_paciente_cod'] != $pacienteLogadoCod) {
        header('Location: agendamento.php');
        exit;
    }
}

// Lógica de proteção e criação de agendamentos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($isPaciente) {
        // Paciente só pode criar agendamentos para si mesmo
        if ($action == 'create') {
            $data = [
                'fk_solicitacao_cod' => null,
                'fk_paciente_cod' => $pacienteLogadoCod,
                'motivo' => $_POST['motivo'] ?? null,
                'data_agendamento' => $_POST['data_agendamento'],
                'data_consulta' => $_POST['data_agendamento'],
                'sintese' => $_POST['sintese'] ?? null
            ];
            $controller->create($data);
            header('Location: agendamento.php');
            exit;
        }
        // Pacientes não podem editar ou deletar agendamentos pelo formulário direto
        header('Location: agendamento.php');
        exit;
    }

    if (isset($_POST['delete_id'])) {
        $controller->delete($_POST['delete_id']);
        header('Location: agendamento.php');
        exit;
    } elseif ($action == 'create') {
        $data = [
            'fk_solicitacao_cod' => null,
            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
            'motivo' => $_POST['motivo'] ?? null,
            'data_agendamento' => $_POST['data_agendamento'],
            'data_consulta' => $_POST['data_agendamento'],
            'sintese' => $_POST['sintese'] ?? null
        ];
        $controller->create($data);
        header('Location: agendamento.php');
        exit;
    } elseif ($action == 'edit' && isset($_POST['cod'])) {
        $data = [
            'fk_solicitacao_cod' => null,
            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
            'motivo' => $_POST['motivo'] ?? null,
            'data_agendamento' => $_POST['data_agendamento'],
            'data_consulta' => $_POST['data_agendamento'],
            'sintese' => $_POST['sintese'] ?? null
        ];
        $controller->update($_POST['cod'], $data);
        header('Location: agendamento.php');
        exit;
    }
}

if ($action == 'list') {
?>

<div class="d-flex justify-content-between align-items-start mb-5">
    <div class="d-flex align-items-start gap-3">
        <div style="background: #dbeafe; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-calendar-event" style="font-size: 30px; color: var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Lista de Agendamentos</h2>
            <p class="text-muted mb-0"><?= $isPaciente ? 'Aqui estão seus agendamentos' : 'Gerencie os agendamentos cadastrados no sistema' ?></p>
        </div>
    </div>

    <a href="agendamento.php?action=create" class="btn btn-verde d-flex align-items-center gap-2" style="margin-top: 5px;">
        <i class="bi bi-plus-lg" style="font-size: 18px;"></i> Adicionar Novo Agendamento
    </a>
</div>

<div class="card-modern">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead style="background: #f1f5f9;">
<tr>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">CÓDIGO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">PACIENTE</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">MOTIVO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">DATA AGENDAMENTO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">SÍNTESE</th>
    <?php if (!$isPaciente) { ?>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">AÇÕES</th>
    <?php } ?>
</tr>
</thead>

<tbody>
<?php
// === FILTRO APLICADO AQUI ===
// Se o usuário logado for de fato um paciente, filtramos pelo código dele
if ($isPaciente && !empty($pacienteLogadoCod)) {
    $agendamentos = $controller->getByPaciente($pacienteLogadoCod);
} else {
    // Caso seja admin/médico ou a sessão não esteja preenchida, mostra tudo
    $agendamentos = $controller->getAll();
}

if (empty($agendamentos)) {
    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Nenhum agendamento encontrado.</td></tr>";
}

foreach ($agendamentos as $ag) {
    $sintese = isset($ag['sintese']) ? substr($ag['sintese'], 0, 50) . (strlen($ag['sintese']) > 50 ? '...' : '') : '-';
    $motivo = isset($ag['motivo']) ? substr($ag['motivo'], 0, 50) . (strlen($ag['motivo']) > 50 ? '...' : '') : '-';
    echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$ag['cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'><strong>{$ag['paciente_nome']}</strong> (Cód: {$ag['fk_paciente_cod']})</td>";
    echo "<td style='padding: 15px; color: #0f172a; font-size: 13px;'>{$motivo}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'><i class='bi bi-calendar' style='color: var(--azul); margin-right: 8px;'></i>" . date('d/m/Y H:i', strtotime($ag['data_consulta'])) . "</td>";
    echo "<td style='padding: 15px; color: #0f172a; font-size: 13px;'>{$sintese}</td>";
    
    // Esconde as ações de editar/deletar se for paciente
    if (!$isPaciente) {
        echo "<td style='padding: 15px;'>
            <a href='?action=edit&id={$ag['cod']}' class='btn btn-sm me-2' style='background: #3b82f6; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
                <i class='bi bi-pencil' style='font-size: 14px;'></i> Editar
            </a>
            <form method='POST' style='display:inline;'>
                <input type='hidden' name='delete_id' value='{$ag['cod']}'>
                <button class='btn btn-sm' style='background: #ef4444; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
                    <i class='bi bi-trash' style='font-size: 14px;'></i> Deletar
                </button>
            </form>
        </td>";
    }
    echo "</tr>";
}
?>
</tbody>
</table>
</div>
</div>

<?php } else { 
    // Proteção de Rota: pacientes não podem acessar o formulário de edição
    if ($isPaciente && $action == 'edit') {
        header('Location: agendamento.php');
        exit;
    }
?>

<div class="card-modern">
<h2 class="title mb-4"><?= $action == 'create' ? 'Novo Agendamento' : 'Editar Agendamento' ?></h2>

<form method="POST">

<?php if ($action == 'edit') { ?>
<input type="hidden" name="cod" value="<?= $agendamento['cod'] ?>">
<?php } ?>

<div class="mb-3">
    <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Paciente</label>
    <?php if ($isPaciente) { ?>
        <input type="hidden" name="fk_paciente_cod" value="<?= htmlspecialchars($pacienteLogadoCod) ?>">
        <div class="form-control" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px; background: #f8fafc;" readonly>
            <?= htmlspecialchars($pacienteLogadoNome ?? 'Paciente logado') ?> (Cód: <?= htmlspecialchars($pacienteLogadoCod) ?>)
        </div>
    <?php } else { ?>
        <select name="fk_paciente_cod" class="form-control" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;" required>
            <option value="">-- Selecione um Paciente --</option>
            <?php foreach ($pacientes as $pac) { ?>
            <option value="<?= $pac['cod'] ?>" <?= $action == 'edit' && $agendamento['fk_paciente_cod'] == $pac['cod'] ? 'selected' : '' ?>><?= $pac['nome'] ?> (Cód: <?= $pac['cod'] ?>)</option>
            <?php } ?>
        </select>
    <?php } ?>
</div>

<div class="mb-3">
    <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Motivo</label>
    <input type="text" name="motivo" class="form-control" placeholder="Digite o motivo da consulta..." value="<?= $action == 'edit' ? htmlspecialchars($agendamento['motivo'] ?? '') : '' ?>" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;" required>
</div>

<div class="mb-4">
    <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Data do Agendamento</label>
    <input class="form-control" type="datetime-local" name="data_agendamento" value="<?= $action == 'edit' ? date('Y-m-d\TH:i', strtotime($agendamento['data_agendamento'] ?? $agendamento['data_consulta'])) : '' ?>" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;" required>
</div>

<div class="mb-4">
    <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Síntese da Consulta</label>
    <textarea class="form-control" name="sintese" rows="4" placeholder="Digite a síntese ou observações da consulta..." style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;"><?= $action == 'edit' ? htmlspecialchars($agendamento['sintese'] ?? '') : '' ?></textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-verde">
        <i class="bi bi-check-lg"></i> Salvar
    </button>
    <a href="agendamento.php" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 18px;">
        <i class="bi bi-x-lg"></i> Cancelar
    </a>
</div>

</form>
</div>

<?php } ?>

</div>
</section>

<footer style="background: transparent; color: #64748b; padding: 20px 0;">
    <div class="container-lg text-center">
        <p class="mb-0">© 2026 SMART CLINIC - Todos os direitos reservados</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php ob_end_flush(); ?>
</body>
</html>
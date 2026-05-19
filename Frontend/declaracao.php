<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
require_once __DIR__ . '/navbar.php'; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Declarações</title>
    
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
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/DeclaracaoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/PacienteController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/MedicoController.php';
$controller = new DeclaracaoController();
$pacienteController = new PacienteController();
$medicoController = new MedicoController();
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$isPaciente = isset($_SESSION['role']) && $_SESSION['role'] === 'paciente';
$pacienteLogadoCod = $_SESSION['user_id'] ?? null;

if ($isPaciente && ($action == 'create' || $action == 'edit')) {
    header('Location: declaracao.php');
    exit;
}

$pacientes = $pacienteController->getAll();
$medicos = $medicoController->getAll();
$declaracao = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $declaracao = $controller->getById($_GET['id']);
    if (!$declaracao) {
        echo "<p>Declaração não encontrada.</p>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($isPaciente) {
        header('Location: declaracao.php');
        exit;
    }
    if (isset($_POST['delete_id'])) {
        $controller->delete($_POST['delete_id']);
        header('Location: declaracao.php');
        exit;
    } elseif ($action == 'create') {
        $data = [
            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
            'fk_medico_cod' => $_POST['fk_medico_cod'],
            'tipo' => $_POST['tipo'],
            'motivo' => $_POST['motivo'],
            'validade' => $_POST['validade'] ?: null,
            'data_hora' => $_POST['data_hora'] ?: null
        ];
        $controller->create($data);
        header('Location: declaracao.php');
        exit;
    } elseif ($action == 'edit' && isset($_POST['cod'])) {
        $data = [
            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
            'fk_medico_cod' => $_POST['fk_medico_cod'],
            'tipo' => $_POST['tipo'],
            'motivo' => $_POST['motivo'],
            'validade' => $_POST['validade'] ?: null,
            'data_hora' => $_POST['data_hora'] ?: null
        ];
        $controller->update($_POST['cod'], $data);
        header('Location: declaracao.php');
        exit;
    }
}

if ($action == 'list') {
?>

<div class="d-flex justify-content-between align-items-start mb-5">
    <div class="d-flex align-items-start gap-3">
        <div style="background: #dbeafe; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-file-earmark-text" style="font-size: 30px; color: var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Lista de Declarações</h2>
            <p class="text-muted mb-0">Gerencie as declarações cadastradas no sistema</p>
        </div>
    </div>

    <?php if (!$isPaciente) { ?>
    <a href="declaracao.php?action=create" class="btn btn-verde d-flex align-items-center gap-2" style="margin-top: 5px;">
        <i class="bi bi-plus-lg" style="font-size: 18px;"></i> Adicionar Nova Declaração
    </a>
    <?php } ?>
</div>

<div class="card-modern">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead style="background: #f1f5f9;">
<tr>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">CÓDIGO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">PACIENTE</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">MÉDICO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">TIPO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">MOTIVO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">VALIDADE</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">AÇÕES</th>
</tr>
</thead>

<tbody>
<?php
if ($isPaciente && !empty($pacienteLogadoCod)) {
    $declaracoes = $controller->getByPaciente($pacienteLogadoCod);
} else {
    $declaracoes = $controller->getAll();
}
foreach ($declaracoes as $dec) {
    echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$dec['cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$dec['paciente_nome']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$dec['medico_nome']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$dec['tipo']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$dec['motivo']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$dec['validade']}</td>";
    echo "<td style='padding: 15px;'>";
    if (!$isPaciente) {
        echo "<a href='?action=edit&id={$dec['cod']}' class='btn btn-sm me-2' style='background: #3b82f6; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
            <i class='bi bi-pencil' style='font-size: 14px;'></i> Editar
        </a>";
        echo "<form method='POST' style='display:inline;'>
            <input type='hidden' name='delete_id' value='{$dec['cod']}'>
            <button class='btn btn-sm' style='background: #ef4444; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
                <i class='bi bi-trash' style='font-size: 14px;'></i> Deletar
            </button>
        </form>";
    }
    echo "</td>";
    echo "</tr>";
}
?>
</tbody>
</table>
</div>
</div>

<?php } else { ?>

<div class="card-modern">
<h2 class="title mb-3"><?= $action == 'create' ? 'Nova Declaração' : 'Editar Declaração' ?></h2>
<p class="text-muted mb-4"><?php echo $action == 'create' ? 'Preencha os dados para adicionar uma declaração.' : 'Atualize os dados da declaração.'; ?></p>

<form method="POST">

<?php if ($action == 'edit') { ?>
<input type="hidden" name="cod" value="<?= $declaracao['cod'] ?>">
<?php } ?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Paciente</label>
        <select name="fk_paciente_cod" class="form-control form-control-lg" <?php echo $isPaciente ? 'disabled' : ''; ?>>
        <?php foreach ($pacientes as $pac) { 
            $selected = ($action == 'edit' && $pac['cod'] == ($declaracao['fk_paciente_cod'] ?? '')) ? 'selected' : '';
        ?>
        <option value="<?= $pac['cod'] ?>" <?= $selected ?>><?= $pac['nome'] ?></option>
        <?php } ?>
        </select>
        <?php if ($isPaciente): ?>
            <input type="hidden" name="fk_paciente_cod" value="<?= htmlspecialchars($declaracao['fk_paciente_cod'] ?? $pacienteLogadoCod) ?>">
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label">Médico</label>
        <select name="fk_medico_cod" class="form-control form-control-lg">
        <?php foreach ($medicos as $med) { 
            $selected = ($action == 'edit' && $med['cod'] == ($declaracao['fk_medico_cod'] ?? '')) ? 'selected' : '';
        ?>
        <option value="<?= $med['cod'] ?>" <?= $selected ?>><?= $med['nome'] ?></option>
        <?php } ?>
        </select>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-6">
        <label class="form-label">Tipo</label>
        <input class="form-control form-control-lg" name="tipo" value="<?= $action == 'edit' ? htmlspecialchars($declaracao['tipo']) : '' ?>" placeholder="Tipo de declaração">
    </div>

    <div class="col-md-6">
        <label class="form-label">Motivo</label>
        <input class="form-control form-control-lg" name="motivo" value="<?= $action == 'edit' ? htmlspecialchars($declaracao['motivo']) : '' ?>" placeholder="Motivo da declaração">
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-6">
        <label class="form-label">Validade</label>
        <input class="form-control form-control-lg" type="date" name="validade" value="<?= $action == 'edit' ? htmlspecialchars($declaracao['validade']) : '' ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Data/Hora</label>
        <input class="form-control form-control-lg" type="datetime-local" name="data_hora" value="<?= $action == 'edit' ? htmlspecialchars($declaracao['data_hora']) : '' ?>">
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-verde btn-lg">
        <i class="bi bi-check-lg"></i> Salvar
    </button>
    <a href="declaracao.php" class="btn btn-secondary btn-lg">
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

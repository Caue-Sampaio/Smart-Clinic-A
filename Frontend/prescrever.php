<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Prescrever</title>
    
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
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/PrescreverController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/ReceitaController.php';
$require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/MedicamentoController.php';
$controller = new PrescreverController();
$receitaController = new ReceitaController();
$medicamentoController = new MedicamentoController();
$isPaciente = isset($_SESSION['role']) && $_SESSION['role'] === 'paciente';
$pacienteLogadoCod = $_SESSION['user_id'] ?? null;

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$prescrever = null;

if ($action == 'edit' && isset($_GET['id1']) && isset($_GET['id2'])) {
    $prescrever = $controller->getByIds($_GET['id1'], $_GET['id2']);
    if (!$prescrever) {
        echo "<p>Prescrever não encontrado.</p>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($isPaciente) {
        header('Location: prescrever.php');
        exit;
    }
    if (isset($_POST['delete_id1']) && isset($_POST['delete_id2'])) {
        $controller->delete($_POST['delete_id1'], $_POST['delete_id2']);
        header('Location: prescrever.php');
        exit;
    } elseif ($action == 'create') {
        $data = [
            'fk_receita_cod' => $_POST['fk_receita_cod'],
            'fk_medicamento_cod' => $_POST['fk_medicamento_cod'],
            'descricao' => $_POST['descricao'],
            'modo_uso' => $_POST['modo_uso']
        ];
        $controller->create($data);
        header('Location: prescrever.php');
        exit;
    } elseif ($action == 'edit' && isset($_POST['fk_receita_cod']) && isset($_POST['fk_medicamento_cod'])) {
        $data = [
            'descricao' => $_POST['descricao'],
            'modo_uso' => $_POST['modo_uso']
        ];
        $controller->update($_POST['fk_receita_cod'], $_POST['fk_medicamento_cod'], $data);
        header('Location: prescrever.php');
        exit;
    }
}

if ($isPaciente && ($action == 'create' || $action == 'edit')) {
    header('Location: prescrever.php');
    exit;
}

if ($action == 'list') {
?>

<div class="d-flex justify-content-between align-items-start mb-5">
    <div class="d-flex align-items-start gap-3">
        <div style="background: #dbeafe; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-prescription2" style="font-size: 30px; color: var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Lista de Prescrever</h2>
            <p class="text-muted mb-0">Gerencie as prescrições cadastradas no sistema</p>
        </div>
    </div>

    <?php if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente'): ?>
    <a href="prescrever.php?action=create" class="btn btn-verde d-flex align-items-center gap-2" style="margin-top: 5px;">
        <i class="bi bi-plus-lg" style="font-size: 18px;"></i> Adicionar Novo Prescrever
    </a>
    <?php endif; ?>
</div>

<div class="card-modern">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead style="background: #f1f5f9;">
<tr>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">RECEITA</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">MEDICAMENTO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">DESCRIÇÃO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">MODO USO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">AÇÕES</th>
</tr>
</thead>

<tbody>
<?php
$prescrivers = $controller->getAll();
if ($isPaciente && !empty($pacienteLogadoCod)) {
    $minhasReceitas = array_column($receitaController->getByPaciente($pacienteLogadoCod), 'cod');
    $prescrivers = array_filter($prescrivers, function($pres) use ($minhasReceitas) {
        return in_array($pres['fk_receita_cod'], $minhasReceitas);
    });
}
foreach ($prescrivers as $pres) {
    echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$pres['fk_receita_cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$pres['fk_medicamento_cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$pres['descricao']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$pres['modo_uso']}</td>";
    echo "<td style='padding: 15px;'>";
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente') {
        echo "<a href='?action=edit&id1={$pres['fk_receita_cod']}&id2={$pres['fk_medicamento_cod']}' class='btn btn-sm me-2' style='background: #3b82f6; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
            <i class='bi bi-pencil' style='font-size: 14px;'></i> Editar
        </a>";
        echo "<form method='POST' style='display:inline;'>
            <input type='hidden' name='delete_id1' value='{$pres['fk_receita_cod']}'>
            <input type='hidden' name='delete_id2' value='{$pres['fk_medicamento_cod']}'>
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
<h2 class="title mb-4"><?= $action == 'create' ? 'Novo Prescrever' : 'Editar Prescrever' ?></h2>

<form method="POST">

<?php if ($action == 'edit') { ?>
<input type="hidden" name="fk_receita_cod" value="<?= $prescrever['fk_receita_cod'] ?>">
<input type="hidden" name="fk_medicamento_cod" value="<?= $prescrever['fk_medicamento_cod'] ?>">
<?php } ?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Receita</label>
        <select name="fk_receita_cod" class="form-control form-control-lg" <?= $action == 'edit' ? 'disabled' : '' ?> required>
            <option value="">Selecione uma receita</option>
            <?php $receitas = $receitaController->getAll(); foreach ($receitas as $r) {
                $sel = ($action == 'edit' && $r['cod'] == ($prescrever['fk_receita_cod'] ?? '')) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($r['cod']) . "' $sel>" . htmlspecialchars($r['cod'] . ' - ' . ($r['paciente_nome'] ?? '')) . "</option>";
            } ?>
        </select>
        <?php if ($action == 'edit'): ?><input type="hidden" name="fk_receita_cod" value="<?= $prescrever['fk_receita_cod'] ?>"><?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label">Medicamento</label>
        <select name="fk_medicamento_cod" class="form-control form-control-lg" <?= $action == 'edit' ? 'disabled' : '' ?> required>
            <option value="">Selecione um medicamento</option>
            <?php $meds = $medicamentoController->getAll(); foreach ($meds as $m) {
                $sel = ($action == 'edit' && $m['cod'] == ($prescrever['fk_medicamento_cod'] ?? '')) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($m['cod']) . "' $sel>" . htmlspecialchars($m['nome']) . "</option>";
            } ?>
        </select>
        <?php if ($action == 'edit'): ?><input type="hidden" name="fk_medicamento_cod" value="<?= $prescrever['fk_medicamento_cod'] ?>"><?php endif; ?>
    </div>
</div>

<div class="mb-3 mt-3">
    <label class="form-label">Descrição</label>
    <textarea class="form-control" name="descricao" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($prescrever['descricao']) : ''; ?></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Modo Uso</label>
    <textarea class="form-control" name="modo_uso" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($prescrever['modo_uso']) : ''; ?></textarea>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-verde btn-lg"><i class="bi bi-check-lg"></i> Salvar</button>
    <a href="prescrever.php" class="btn btn-secondary btn-lg">Cancelar</a>
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
</body>
</html>

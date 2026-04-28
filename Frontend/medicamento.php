<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/MedicamentoController.php';
$controller = new MedicamentoController();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$medicamento = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $medicamento = $controller->getById($_GET['id']);
    if (!$medicamento) {
        echo "<p>Medicamento não encontrado.</p>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        $controller->delete($_POST['delete_id']);
        header('Location: medicamento.php');
        exit;
    } elseif ($action == 'create') {
        $data = [
            'nome' => $_POST['nome'],
            'dosagem' => $_POST['dosagem'],
            'forma' => $_POST['forma'],
            'descricao' => $_POST['descricao']
        ];
        $controller->create($data);
        header('Location: medicamento.php');
        exit;
    } elseif ($action == 'edit' && isset($_POST['cod'])) {
        $data = [
            'nome' => $_POST['nome'],
            'dosagem' => $_POST['dosagem'],
            'forma' => $_POST['forma'],
            'descricao' => $_POST['descricao']
        ];
        $controller->update($_POST['cod'], $data);
        header('Location: medicamento.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Medicamentos</title>
    
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

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #1e3a8a 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
<div class="container-fluid px-5">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
        <img src="../img/logob.png" alt="Logo" class="me-2" style="height: 40px;">
        SMART CLINIC
    </a>

    <div class="ms-auto d-flex gap-3 align-items-center">
        <div class="dropdown">
            <button class="btn btn-link text-white text-decoration-none d-flex align-items-center gap-2" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: 500;">
                <i class="bi bi-gear" style="font-size: 20px;"></i>
                Gerenciar Dados
                <i class="bi bi-chevron-down" style="font-size: 16px;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="paciente.php"><i class="bi bi-person me-2"></i>Pacientes</a></li>
                <li><a class="dropdown-item" href="medico.php"><i class="bi bi-stethoscope me-2"></i>Médicos</a></li>
                <li><a class="dropdown-item" href="instituicao.php"><i class="bi bi-building me-2"></i>Instituições</a></li>
                <li><a class="dropdown-item" href="medicamento.php"><i class="bi bi-capsule me-2"></i>Medicamentos</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
            </ul>
        </div>
        <a href="#" class="btn btn-light rounded-pill px-4 d-flex align-items-center gap-2" style="font-weight: 500;">
            <i class="bi bi-calendar-check" style="font-size: 18px;"></i>
            Agendar Consulta
        </a>
    </div>
</div>
</nav>

<section class="py-5">
<div class="container-lg" style="max-width: 1200px;">

<?php
if ($action == 'list') {
?>

<div class="d-flex justify-content-between align-items-start mb-5">
    <div class="d-flex align-items-start gap-3">
        <div style="background: #dbeafe; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-capsule" style="font-size: 30px; color: var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Lista de Medicamentos</h2>
            <p class="text-muted mb-0">Gerencie os medicamentos cadastrados no sistema</p>
        </div>
    </div>

    <a href="medicamento.php?action=create" class="btn btn-verde d-flex align-items-center gap-2" style="margin-top: 5px;">
        <i class="bi bi-plus-lg" style="font-size: 18px;"></i> Adicionar Novo Medicamento
    </a>
</div>

<div class="card-modern">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead style="background: #f1f5f9;">
<tr>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">CÓDIGO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">NOME</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">DOSAGEM</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">FORMA</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">DESCRIÇÃO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">AÇÕES</th>
</tr>
</thead>

<tbody>
<?php
$medicamentos = $controller->getAll();
foreach ($medicamentos as $med) {
    echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$med['cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$med['nome']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$med['dosagem']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$med['forma']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$med['descricao']}</td>";
    echo "<td style='padding: 15px;'>
        <a href='?action=edit&id={$med['cod']}' class='btn btn-sm me-2' style='background: #3b82f6; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
            <i class='bi bi-pencil' style='font-size: 14px;'></i> Editar
        </a>
        <form method='POST' style='display:inline;'>
            <input type='hidden' name='delete_id' value='{$med['cod']}'>
            <button class='btn btn-sm' style='background: #ef4444; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
                <i class='bi bi-trash' style='font-size: 14px;'></i> Deletar
            </button>
        </form>
    </td>";
    echo "</tr>";
}
?>
</tbody>
</table>
</div>
</div>

<?php } else { ?>

<div class="card-modern">
<h2 class="title mb-4"><?= $action == 'create' ? 'Novo Medicamento' : 'Editar Medicamento' ?></h2>

<form method="POST">

<?php if ($action == 'edit') { ?>
<input type="hidden" name="cod" value="<?= $medicamento['cod'] ?>">
<?php } ?>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Nome</label>
        <input class="form-control" name="nome" value="<?= $action == 'edit' ? $medicamento['nome'] : '' ?>" placeholder="Nome do medicamento" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>

    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Dosagem</label>
        <input class="form-control" name="dosagem" value="<?= $action == 'edit' ? $medicamento['dosagem'] : '' ?>" placeholder="Ex: 500mg" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Forma</label>
        <input class="form-control" name="forma" value="<?= $action == 'edit' ? $medicamento['forma'] : '' ?>" placeholder="Ex: Comprimido, Cápsula, Xarope" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>
</div>

<div class="mb-4">
    <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Descrição</label>
    <textarea class="form-control" name="descricao" placeholder="Descrição do medicamento..." rows="3" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;"><?= $action == 'edit' ? $medicamento['descricao'] : '' ?></textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-verde">
        <i class="bi bi-check-lg"></i> Salvar
    </button>
    <a href="medicamento.php" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 18px;">
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
</body>
</html>

<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Médicos</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --azul: #2563eb;
            --azul-escuro: #1e40af;
            --verde: #22c55e;
            --fundo: #f1f5f9;
        }

        /* FUNDO */
        body {
            background: var(--fundo);
            padding-top: 80px;
            font-family: 'Segoe UI', sans-serif;
        }

        /* NAVBAR */
        .navbar {
            background: linear-gradient(90deg, var(--azul), var(--azul-escuro));
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        /* CARD */
        .card-modern {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        /* TITULO */
        .title {
            font-weight: 600;
            color: #0f172a;
        }

        /* BOTÃO VERDE */
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

        /* BOTÕES AÇÃO */
        .btn-edit {
            background: #3b82f6;
            color: white;
            border-radius: 8px;
        }
        .btn-delete {
            background: #ef4444;
            color: white;
            border-radius: 8px;
        }

        /* INPUT FOCUS */
        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        /* TABELA */
        .table-modern thead {
            background: #f1f5f9;
        }
        .table-modern th {
            color: #475569;
        }
        .table-modern tr:hover {
            background: #eef2ff;
        }

        /* FOOTER */
        footer {
            background: transparent;
            color: #64748b;
        }
    </style>
</head>

<body>

<?php renderNavbar(); ?>

<section class="py-5">
<div class="container-lg" style="max-width: 1200px;">

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/MedicoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/InstituicaoController.php';
$controller = new MedicoController();
$instituicaoController = new InstituicaoController();
$instituicoes = $instituicaoController->getAll();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$medico = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $medico = $controller->getById($_GET['id']);
    if (!$medico) {
        echo "<p>Médico não encontrado.</p>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        $controller->delete($_POST['delete_id']);
        header('Location: medico.php');
        exit;
    } elseif ($action == 'create') {
        $data = [
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf' => $_POST['cpf'],
            'crm' => $_POST['crm'],
            'rqe' => $_POST['rqe'],
            'foto' => $_POST['foto'],
            'nome' => $_POST['nome'],
            'email' => $_POST['email'],
            'senha' => $_POST['senha'],
            'especialidade' => $_POST['especialidade'],
            'telefone' => $_POST['telefone'],
            'endereco' => $_POST['endereco']
        ];
        $controller->create($data);
        header('Location: medico.php');
        exit;
    } elseif ($action == 'edit' && isset($_POST['cod'])) {
        $data = [
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf' => $_POST['cpf'],
            'crm' => $_POST['crm'],
            'rqe' => $_POST['rqe'],
            'foto' => $_POST['foto'],
            'nome' => $_POST['nome'],
            'email' => $_POST['email'],
            'senha' => $_POST['senha'],
            'especialidade' => $_POST['especialidade'],
            'telefone' => $_POST['telefone'],
            'endereco' => $_POST['endereco']
        ];
        $controller->update($_POST['cod'], $data);
        header('Location: medico.php');
        exit;
    }
}

if ($action == 'list') {
?>

<div class="d-flex justify-content-between align-items-start mb-5">
    <div class="d-flex align-items-start gap-3">
        <div style="background: #dbeafe; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-stethoscope" style="font-size: 30px; color: var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Lista de Médicos</h2>
            <p class="text-muted mb-0">Gerencie seus médicos cadastrados no sistema</p>
        </div>
    </div>

    <a href="medico.php?action=create" class="btn btn-verde d-flex align-items-center gap-2" style="margin-top: 5px;">
        <i class="bi bi-plus-lg" style="font-size: 18px;"></i> Adicionar Novo Médico
    </a>
</div>

<div class="card-modern">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead style="background: #f1f5f9;">
<tr>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">CÓDIGO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">INSTITUIÇÃO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">CPF</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">CRM</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">NOME</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">ESPECIALIDADE</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">TELEFONE</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">AÇÕES</th>
</tr>
</thead>

<tbody>
<?php
$medicos = $controller->getAll();
foreach ($medicos as $med) {
    echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$med['cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$med['instituicao_nome']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$med['cpf']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$med['crm']}</td>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$med['nome']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$med['especialidade']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'><i class='bi bi-telephone-fill' style='color: var(--azul); margin-right: 8px;'></i>{$med['telefone']}</td>";
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
<h2 class="title mb-4"><?= $action == 'create' ? 'Novo Médico' : 'Editar Médico' ?></h2>

<form method="POST">

<?php if ($action == 'edit') { ?>
<input type="hidden" name="cod" value="<?= $medico['cod'] ?>">
<?php } ?>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Instituição</label>
        <select name="fk_instituicao_cod" class="form-control" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
        <?php foreach ($instituicoes as $inst) { ?>
        <option value="<?= $inst['cod'] ?>"><?= $inst['nome'] ?></option>
        <?php } ?>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">CPF</label>
        <input class="form-control" name="cpf" value="<?= $action == 'edit' ? $medico['cpf'] : '' ?>" placeholder="000.000.000-00" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">CRM</label>
        <input class="form-control" name="crm" value="<?= $action == 'edit' ? $medico['crm'] : '' ?>" placeholder="CRM número" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>

    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">RQE</label>
        <input class="form-control" name="rqe" value="<?= $action == 'edit' ? $medico['rqe'] : '' ?>" placeholder="RQE número" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Nome</label>
        <input class="form-control" name="nome" value="<?= $action == 'edit' ? $medico['nome'] : '' ?>" placeholder="Nome completo" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>

    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Email</label>
        <input class="form-control" name="email" value="<?= $action == 'edit' ? $medico['email'] : '' ?>" placeholder="email@example.com" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Senha</label>
        <input class="form-control" type="password" name="senha" placeholder="Digite uma senha" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>

    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Especialidade</label>
        <input class="form-control" name="especialidade" value="<?= $action == 'edit' ? $medico['especialidade'] : '' ?>" placeholder="Especialidade médica" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Telefone</label>
        <input class="form-control" name="telefone" value="<?= $action == 'edit' ? $medico['telefone'] : '' ?>" placeholder="(00) 00000-0000" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>

    <div class="col-md-6 mb-3">
        <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Foto URL</label>
        <input class="form-control" name="foto" value="<?= $action == 'edit' ? $medico['foto'] : '' ?>" placeholder="URL da foto" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;">
    </div>
</div>

<div class="mb-4">
    <label style="color: #475569; font-weight: 500; margin-bottom: 8px; display: block;">Endereço</label>
    <textarea class="form-control" name="endereco" placeholder="Rua, número, complemento..." rows="3" style="border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px;"><?= $action == 'edit' ? $medico['endereco'] : '' ?></textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-verde">
        <i class="bi bi-check-lg"></i> Salvar
    </button>
    <a href="medico.php" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 18px;">
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


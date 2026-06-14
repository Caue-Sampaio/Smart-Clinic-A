<?php
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();
require_once __DIR__ . '/navbar.php';

// ── Apenas admin pode acessar ─────────────────────────────────────────────────
if (($_SESSION['role'] ?? null) !== 'admin') {
    header('Location: login.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Instituições</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --azul:#2563eb; --azul-escuro:#1e40af; --verde:#22c55e; --fundo:#f1f5f9; }
        body { background:var(--fundo); font-family:'Segoe UI',sans-serif; }
        .btn-verde { background:linear-gradient(135deg,#22c55e,#16a34a); border:none; color:white; border-radius:10px; padding:10px 18px; transition:.2s; }
        .btn-verde:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(34,197,94,.4); color:white; }
        .card-modern { background:white; border-radius:16px; padding:25px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        .title { font-weight:600; color:#0f172a; }
        .form-control:focus { border-color:var(--azul); box-shadow:0 0 0 .2rem rgba(37,99,235,.25); }
        .table-modern thead { background:#f1f5f9; }
        .table-modern th { color:#475569; font-size:13px; text-transform:uppercase; font-weight:600; padding:15px; }
        .table-modern td { padding:15px; color:#0f172a; vertical-align:middle; }
        .table-modern tr:hover { background:#eef2ff; }
        .inst-status { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }
        .inst-status.ativo   { background:#dcfce7; color:#166534; }
        .inst-status.inativo { background:#fee2e2; color:#991b1b; }
    </style>
</head>
<body>
<?php renderNavbar(); ?>

<section class="py-5">
<div class="container-lg" style="max-width:1200px;">
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/InstituicaoController.php';
$controller  = new InstituicaoController();
$action      = $_GET['action'] ?? 'list';
$instituicao = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $instituicao = $controller->getById($_GET['id']);
    if (!$instituicao) { echo "<p>Instituição não encontrada.</p>"; exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $controller->delete($_POST['delete_id']);
        header('Location: instituicao.php'); exit;
    }
    $data = [
        'cnpj'     => $_POST['cnpj'],
        'logo'     => $_POST['logo']     ?? null,
        'email'    => $_POST['email'],
        'senha'    => $_POST['senha'],
        'nome'     => $_POST['nome'],
        'telefone' => $_POST['telefone'],
        'endereco' => $_POST['endereco'],
        'status'   => $_POST['status']
    ];
    if ($action === 'create') {
        $controller->create($data);
    } elseif ($action === 'edit' && isset($_POST['cod'])) {
        $controller->update($_POST['cod'], $data);
    }
    header('Location: instituicao.php'); exit;
}

if ($action === 'list'):
    $instituicoes = $controller->getAll();
?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-start gap-3">
        <div style="background:#dbeafe;width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-building" style="font-size:26px;color:var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Instituições</h2>
            <p class="text-muted mb-0"><?= count($instituicoes) ?> instituição(ões) cadastrada(s)</p>
        </div>
    </div>
    <a href="instituicao.php?action=create" class="btn btn-verde d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Nova Instituição
    </a>
</div>

<div class="card-modern">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead>
<tr>
    <th>Código</th><th>CNPJ</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Status</th><th>Ações</th>
</tr>
</thead>
<tbody>
<?php foreach ($instituicoes as $inst):
    $statusClass = strtolower($inst['status'] ?? '') === 'ativo' ? 'ativo' : 'inativo';
?>
<tr>
    <td><?= htmlspecialchars($inst['cod']) ?></td>
    <td><?= htmlspecialchars($inst['cnpj']) ?></td>
    <td><strong><?= htmlspecialchars($inst['nome']) ?></strong></td>
    <td><?= htmlspecialchars($inst['email']) ?></td>
    <td><i class="bi bi-telephone-fill" style="color:var(--azul);margin-right:6px;"></i><?= htmlspecialchars($inst['telefone']) ?></td>
    <td><span class="inst-status <?= $statusClass ?>"><?= htmlspecialchars($inst['status'] ?? '—') ?></span></td>
    <td>
        <a href="?action=edit&id=<?= $inst['cod'] ?>" class="btn btn-sm me-1" style="background:#2563eb;color:white;border:none;border-radius:6px;padding:6px 12px;">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <form method="POST" style="display:inline;" onsubmit="return confirm('Deletar esta instituição?')">
            <input type="hidden" name="delete_id" value="<?= $inst['cod'] ?>">
            <button class="btn btn-sm" style="background:#ef4444;color:white;border:none;border-radius:6px;padding:6px 12px;">
                <i class="bi bi-trash"></i> Deletar
            </button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($instituicoes)): ?>
<tr><td colspan="7" class="text-center text-muted py-4">Nenhuma instituição cadastrada.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<?php else: ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="instituicao.php" style="color:#64748b;text-decoration:none;font-size:.9rem;"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
</div>
<div class="card-modern">
<h2 class="title mb-1"><?= $action === 'create' ? 'Nova Instituição' : 'Editar Instituição' ?></h2>
<p class="text-muted mb-4"><?= $action === 'create' ? 'Preencha os dados para cadastrar.' : 'Atualize os dados da instituição.' ?></p>
<form method="POST">
<?php if ($action === 'edit'): ?><input type="hidden" name="cod" value="<?= $instituicao['cod'] ?>"><?php endif; ?>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">CNPJ</label>
        <input class="form-control" name="cnpj" value="<?= $action==='edit' ? htmlspecialchars($instituicao['cnpj']) : '' ?>" placeholder="00.000.000/0001-00" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Nome</label>
        <input class="form-control" name="nome" value="<?= $action==='edit' ? htmlspecialchars($instituicao['nome']) : '' ?>" placeholder="Nome da instituição" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Email</label>
        <input class="form-control" name="email" value="<?= $action==='edit' ? htmlspecialchars($instituicao['email']) : '' ?>" placeholder="email@example.com" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Senha</label>
        <input class="form-control" type="password" name="senha" placeholder="Digite uma senha" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Telefone</label>
        <input class="form-control" name="telefone" value="<?= $action==='edit' ? htmlspecialchars($instituicao['telefone']) : '' ?>" placeholder="(00) 00000-0000" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Status</label>
        <select name="status" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
            <option value="Ativo"   <?= ($action==='edit' && $instituicao['status']==='Ativo')   ? 'selected':'' ?>>Ativo</option>
            <option value="Inativo" <?= ($action==='edit' && $instituicao['status']==='Inativo') ? 'selected':'' ?>>Inativo</option>
        </select>
    </div>
</div>
<div class="mb-3">
    <label class="form-label" style="color:#475569;">Logo URL</label>
    <input class="form-control" name="logo" value="<?= $action==='edit' ? htmlspecialchars($instituicao['logo'] ?? '') : '' ?>" placeholder="https://..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
</div>
<div class="mb-4">
    <label class="form-label" style="color:#475569;">Endereço</label>
    <textarea class="form-control" name="endereco" rows="3" placeholder="Rua, número, complemento..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $action==='edit' ? htmlspecialchars($instituicao['endereco']) : '' ?></textarea>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-verde"><i class="bi bi-check-lg me-1"></i> Salvar</button>
    <a href="instituicao.php" class="btn btn-secondary" style="border-radius:8px;padding:10px 18px;"><i class="bi bi-x-lg me-1"></i> Cancelar</a>
</div>
</form>
</div>
<?php endif; ?>
</div>
</section>
<footer style="padding:20px 0;" class="text-center">
    <p class="mb-0" style="color:#94a3b8;font-size:.85rem;">© 2026 SMART CLINIC — Todos os direitos reservados</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php ob_end_flush(); ?>
</body>
</html>
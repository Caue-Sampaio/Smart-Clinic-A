<?php
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();
require_once __DIR__ . '/navbar.php';

// ── Controle de acesso ────────────────────────────────────────────────────────
$role    = $_SESSION['role']    ?? null;
$isAdmin = $role === 'admin';
$isMedico= $role === 'medico';

// Apenas admin e médico podem acessar esta página
if (!$isAdmin && !$isMedico) {
    header('Location: login.php'); exit;
}

define('FOTO_MEDICO_URL',  '/SMART-CLINIC-A/img/medicos/');
define('FOTO_MEDICO_PATH', $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/img/medicos/');
if (!is_dir(FOTO_MEDICO_PATH)) mkdir(FOTO_MEDICO_PATH, 0755, true);

function salvarFotoMedico(array $f): ?string {
    if ($f['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext,['jpg','jpeg','png','webp','gif']) || $f['size'] > 5*1024*1024) return null;
    $filename = 'medico_' . uniqid() . '.' . $ext;
    return move_uploaded_file($f['tmp_name'], FOTO_MEDICO_PATH . $filename) ? $filename : null;
}
function apagarFotoMedico(?string $fn): void {
    if (!empty($fn) && file_exists(FOTO_MEDICO_PATH . $fn)) unlink(FOTO_MEDICO_PATH . $fn);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Médicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --azul:#2563eb; --azul-escuro:#1e40af; --verde:#22c55e; --fundo:#f1f5f9; }
        body { background:var(--fundo); font-family:'Segoe UI',sans-serif; }
        .btn-verde { background:linear-gradient(135deg,#22c55e,#16a34a); border:none; color:white; border-radius:10px; padding:10px 18px; transition:.2s; }
        .btn-verde:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(34,197,94,.4); color:white; }
        .form-control:focus { border-color:var(--azul); box-shadow:0 0 0 .2rem rgba(37,99,235,.25); }
        .card-modern { background:white; border-radius:16px; padding:25px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        .title { font-weight:600; color:#0f172a; }
        .medico-card { background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; transition:all .25s; height:100%; }
        .medico-card:hover { transform:translateY(-6px); box-shadow:0 20px 40px rgba(0,0,0,.10); border-color:transparent; }
        .medico-card-header { background:linear-gradient(135deg,#1e40af,#2563eb); padding:28px 20px 20px; text-align:center; }
        .medico-avatar { width:90px; height:90px; border-radius:50%; border:3px solid rgba(255,255,255,.5); object-fit:cover; }
        .medico-avatar-placeholder { width:90px; height:90px; border-radius:50%; border:3px solid rgba(255,255,255,.3); background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:2.2rem; color:rgba(255,255,255,.7); margin:0 auto; }
        .medico-card-header h5 { color:white; font-weight:700; font-size:1rem; margin:12px 0 4px; }
        .medico-card-header span { font-size:.8rem; color:rgba(255,255,255,.75); background:rgba(255,255,255,.15); padding:3px 10px; border-radius:20px; }
        .medico-card-body { padding:18px 20px; }
        .medico-info-row { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:.85rem; color:#475569; }
        .medico-info-row:last-child { border-bottom:none; }
        .medico-info-row i { color:var(--azul); font-size:.9rem; width:16px; }
        .medico-info-row strong { color:#0f172a; font-weight:600; }
        .medico-card-footer { padding:14px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; gap:8px; }
        .btn-card-edit { flex:1; background:#2563eb; color:white; border:none; border-radius:8px; padding:8px; font-size:.82rem; font-weight:500; transition:.2s; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:5px; }
        .btn-card-edit:hover { background:#1e40af; color:white; }
        .btn-card-delete { flex:1; background:#fef2f2; color:#ef4444; border:1px solid #fecaca; border-radius:8px; padding:8px; font-size:.82rem; font-weight:500; cursor:pointer; transition:.2s; display:flex; align-items:center; justify-content:center; gap:5px; }
        .btn-card-delete:hover { background:#ef4444; color:white; border-color:#ef4444; }
        .section-label { font-size:.78rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#94a3b8; margin-bottom:16px; }
        .foto-preview-wrap { width:90px; height:90px; border-radius:50%; border:2px dashed #cbd5e1; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f8fafc; margin-bottom:10px; cursor:pointer; transition:.2s; }
        .foto-preview-wrap:hover { border-color:var(--azul); }
        .foto-preview-wrap img { width:100%; height:100%; object-fit:cover; }
        .foto-preview-wrap i { font-size:2rem; color:#cbd5e1; }
        /* Badge somente leitura para médico */
        .readonly-badge { display:inline-flex; align-items:center; gap:6px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; border-radius:8px; padding:6px 12px; font-size:.82rem; font-weight:600; }
    </style>
</head>
<body>
<?php renderNavbar(); ?>

<section class="py-5">
<div class="container-lg" style="max-width:1200px;">

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/MedicoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/InstituicaoController.php';
$controller            = new MedicoController();
$instituicaoController = new InstituicaoController();
$instituicoes          = $instituicaoController->getAll();
$action                = $_GET['action'] ?? 'list';
$medico                = null;

// Médico só pode listar — bloqueia create/edit
if (!$isAdmin && in_array($action, ['create','edit'])) {
    header('Location: medico.php'); exit;
}

if ($action === 'edit' && isset($_GET['id'])) {
    $medico = $controller->getById($_GET['id']);
    if (!$medico) { echo "<p>Médico não encontrado.</p>"; exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    if (isset($_POST['delete_id'])) {
        $m = $controller->getById($_POST['delete_id']);
        if ($m) apagarFotoMedico($m['foto']);
        $controller->delete($_POST['delete_id']);
        header('Location: medico.php'); exit;

    } elseif ($action === 'create') {
        $foto = !empty($_FILES['foto_file']['name']) ? salvarFotoMedico($_FILES['foto_file']) : null;
        $controller->create([
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf'           => $_POST['cpf'],   'crm'  => $_POST['crm'],
            'rqe'           => $_POST['rqe'],   'foto' => $foto,
            'nome'          => $_POST['nome'],  'email'=> $_POST['email'],
            'senha'         => $_POST['senha'], 'especialidade' => $_POST['especialidade'],
            'telefone'      => $_POST['telefone'], 'endereco' => $_POST['endereco']
        ]);
        header('Location: medico.php'); exit;

    } elseif ($action === 'edit' && isset($_POST['cod'])) {
        $atual = $controller->getById($_POST['cod']);
        $foto  = $atual['foto'] ?? null;
        if (!empty($_FILES['foto_file']['name'])) {
            apagarFotoMedico($foto);
            $foto = salvarFotoMedico($_FILES['foto_file']);
        }
        $controller->update($_POST['cod'], [
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf'  => $_POST['cpf'],  'crm'  => $_POST['crm'],
            'rqe'  => $_POST['rqe'], 'foto'  => $foto,
            'nome' => $_POST['nome'], 'email' => $_POST['email'],
            'senha'=> $_POST['senha'], 'especialidade' => $_POST['especialidade'],
            'telefone' => $_POST['telefone'], 'endereco' => $_POST['endereco']
        ]);
        header('Location: medico.php'); exit;
    }
}

/* ── LISTAGEM ─────────────────────────────────────────────────────────────── */
if ($action === 'list'):
    $medicos = $controller->getAll();
?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-start gap-3">
        <div style="background:#dbeafe;width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-stethoscope" style="font-size:26px;color:var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Médicos</h2>
            <p class="text-muted mb-0"><?= count($medicos) ?> médico(s) cadastrado(s)</p>
        </div>
    </div>
    <?php if ($isAdmin): ?>
        <a href="medico.php?action=create" class="btn btn-verde d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Novo Médico
        </a>
    <?php else: ?>
        <div class="readonly-badge"><i class="bi bi-eye"></i> Somente leitura</div>
    <?php endif; ?>
</div>

<div class="section-label">Equipe médica</div>
<div class="row g-4">
<?php foreach ($medicos as $med): ?>
<div class="col-xl-3 col-lg-4 col-md-6">
    <div class="medico-card">
        <div class="medico-card-header">
            <?php if (!empty($med['foto'])): ?>
                <img src="<?= FOTO_MEDICO_URL . htmlspecialchars($med['foto']) ?>"
                     alt="Foto" class="medico-avatar"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="medico-avatar-placeholder" style="display:none;"><i class="bi bi-person-fill"></i></div>
            <?php else: ?>
                <div class="medico-avatar-placeholder"><i class="bi bi-person-fill"></i></div>
            <?php endif; ?>
            <h5><?= htmlspecialchars($med['nome']) ?></h5>
            <span><?= htmlspecialchars($med['especialidade'] ?? 'Especialidade não informada') ?></span>
        </div>
        <div class="medico-card-body">
            <div class="medico-info-row"><i class="bi bi-card-text"></i><span>CRM: <strong><?= htmlspecialchars($med['crm'] ?? '—') ?></strong></span></div>
            <div class="medico-info-row"><i class="bi bi-telephone-fill"></i><span><?= htmlspecialchars($med['telefone'] ?? '—') ?></span></div>
            <div class="medico-info-row"><i class="bi bi-envelope-fill"></i><span style="word-break:break-all;"><?= htmlspecialchars($med['email'] ?? '—') ?></span></div>
            <div class="medico-info-row"><i class="bi bi-building"></i><span><?= htmlspecialchars($med['instituicao_nome'] ?? '—') ?></span></div>
        </div>
        <?php if ($isAdmin): ?>
        <div class="medico-card-footer">
            <a href="medico.php?action=edit&id=<?= $med['cod'] ?>" class="btn-card-edit"><i class="bi bi-pencil"></i> Editar</a>
            <form method="POST" style="flex:1;" onsubmit="return confirm('Deletar este médico?')">
                <input type="hidden" name="delete_id" value="<?= $med['cod'] ?>">
                <button type="submit" class="btn-card-delete w-100"><i class="bi bi-trash"></i> Deletar</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
<?php if (empty($medicos)): ?>
<div class="col-12">
    <div class="card-modern text-center py-5">
        <i class="bi bi-person-x" style="font-size:3rem;color:#cbd5e1;"></i>
        <p class="text-muted mt-3 mb-0">Nenhum médico cadastrado.</p>
        <?php if ($isAdmin): ?><a href="medico.php?action=create" class="btn btn-verde mt-3">Cadastrar primeiro médico</a><?php endif; ?>
    </div>
</div>
<?php endif; ?>
</div>

<?php
/* ── FORMULÁRIO (só admin chega aqui) ─────────────────────────────────────── */
else:
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="medico.php" style="color:#64748b;text-decoration:none;font-size:.9rem;"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
</div>
<div class="card-modern">
<h2 class="title mb-1"><?= $action === 'create' ? 'Novo Médico' : 'Editar Médico' ?></h2>
<p class="text-muted mb-4"><?= $action === 'create' ? 'Preencha os dados para cadastrar um novo médico.' : 'Atualize os dados do médico.' ?></p>
<form method="POST" enctype="multipart/form-data">
<?php if ($action === 'edit'): ?><input type="hidden" name="cod" value="<?= $medico['cod'] ?>"><?php endif; ?>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Instituição</label>
        <select name="fk_instituicao_cod" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
            <?php foreach ($instituicoes as $inst): ?>
            <option value="<?= $inst['cod'] ?>" <?= ($action==='edit' && $medico['fk_instituicao_cod']==$inst['cod']) ? 'selected' : '' ?>><?= htmlspecialchars($inst['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">CPF</label>
        <input class="form-control" name="cpf" value="<?= $action==='edit' ? htmlspecialchars($medico['cpf']) : '' ?>" placeholder="000.000.000-00" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">CRM</label>
        <input class="form-control" name="crm" value="<?= $action==='edit' ? htmlspecialchars($medico['crm']) : '' ?>" placeholder="CRM número" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">RQE</label>
        <input class="form-control" name="rqe" value="<?= $action==='edit' ? htmlspecialchars($medico['rqe']) : '' ?>" placeholder="RQE" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Nome</label>
        <input class="form-control" name="nome" value="<?= $action==='edit' ? htmlspecialchars($medico['nome']) : '' ?>" placeholder="Nome completo" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Email</label>
        <input class="form-control" name="email" value="<?= $action==='edit' ? htmlspecialchars($medico['email']) : '' ?>" placeholder="email@example.com" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Senha</label>
        <input class="form-control" type="password" name="senha" placeholder="Digite uma senha" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Especialidade</label>
        <input class="form-control" name="especialidade" value="<?= $action==='edit' ? htmlspecialchars($medico['especialidade']) : '' ?>" placeholder="Especialidade médica" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Telefone</label>
        <input class="form-control" name="telefone" value="<?= $action==='edit' ? htmlspecialchars($medico['telefone']) : '' ?>" placeholder="(00) 00000-0000" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Foto do Médico <?php if ($action==='edit' && !empty($medico['foto'])): ?><small class="text-muted">(selecione para substituir)</small><?php endif; ?></label>
        <div class="foto-preview-wrap" onclick="document.getElementById('fotoFile').click()">
            <?php if ($action==='edit' && !empty($medico['foto'])): ?>
                <img src="<?= FOTO_MEDICO_URL . htmlspecialchars($medico['foto']) ?>" alt="Foto atual">
            <?php else: ?>
                <i class="bi bi-camera"></i>
            <?php endif; ?>
        </div>
        <input type="file" id="fotoFile" name="foto_file" accept="image/*" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
        <small id="fotoStatus" class="text-muted d-block mt-1"><?= ($action==='edit' && !empty($medico['foto'])) ? 'Foto atual: '.htmlspecialchars($medico['foto']) : 'Nenhuma foto selecionada' ?></small>
    </div>
</div>
<div class="mb-4">
    <label class="form-label" style="color:#475569;">Endereço</label>
    <textarea class="form-control" name="endereco" rows="3" placeholder="Rua, número, complemento..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $action==='edit' ? htmlspecialchars($medico['endereco']) : '' ?></textarea>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-verde"><i class="bi bi-check-lg me-1"></i> Salvar</button>
    <a href="medico.php" class="btn btn-secondary" style="border-radius:8px;padding:10px 18px;"><i class="bi bi-x-lg me-1"></i> Cancelar</a>
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
<script>
const fotoFileInput = document.getElementById('fotoFile');
if (fotoFileInput) {
    fotoFileInput.addEventListener('change', function () {
        const file = this.files[0]; if (!file) return;
        const wrap = this.closest('.col-md-6').querySelector('.foto-preview-wrap');
        const reader = new FileReader();
        reader.onload = e => { wrap.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`; };
        reader.readAsDataURL(file);
        document.getElementById('fotoStatus').textContent = `📎 "${file.name}" — clique em Salvar para confirmar.`;
        document.getElementById('fotoStatus').className = 'text-warning d-block mt-1';
    });
}
</script>
<?php ob_end_flush(); ?>
</body>
</html>
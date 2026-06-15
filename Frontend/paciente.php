<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();
require_once __DIR__ . '/navbar.php';

// ── Pasta de fotos dos pacientes ──────────────────────────────────────────────
define('FOTO_PAC_URL',  '/SMART-CLINIC-A/img/pacientes/');
define('FOTO_PAC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/img/pacientes/');

if (!is_dir(FOTO_PAC_PATH)) {
    mkdir(FOTO_PAC_PATH, 0755, true);
}

function salvarFotoPaciente(array $file): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['jpg','jpeg','png','webp','gif'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed) || $file['size'] > 5 * 1024 * 1024) return null;
    $filename = 'pac_' . uniqid() . '.' . $ext;
    return move_uploaded_file($file['tmp_name'], FOTO_PAC_PATH . $filename) ? $filename : null;
}

function apagarFotoPaciente(?string $filename): void {
    if (!empty($filename)) {
        $path = FOTO_PAC_PATH . $filename;
        if (file_exists($path)) unlink($path);
    }
}

// ── Controllers ───────────────────────────────────────────────────────────────
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/PacienteController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/InstituicaoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/ProntuarioController.php';

$pacController   = new PacienteController();
$instController  = new InstituicaoController();
$prontController = new ProntuarioController();

$instituicoes = $instController->getAll();

// ── Roles ─────────────────────────────────────────────────────────────────────
$role       = $_SESSION['role'] ?? null;
$isAdmin    = $role === 'admin';
$isMedico   = $role === 'medico';
$isPaciente = $role === 'paciente';
$logadoCod  = $_SESSION['user_id'] ?? null;

// Médico e paciente não podem acessar forms de escrita
$action = $_GET['action'] ?? 'list';
if (($isMedico || $isPaciente) && in_array($action, ['create', 'edit', 'prontuario_create', 'prontuario_edit'])) {
    header('Location: paciente.php'); exit;
}

$paciente   = null;
$prontuario = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $paciente = $pacController->getById($_GET['id']);
    if (!$paciente) { echo "<p>Paciente não encontrado.</p>"; exit; }
}
if ($action == 'prontuario_edit' && isset($_GET['id'])) {
    $prontuario = $prontController->getById($_GET['id']);
    if (!$prontuario) { echo "<p>Prontuário não encontrado.</p>"; exit; }
}

// ── Processar POST — apenas admin ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!$isAdmin) {
        header('Location: paciente.php'); exit;
    }

    // DELETAR PACIENTE
    if (isset($_POST['delete_id'])) {
        $pac = $pacController->getById($_POST['delete_id']);
        if ($pac) {
            apagarFotoPaciente($pac['foto'] ?? null);
            $todosPront = $prontController->getAll();
            foreach ($todosPront as $p) {
                if ($p['fk_paciente_cpf'] === $pac['cpf']) {
                    $prontController->delete($p['cod']);
                }
            }
        }
        $pacController->delete($_POST['delete_id']);
        header('Location: paciente.php'); exit;
    }

    // CRIAR PACIENTE
    if ($action == 'create') {
        $foto = null;
        if (!empty($_FILES['foto_file']['name'])) {
            $foto = salvarFotoPaciente($_FILES['foto_file']);
        }
        $pacController->create([
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf'             => $_POST['cpf'],
            'nome'            => $_POST['nome'],
            'data_nascimento' => $_POST['data_nascimento'],
            'email'           => $_POST['email'],
            'senha'           => $_POST['senha'],
            'foto'            => $foto,
            'endereco'        => $_POST['endereco']
        ]);
        header('Location: paciente.php'); exit;
    }

    // EDITAR PACIENTE
    if ($action == 'edit' && isset($_POST['cod'])) {
        $pacAtual = $pacController->getById($_POST['cod']);
        $foto     = $pacAtual['foto'] ?? null;
        if (!empty($_FILES['foto_file']['name'])) {
            apagarFotoPaciente($foto);
            $foto = salvarFotoPaciente($_FILES['foto_file']);
        }
        $pacController->update($_POST['cod'], [
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf'             => $_POST['cpf'],
            'nome'            => $_POST['nome'],
            'data_nascimento' => $_POST['data_nascimento'],
            'email'           => $_POST['email'],
            'senha'           => $_POST['senha'],
            'foto'            => $foto,
            'endereco'        => $_POST['endereco']
        ]);
        header('Location: paciente.php'); exit;
    }

    // CRIAR PRONTUÁRIO
    if ($action == 'prontuario_create') {
        $prontController->create([
            'fk_paciente_cpf'    => $_POST['fk_paciente_cpf'],
            'tipo_sanguineo'     => $_POST['tipo_sanguineo'],
            'doencas_cronicas'   => $_POST['doencas_cronicas']   ?? '',
            'doencas_geneticas'  => $_POST['doencas_geneticas']  ?? '',
            'doencas_autoimunes' => $_POST['doencas_autoimunes'] ?? '',
            'outros'             => $_POST['outros']             ?? ''
        ]);
        header('Location: paciente.php'); exit;
    }

    // EDITAR PRONTUÁRIO
    if ($action == 'prontuario_edit' && isset($_POST['cod'])) {
        $prontController->update($_POST['cod'], [
            'fk_paciente_cpf'    => $_POST['fk_paciente_cpf'],
            'tipo_sanguineo'     => $_POST['tipo_sanguineo'],
            'doencas_cronicas'   => $_POST['doencas_cronicas']   ?? '',
            'doencas_geneticas'  => $_POST['doencas_geneticas']  ?? '',
            'doencas_autoimunes' => $_POST['doencas_autoimunes'] ?? '',
            'outros'             => $_POST['outros']             ?? ''
        ]);
        header('Location: paciente.php'); exit;
    }
}

// ── Carregar dados ────────────────────────────────────────────────────────────
$pacientes   = $isPaciente ? [$pacController->getById($logadoCod)] : $pacController->getAll();
$prontuarios = $prontController->getAll();

$prontMap = [];
foreach ($prontuarios as $p) {
    $prontMap[$p['fk_paciente_cpf']] = $p;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --azul:#2563eb; --azul-escuro:#1e40af; --verde:#22c55e; --fundo:#f1f5f9; --teal:#0d9488; --teal-dark:#0f766e; }
        body { background:var(--fundo); font-family:'Segoe UI',sans-serif; }
        .btn-verde { background:linear-gradient(135deg,#22c55e,#16a34a); border:none; color:white; border-radius:10px; padding:10px 18px; transition:.2s; }
        .btn-verde:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(34,197,94,.4); color:white; }
        .form-control:focus { border-color:var(--azul); box-shadow:0 0 0 .2rem rgba(37,99,235,.25); }
        .card-modern { background:white; border-radius:16px; padding:25px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        .title { font-weight:600; color:#0f172a; }
        .section-label { font-size:.78rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#94a3b8; margin-bottom:16px; }
        .pac-card { background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; transition:all .25s; height:100%; }
        .pac-card:hover { transform:translateY(-6px); box-shadow:0 20px 40px rgba(0,0,0,.10); border-color:transparent; }
        .pac-card-header { background:linear-gradient(135deg,var(--teal-dark),var(--teal)); padding:28px 20px 20px; text-align:center; }
        .pac-avatar { width:90px; height:90px; border-radius:50%; border:3px solid rgba(255,255,255,.5); object-fit:cover; }
        .pac-avatar-placeholder { width:90px; height:90px; border-radius:50%; border:3px solid rgba(255,255,255,.3); background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:2.2rem; color:rgba(255,255,255,.7); margin:0 auto; }
        .pac-card-header h5 { color:white; font-weight:700; font-size:1rem; margin:12px 0 4px; }
        .pac-card-header span { font-size:.8rem; color:rgba(255,255,255,.75); background:rgba(255,255,255,.15); padding:3px 10px; border-radius:20px; }
        .pac-card-body { padding:18px 20px; }
        .pac-info-row { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:.85rem; color:#475569; }
        .pac-info-row:last-child { border-bottom:none; }
        .pac-info-row i { color:var(--teal); font-size:.9rem; width:16px; }
        .pac-info-row strong { color:#0f172a; font-weight:600; }
        .pac-card-footer { padding:14px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; gap:8px; flex-wrap:wrap; }
        .btn-card-info   { flex:1; background:var(--teal); color:white; border:none; border-radius:8px; padding:8px; font-size:.82rem; font-weight:500; transition:.2s; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px; }
        .btn-card-info:hover { background:var(--teal-dark); }
        .btn-card-edit   { flex:1; background:#2563eb; color:white; border:none; border-radius:8px; padding:8px; font-size:.82rem; font-weight:500; transition:.2s; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:5px; }
        .btn-card-edit:hover { background:#1e40af; color:white; }
        .btn-card-delete { flex:1; background:#fef2f2; color:#ef4444; border:1px solid #fecaca; border-radius:8px; padding:8px; font-size:.82rem; font-weight:500; cursor:pointer; transition:.2s; display:flex; align-items:center; justify-content:center; gap:5px; }
        .btn-card-delete:hover { background:#ef4444; color:white; border-color:#ef4444; }
        .readonly-badge { display:inline-flex; align-items:center; gap:6px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; border-radius:8px; padding:6px 12px; font-size:.82rem; font-weight:600; }
        .modal-pac-foto { width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,.5); }
        .modal-pac-foto-placeholder { width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,.15); border:3px solid rgba(255,255,255,.3); display:flex; align-items:center; justify-content:center; font-size:2.5rem; color:rgba(255,255,255,.7); }
        .badge-sangue { display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:50%; background:#fee2e2; color:#dc2626; font-weight:700; font-size:.95rem; }
        .pront-section { background:#f8fafc; border-radius:10px; padding:14px; margin-bottom:10px; }
        .pront-section-title { font-size:.7rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#94a3b8; margin-bottom:6px; }
        .pront-section-body { font-size:.9rem; color:#334155; white-space:pre-wrap; }
        .foto-preview-wrap { width:90px; height:90px; border-radius:50%; border:2px dashed #cbd5e1; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f8fafc; margin-bottom:10px; cursor:pointer; transition:.2s; }
        .foto-preview-wrap:hover { border-color:var(--teal); }
        .foto-preview-wrap img { width:100%; height:100%; object-fit:cover; }
        .foto-preview-wrap i { font-size:2rem; color:#cbd5e1; }
    </style>
</head>
<body>
<?php renderNavbar(); ?>

<!-- MODAL Prontuário -->
<div class="modal fade" id="modalProntuario" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--teal-dark),var(--teal));border:none;padding:20px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalFotoWrap" class="modal-pac-foto-placeholder"><i class="bi bi-person-fill"></i></div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="modalPacNome">—</h5>
                        <span style="color:rgba(255,255,255,.8);font-size:.85rem;" id="modalPacCpf">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="semProntuario" class="text-center py-4" style="display:none;">
                    <i class="bi bi-file-earmark-x" style="font-size:3rem;color:#cbd5e1;"></i>
                    <p class="text-muted mt-3 mb-0">Nenhum prontuário cadastrado para este paciente.</p>
                </div>
                <div id="comProntuario" style="display:none;">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div id="modalBadgeSangue" class="badge-sangue">—</div>
                        <div>
                            <div style="font-size:.72rem;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:1px;">Tipo Sanguíneo</div>
                            <div id="modalTipoSangue" style="font-size:1.15rem;font-weight:700;color:#0f172a;">—</div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pront-section">
                                <div class="pront-section-title"><i class="bi bi-heart-pulse me-1"></i>Doenças Crônicas</div>
                                <div class="pront-section-body" id="modalCronicas">—</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pront-section">
                                <div class="pront-section-title"><i class="bi bi-diagram-3 me-1"></i>Doenças Genéticas</div>
                                <div class="pront-section-body" id="modalGeneticas">—</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pront-section">
                                <div class="pront-section-title"><i class="bi bi-shield-exclamation me-1"></i>Doenças Autoimunes</div>
                                <div class="pront-section-body" id="modalAutoimunes">—</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pront-section">
                                <div class="pront-section-title"><i class="bi bi-clipboard2-pulse me-1"></i>Outros</div>
                                <div class="pront-section-body" id="modalOutros">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:16px 24px;gap:8px;">
                <div id="modalBtnPront"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<section class="py-5">
<div class="container-lg" style="max-width:1200px;">

<?php if ($action == 'list'): ?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-start gap-3">
        <div style="background:#ccfbf1;width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-people" style="font-size:26px;color:var(--teal);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Pacientes</h2>
            <p class="text-muted mb-0"><?= count($pacientes) ?> paciente(s) cadastrado(s)</p>
        </div>
    </div>
    <?php if ($isAdmin): ?>
        <a href="paciente.php?action=create" class="btn btn-verde d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Novo Paciente
        </a>
    <?php elseif ($isMedico): ?>
        <div class="readonly-badge"><i class="bi bi-eye"></i> Somente leitura</div>
    <?php endif; ?>
</div>

<div class="section-label">Lista de pacientes</div>
<div class="row g-4">
<?php foreach ($pacientes as $pac):
    if (!$pac) continue;
    $pront   = $prontMap[$pac['cpf']] ?? null;
    $fotoSrc = !empty($pac['foto']) ? FOTO_PAC_URL . $pac['foto'] : '';
?>
<div class="col-xl-3 col-lg-4 col-md-6">
    <div class="pac-card">
        <div class="pac-card-header">
            <?php if ($fotoSrc): ?>
                <img src="<?= htmlspecialchars($fotoSrc) ?>" alt="Foto" class="pac-avatar"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="pac-avatar-placeholder" style="display:none;"><i class="bi bi-person-fill"></i></div>
            <?php else: ?>
                <div class="pac-avatar-placeholder"><i class="bi bi-person-fill"></i></div>
            <?php endif; ?>
            <h5><?= htmlspecialchars($pac['nome']) ?></h5>
            <span><?= htmlspecialchars($pac['cpf']) ?></span>
        </div>
        <div class="pac-card-body">
            <div class="pac-info-row"><i class="bi bi-building"></i><span><?= htmlspecialchars($pac['instituicao_nome'] ?? '—') ?></span></div>
            <div class="pac-info-row"><i class="bi bi-envelope-fill"></i><span style="word-break:break-all;"><?= htmlspecialchars($pac['email'] ?? '—') ?></span></div>
            <div class="pac-info-row"><i class="bi bi-calendar-fill"></i><span><?= htmlspecialchars($pac['data_nascimento'] ?? '—') ?></span></div>
            <div class="pac-info-row"><i class="bi bi-droplet-fill"></i><span>Sangue: <strong><?= htmlspecialchars($pront['tipo_sanguineo'] ?? '—') ?></strong></span></div>
        </div>
        <div class="pac-card-footer">
            <!-- Botão Informações: visível para todos -->
            <button class="btn-card-info"
                onclick='abrirProntuario(<?= json_encode([
                    "nome"              => $pac["nome"],
                    "cpf"               => $pac["cpf"],
                    "foto"              => $fotoSrc,
                    "pront_cod"         => $pront["cod"] ?? null,
                    "tipo_sanguineo"    => $pront["tipo_sanguineo"]     ?? "",
                    "doencas_cronicas"  => $pront["doencas_cronicas"]   ?? "",
                    "doencas_geneticas" => $pront["doencas_geneticas"]  ?? "",
                    "doencas_autoimunes"=> $pront["doencas_autoimunes"] ?? "",
                    "outros"            => $pront["outros"]             ?? "",
                    "pac_cpf"           => $pac["cpf"]
                ], JSON_HEX_APOS) ?>)'>
                <i class="bi bi-file-earmark-medical"></i> Informações
            </button>

            <?php if ($isAdmin): ?>
            <!-- Editar e Deletar: apenas admin -->
            <a href="paciente.php?action=edit&id=<?= $pac['cod'] ?>" class="btn-card-edit">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <form method="POST" style="flex:1;" onsubmit="return confirm('Deletar este paciente e seus dados?')">
                <input type="hidden" name="delete_id" value="<?= $pac['cod'] ?>">
                <button type="submit" class="btn-card-delete w-100">
                    <i class="bi bi-trash"></i> Deletar
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($pacientes)): ?>
<div class="col-12">
    <div class="card-modern text-center py-5">
        <i class="bi bi-people" style="font-size:3rem;color:#cbd5e1;"></i>
        <p class="text-muted mt-3 mb-0">Nenhum paciente cadastrado ainda.</p>
        <?php if ($isAdmin): ?>
        <a href="paciente.php?action=create" class="btn btn-verde mt-3">Cadastrar primeiro paciente</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
</div>

<?php elseif ($action == 'create' || $action == 'edit'): ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="paciente.php" style="color:#64748b;text-decoration:none;font-size:.9rem;"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
</div>
<div class="card-modern">
    <h2 class="title mb-1"><?= $action == 'create' ? 'Novo Paciente' : 'Editar Paciente' ?></h2>
    <p class="text-muted mb-4"><?= $action == 'create' ? 'Preencha os dados para cadastrar.' : 'Atualize os dados do paciente.' ?></p>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($action == 'edit'): ?>
            <input type="hidden" name="cod" value="<?= $paciente['cod'] ?>">
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Instituição</label>
                <select name="fk_instituicao_cod" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
                    <?php foreach ($instituicoes as $inst): ?>
                    <option value="<?= $inst['cod'] ?>" <?= ($action=='edit' && $paciente['fk_instituicao_cod']==$inst['cod']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($inst['nome']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">CPF</label>
                <input class="form-control" name="cpf" value="<?= $action=='edit' ? htmlspecialchars($paciente['cpf']) : '' ?>" placeholder="000.000.000-00" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Nome</label>
                <input class="form-control" name="nome" value="<?= $action=='edit' ? htmlspecialchars($paciente['nome']) : '' ?>" placeholder="Nome completo" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Data de Nascimento</label>
                <input class="form-control" type="date" name="data_nascimento" value="<?= $action=='edit' ? htmlspecialchars($paciente['data_nascimento']) : '' ?>" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Email</label>
                <input class="form-control" name="email" value="<?= $action=='edit' ? htmlspecialchars($paciente['email']) : '' ?>" placeholder="email@example.com" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Senha</label>
                <input class="form-control" type="password" name="senha" placeholder="Digite uma senha" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">
                    Foto do Paciente
                    <?php if ($action=='edit' && !empty($paciente['foto'])): ?><small class="text-muted">(selecione para substituir)</small><?php endif; ?>
                </label>
                <div class="foto-preview-wrap" id="fotoPacWrap" onclick="document.getElementById('fotoFilePac').click()" title="Clique para escolher foto">
                    <?php if ($action=='edit' && !empty($paciente['foto'])): ?>
                        <img src="<?= FOTO_PAC_URL . htmlspecialchars($paciente['foto']) ?>" alt="Foto atual">
                    <?php else: ?>
                        <i class="bi bi-camera"></i>
                    <?php endif; ?>
                </div>
                <input type="file" id="fotoFilePac" name="foto_file" accept="image/*" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
                <small id="fotoStatusPac" class="text-muted d-block mt-1">
                    <?= ($action=='edit' && !empty($paciente['foto'])) ? 'Foto atual: '.htmlspecialchars($paciente['foto']) : 'Nenhuma foto selecionada' ?>
                </small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Endereço</label>
                <textarea class="form-control" name="endereco" rows="5" placeholder="Rua, número, complemento..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $action=='edit' ? htmlspecialchars($paciente['endereco']) : '' ?></textarea>
            </div>
        </div>
        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-verde"><i class="bi bi-check-lg me-1"></i> Salvar</button>
            <a href="paciente.php" class="btn btn-secondary" style="border-radius:8px;padding:10px 18px;"><i class="bi bi-x-lg me-1"></i> Cancelar</a>
        </div>
    </form>
</div>

<?php elseif ($action == 'prontuario_create' || $action == 'prontuario_edit'):
    $isEdit    = $action == 'prontuario_edit';
    $cpfPresel = $_GET['cpf'] ?? ($prontuario['fk_paciente_cpf'] ?? '');
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="paciente.php" style="color:#64748b;text-decoration:none;font-size:.9rem;"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
</div>
<div class="card-modern">
    <h2 class="title mb-1"><?= $isEdit ? 'Editar Prontuário' : 'Novo Prontuário' ?></h2>
    <p class="text-muted mb-4"><?= $isEdit ? 'Atualize as informações médicas do paciente.' : 'Preencha as informações médicas do paciente.' ?></p>
    <form method="POST">
        <?php if ($isEdit): ?><input type="hidden" name="cod" value="<?= $prontuario['cod'] ?>"><?php endif; ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Paciente</label>
                <select name="fk_paciente_cpf" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
                    <?php foreach ($pacientes as $p): ?>
                    <option value="<?= htmlspecialchars($p['cpf']) ?>" <?= ($p['cpf'] == $cpfPresel) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Tipo Sanguíneo</label>
                <select name="tipo_sanguineo" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                    <option value="<?= $bt ?>" <?= ($isEdit && ($prontuario['tipo_sanguineo'] ?? '') == $bt) ? 'selected' : '' ?>><?= $bt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label" style="color:#475569;">Doenças Crônicas</label>
                <textarea class="form-control" name="doencas_cronicas" rows="3" placeholder="Ex: Diabetes, Hipertensão..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $isEdit ? htmlspecialchars($prontuario['doencas_cronicas'] ?? '') : '' ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Doenças Genéticas</label>
                <textarea class="form-control" name="doencas_geneticas" rows="3" placeholder="Ex: Hemofilia..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $isEdit ? htmlspecialchars($prontuario['doencas_geneticas'] ?? '') : '' ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" style="color:#475569;">Doenças Autoimunes</label>
                <textarea class="form-control" name="doencas_autoimunes" rows="3" placeholder="Ex: Lúpus..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $isEdit ? htmlspecialchars($prontuario['doencas_autoimunes'] ?? '') : '' ?></textarea>
            </div>
            <div class="col-12 mb-4">
                <label class="form-label" style="color:#475569;">Outros</label>
                <textarea class="form-control" name="outros" rows="2" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $isEdit ? htmlspecialchars($prontuario['outros'] ?? '') : '' ?></textarea>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-verde"><i class="bi bi-check-lg me-1"></i> Salvar</button>
            <a href="paciente.php" class="btn btn-secondary" style="border-radius:8px;padding:10px 18px;"><i class="bi bi-x-lg me-1"></i> Cancelar</a>
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
function abrirProntuario(data) {
    document.getElementById('modalPacNome').textContent = data.nome;
    document.getElementById('modalPacCpf').textContent  = data.cpf;

    const fotoWrap = document.getElementById('modalFotoWrap');
    fotoWrap.innerHTML = data.foto
        ? `<img src="${data.foto}" class="modal-pac-foto" alt="Foto">`
        : `<div class="modal-pac-foto-placeholder"><i class="bi bi-person-fill"></i></div>`;

    const comPront = document.getElementById('comProntuario');
    const semPront = document.getElementById('semProntuario');
    const btnPront = document.getElementById('modalBtnPront');

    if (data.pront_cod) {
        semPront.style.display = 'none';
        comPront.style.display = 'block';
        document.getElementById('modalBadgeSangue').textContent = data.tipo_sanguineo      || '—';
        document.getElementById('modalTipoSangue').textContent  = data.tipo_sanguineo      || '—';
        document.getElementById('modalCronicas').textContent    = data.doencas_cronicas    || 'Nenhuma informada';
        document.getElementById('modalGeneticas').textContent   = data.doencas_geneticas   || 'Nenhuma informada';
        document.getElementById('modalAutoimunes').textContent  = data.doencas_autoimunes  || 'Nenhuma informada';
        document.getElementById('modalOutros').textContent      = data.outros              || '—';
        <?php if ($isAdmin): ?>
        btnPront.innerHTML = `<a href="paciente.php?action=prontuario_edit&id=${data.pront_cod}" class="btn btn-verde"><i class="bi bi-pencil me-1"></i> Editar Prontuário</a>`;
        <?php else: ?>
        btnPront.innerHTML = '';
        <?php endif; ?>
    } else {
        semPront.style.display = 'block';
        comPront.style.display = 'none';
        <?php if ($isAdmin): ?>
        btnPront.innerHTML = `<a href="paciente.php?action=prontuario_create&cpf=${data.pac_cpf}" class="btn btn-verde"><i class="bi bi-plus-lg me-1"></i> Criar Prontuário</a>`;
        <?php else: ?>
        btnPront.innerHTML = '';
        <?php endif; ?>
    }

    new bootstrap.Modal(document.getElementById('modalProntuario')).show();
}

const fotoFilePac = document.getElementById('fotoFilePac');
if (fotoFilePac) {
    fotoFilePac.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const wrap   = document.getElementById('fotoPacWrap');
        const status = document.getElementById('fotoStatusPac');
        const reader = new FileReader();
        reader.onload = e => {
            wrap.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;" alt="Preview">`;
        };
        reader.readAsDataURL(file);
        status.textContent = `📎 "${file.name}" — clique em Salvar para confirmar.`;
        status.className   = 'text-warning d-block mt-1';
    });
}
</script>
<?php ob_end_flush(); ?>
</body>
</html>
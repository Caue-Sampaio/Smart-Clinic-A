<?php
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();
require_once __DIR__ . '/navbar.php';

$role   = $_SESSION['role']    ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$role || !$userId) { header('Location: login.php'); exit; }

require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/AdminController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/MedicoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/PacienteController.php';

$adminCtrl   = new AdminController();
$medicoCtrl  = new MedicoController();
$pacCtrl     = new PacienteController();

// Foto só para médico e paciente
define('FOTO_PERFIL_URL',  '/SMART-CLINIC-A/img/');
define('FOTO_MEDICO_PATH_P',  $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/img/medicos/');
define('FOTO_PAC_PATH_P',     $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/img/pacientes/');

function salvarFotoPerfil(array $file, string $pasta, string $prefix): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext,['jpg','jpeg','png','webp','gif']) || $file['size'] > 5*1024*1024) return null;
    if (!is_dir($pasta)) mkdir($pasta, 0755, true);
    $fn = $prefix . uniqid() . '.' . $ext;
    return move_uploaded_file($file['tmp_name'], $pasta . $fn) ? $fn : null;
}

// Carregar dados do usuário logado
$userData = match($role) {
    'admin'    => $adminCtrl->getById($userId),
    'medico'   => $medicoCtrl->getById($userId),
    'paciente' => $pacCtrl->getById($userId),
    default    => null
};

if (!$userData) { header('Location: login.php'); exit; }

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($nome === '' || $email === '') {
        $error = 'Nome e email são obrigatórios.';
    } else {
        $senhaFinal = $senha !== '' ? $senha : $userData['senha'];

        if ($role === 'admin') {
            $adminCtrl->update($userId, ['nome'=>$nome,'email'=>$email,'senha'=>$senhaFinal]);

        } elseif ($role === 'medico') {
            $fotoAtual = $userData['foto'] ?? null;
            if (!empty($_FILES['foto_file']['name'])) {
                if ($fotoAtual) @unlink(FOTO_MEDICO_PATH_P . $fotoAtual);
                $fotoAtual = salvarFotoPerfil($_FILES['foto_file'], FOTO_MEDICO_PATH_P, 'medico_');
            }
            $medicoCtrl->update($userId, array_merge($userData, [
                'nome'=>$nome,'email'=>$email,'senha'=>$senhaFinal,'foto'=>$fotoAtual
            ]));

        } elseif ($role === 'paciente') {
            $fotoAtual = $userData['foto'] ?? null;
            if (!empty($_FILES['foto_file']['name'])) {
                if ($fotoAtual) @unlink(FOTO_PAC_PATH_P . $fotoAtual);
                $fotoAtual = salvarFotoPerfil($_FILES['foto_file'], FOTO_PAC_PATH_P, 'pac_');
            }
            $pacCtrl->update($userId, array_merge($userData, [
                'nome'=>$nome,'email'=>$email,'senha'=>$senhaFinal,'foto'=>$fotoAtual
            ]));
        }

        // Atualiza sessão
        $_SESSION['user_name']  = $nome;
        $_SESSION['user_email'] = $email;
        $success = 'Perfil atualizado com sucesso!';
        // Recarrega dados atualizados
        $userData = match($role) {
            'admin'    => $adminCtrl->getById($userId),
            'medico'   => $medicoCtrl->getById($userId),
            'paciente' => $pacCtrl->getById($userId),
            default    => $userData
        };
    }
}

// Foto para exibição
$fotoSrc = '';
if ($role === 'medico' && !empty($userData['foto'])) {
    $fotoSrc = '/SMART-CLINIC-A/img/medicos/' . $userData['foto'];
} elseif ($role === 'paciente' && !empty($userData['foto'])) {
    $fotoSrc = '/SMART-CLINIC-A/img/pacientes/' . $userData['foto'];
}

$roleLabelMap = ['admin'=>'Administrador','medico'=>'Médico','paciente'=>'Paciente'];
$roleLabel    = $roleLabelMap[$role] ?? $role;
$roleColorMap = ['admin'=>'#92400e','medico'=>'#1e40af','paciente'=>'#065f46'];
$roleBgMap    = ['admin'=>'#fef3c7','medico'=>'#dbeafe','paciente'=>'#d1fae5'];
$roleColor    = $roleColorMap[$role] ?? '#475569';
$roleBg       = $roleBgMap[$role]    ?? '#f1f5f9';

// Iniciais
$parts    = explode(' ', trim($userData['nome']));
$initials = strtoupper(substr($parts[0],0,1)) . (count($parts)>1 ? strtoupper(substr(end($parts),0,1)):'');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Meu Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --azul:#2563eb; --azul-escuro:#1e40af; --verde:#22c55e; --fundo:#f1f5f9; }
        body { background:var(--fundo); font-family:'Segoe UI',sans-serif; }
        .btn-verde { background:linear-gradient(135deg,#22c55e,#16a34a); border:none; color:white; border-radius:10px; padding:10px 18px; transition:.2s; }
        .btn-verde:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(34,197,94,.4); color:white; }
        .form-control:focus { border-color:var(--azul); box-shadow:0 0 0 .2rem rgba(37,99,235,.25); }
        .card-modern { background:white; border-radius:16px; padding:0; box-shadow:0 10px 30px rgba(0,0,0,.08); overflow:hidden; }
        .perfil-header { background:linear-gradient(135deg,var(--azul-escuro),var(--azul)); padding:40px 32px; display:flex; align-items:center; gap:24px; }
        .perfil-avatar-wrap { position:relative; cursor:pointer; }
        .perfil-avatar { width:100px; height:100px; border-radius:50%; border:4px solid rgba(255,255,255,.5); object-fit:cover; display:block; }
        .perfil-avatar-placeholder { width:100px; height:100px; border-radius:50%; border:4px solid rgba(255,255,255,.4); background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:2.5rem; font-weight:700; color:white; }
        .perfil-avatar-overlay { position:absolute; inset:0; border-radius:50%; background:rgba(0,0,0,.4); display:flex; align-items:center; justify-content:center; opacity:0; transition:.2s; }
        .perfil-avatar-wrap:hover .perfil-avatar-overlay { opacity:1; }
        .perfil-header-info h2 { color:white; font-weight:700; font-size:1.5rem; margin-bottom:4px; }
        .perfil-header-info p  { color:rgba(255,255,255,.8); margin:0; font-size:.9rem; }
        .role-pill { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.2); color:white; padding:4px 12px; border-radius:20px; font-size:.8rem; font-weight:600; margin-top:8px; }
        .perfil-body { padding:32px; }
        .section-divider { font-size:.72rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#94a3b8; margin-bottom:16px; margin-top:8px; padding-bottom:8px; border-bottom:1px solid #f1f5f9; }
        .info-card { background:#f8fafc; border-radius:10px; padding:16px; margin-bottom:16px; }
        .info-card-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin-bottom:4px; }
        .info-card-value { font-size:.95rem; color:#0f172a; font-weight:500; }
        .foto-preview-wrap { width:80px; height:80px; border-radius:50%; border:2px dashed #cbd5e1; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f8fafc; cursor:pointer; transition:.2s; }
        .foto-preview-wrap:hover { border-color:var(--azul); }
        .foto-preview-wrap img { width:100%; height:100%; object-fit:cover; }
        .foto-preview-wrap i { font-size:1.8rem; color:#cbd5e1; }
        .alert-success-custom { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:10px; padding:12px 16px; font-weight:500; display:flex; align-items:center; gap:8px; }
        .alert-error-custom   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:12px 16px; font-weight:500; display:flex; align-items:center; gap:8px; }
    </style>
</head>
<body>
<?php renderNavbar(); ?>

<section class="py-5">
<div class="container-lg" style="max-width:760px;">

<?php if ($success): ?>
<div class="alert-success-custom mb-4"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert-error-custom mb-4"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card-modern mb-4">
    <!-- Header com foto e nome -->
    <div class="perfil-header">
        <div class="perfil-avatar-wrap" <?= ($role !== 'admin') ? 'onclick="document.getElementById(\'fotoFilePerfil\').click()" title="Clique para trocar a foto"' : '' ?>>
            <?php if ($fotoSrc): ?>
                <img src="<?= htmlspecialchars($fotoSrc) ?>" class="perfil-avatar" id="perfilAvatarImg"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="perfil-avatar-placeholder" style="display:none;"><?= $initials ?></div>
            <?php else: ?>
                <div class="perfil-avatar-placeholder" id="perfilAvatarPlaceholder"><?= $initials ?></div>
            <?php endif; ?>
            <?php if ($role !== 'admin'): ?>
            <div class="perfil-avatar-overlay"><i class="bi bi-camera-fill text-white" style="font-size:1.4rem;"></i></div>
            <?php endif; ?>
        </div>
        <div class="perfil-header-info">
            <h2><?= htmlspecialchars($userData['nome']) ?></h2>
            <p><?= htmlspecialchars($userData['email']) ?></p>
            <div class="role-pill"><i class="bi bi-person-badge"></i> <?= $roleLabel ?></div>
        </div>
    </div>

    <!-- Informações somente leitura -->
    <div class="perfil-body">
        <div class="section-divider">Informações da conta</div>
        <div class="row g-3 mb-4">
            <?php if (isset($userData['cpf'])): ?>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-label"><i class="bi bi-card-text me-1"></i> CPF</div>
                    <div class="info-card-value"><?= htmlspecialchars($userData['cpf']) ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (isset($userData['crm'])): ?>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-label"><i class="bi bi-stethoscope me-1"></i> CRM</div>
                    <div class="info-card-value"><?= htmlspecialchars($userData['crm']) ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (isset($userData['especialidade'])): ?>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-label"><i class="bi bi-award me-1"></i> Especialidade</div>
                    <div class="info-card-value"><?= htmlspecialchars($userData['especialidade'] ?? '—') ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (isset($userData['telefone'])): ?>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-label"><i class="bi bi-telephone me-1"></i> Telefone</div>
                    <div class="info-card-value"><?= htmlspecialchars($userData['telefone'] ?? '—') ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (isset($userData['data_nascimento'])): ?>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-label"><i class="bi bi-calendar me-1"></i> Data de Nascimento</div>
                    <div class="info-card-value"><?= htmlspecialchars($userData['data_nascimento'] ?? '—') ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (isset($userData['instituicao_nome'])): ?>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-label"><i class="bi bi-building me-1"></i> Instituição</div>
                    <div class="info-card-value"><?= htmlspecialchars($userData['instituicao_nome'] ?? '—') ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Formulário de edição -->
        <div class="section-divider">Editar dados</div>
        <form method="POST" enctype="multipart/form-data">

            <?php if ($role !== 'admin'): ?>
            <!-- Campo foto (médico e paciente) -->
            <div class="mb-4 d-flex align-items-center gap-3">
                <div class="foto-preview-wrap" id="fotoPreviewWrap" onclick="document.getElementById('fotoFilePerfil').click()">
                    <?php if ($fotoSrc): ?>
                        <img src="<?= htmlspecialchars($fotoSrc) ?>" id="fotoPreviewImg" alt="Foto">
                    <?php else: ?>
                        <i class="bi bi-camera"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <input type="file" id="fotoFilePerfil" name="foto_file" accept="image/*" style="display:none;">
                    <small id="fotoStatusPerfil" class="text-muted d-block">
                        <?= $fotoSrc ? 'Clique na foto para trocar' : 'Clique para adicionar foto' ?>
                    </small>
                </div>
            </div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="color:#475569;">Nome completo</label>
                    <input class="form-control" name="nome" value="<?= htmlspecialchars($userData['nome']) ?>"
                           style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="color:#475569;">Email</label>
                    <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>"
                           style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="color:#475569;">Nova senha <small class="text-muted">(deixe em branco para manter)</small></label>
                    <input class="form-control" type="password" name="senha" placeholder="••••••••"
                           style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-verde"><i class="bi bi-check-lg me-1"></i> Salvar alterações</button>
            </div>
        </form>
    </div>
</div>

</div>
</section>

<footer style="padding:20px 0;" class="text-center">
    <p class="mb-0" style="color:#94a3b8;font-size:.85rem;">© 2026 SMART CLINIC — Todos os direitos reservados</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const fotoInput = document.getElementById('fotoFilePerfil');
if (fotoInput) {
    fotoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            // Atualiza preview pequeno
            const wrap = document.getElementById('fotoPreviewWrap');
            wrap.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;" alt="Preview">`;
            // Atualiza avatar grande no header
            const bigImg = document.getElementById('perfilAvatarImg');
            const bigPh  = document.getElementById('perfilAvatarPlaceholder');
            if (bigImg) { bigImg.src = e.target.result; bigImg.style.display='block'; }
            else if (bigPh) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'perfil-avatar';
                img.id = 'perfilAvatarImg';
                bigPh.replaceWith(img);
            }
            // Status
            const status = document.getElementById('fotoStatusPerfil');
            if (status) { status.textContent = `📎 "${file.name}" — clique em Salvar para confirmar.`; status.className='text-warning d-block'; }
        };
        reader.readAsDataURL(file);
    });
}
</script>
<?php ob_end_flush(); ?>
</body>
</html>
<?php 
ob_start();
require_once __DIR__ . '/navbar.php'; 
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
        body { background:var(--fundo); padding-top:80px; font-family:'Segoe UI',sans-serif; }
        .btn-verde { background:linear-gradient(135deg,#22c55e,#16a34a); border:none; color:white; border-radius:10px; padding:10px 18px; transition:.2s; }
        .btn-verde:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(34,197,94,.4); color:white; }
        .form-control:focus { border-color:var(--azul); box-shadow:0 0 0 .2rem rgba(37,99,235,.25); }
        .card-modern { background:white; border-radius:16px; padding:25px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        .title { font-weight:600; color:#0f172a; }
        footer { background:transparent; color:#64748b; }

        /* ── Cards de médico ── */
        .medico-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
        }
        .medico-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.10);
            border-color: transparent;
        }
        .medico-card-header {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            padding: 28px 20px 20px;
            text-align: center;
            position: relative;
        }
        .medico-avatar {
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.5);
            object-fit: cover;
        }
        .medico-avatar-placeholder {
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: rgba(255,255,255,0.7);
            margin: 0 auto;
        }
        .medico-card-header h5 {
            color: white; font-weight: 700;
            font-size: 1rem; margin: 12px 0 4px;
            letter-spacing: -0.3px;
        }
        .medico-card-header span {
            font-size: 0.8rem; color: rgba(255,255,255,0.75);
            background: rgba(255,255,255,0.15);
            padding: 3px 10px; border-radius: 20px;
        }
        .medico-card-body { padding: 18px 20px; }
        .medico-info-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem; color: #475569;
        }
        .medico-info-row:last-child { border-bottom: none; }
        .medico-info-row i { color: var(--azul); font-size: 0.9rem; width: 16px; }
        .medico-info-row strong { color: #0f172a; font-weight: 600; }
        .medico-card-footer {
            padding: 14px 20px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            display: flex; gap: 8px;
        }
        .btn-card-edit {
            flex: 1; background: #2563eb; color: white;
            border: none; border-radius: 8px; padding: 8px;
            font-size: 0.82rem; font-weight: 500;
            transition: .2s; text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }
        .btn-card-edit:hover { background: #1e40af; color: white; }
        .btn-card-delete {
            flex: 1; background: #fef2f2; color: #ef4444;
            border: 1px solid #fecaca; border-radius: 8px; padding: 8px;
            font-size: 0.82rem; font-weight: 500; cursor: pointer;
            transition: .2s;
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }
        .btn-card-delete:hover { background: #ef4444; color: white; border-color: #ef4444; }
        .section-label {
            font-size:.78rem; font-weight:700;
            letter-spacing:1.5px; text-transform:uppercase;
            color:#94a3b8; margin-bottom:16px;
        }

        /* Preview foto no formulário */
        .foto-preview-wrap {
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 2px dashed #cbd5e1;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            background: #f8fafc;
            margin-bottom: 10px;
        }
        .foto-preview-wrap img { width:100%; height:100%; object-fit:cover; }
        .foto-preview-wrap i { font-size: 2rem; color: #cbd5e1; }
    </style>
</head>
<body>
<?php renderNavbar(); ?>

<?php
// ─── Constante com a pasta de fotos ───────────────────────────────────────────
define('FOTO_MEDICO_DIR', '/SMART-CLINIC-A/img/medicos/');
?>

<section class="py-5">
<div class="container-lg" style="max-width:1200px;">

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/MedicoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/InstituicaoController.php';
$controller           = new MedicoController();
$instituicaoController = new InstituicaoController();
$instituicoes         = $instituicaoController->getAll();
$action               = isset($_GET['action']) ? $_GET['action'] : 'list';
$medico               = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $medico = $controller->getById($_GET['id']);
    if (!$medico) { echo "<p>Médico não encontrado.</p>"; exit; }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        $controller->delete($_POST['delete_id']);
        header('Location: medico.php'); exit;

    } elseif ($action == 'create') {
        $data = [
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf'           => $_POST['cpf'],
            'crm'           => $_POST['crm'],
            'rqe'           => $_POST['rqe'],
            'foto'          => $_POST['foto'],      // apenas o nome: medico_xxx.png
            'nome'          => $_POST['nome'],
            'email'         => $_POST['email'],
            'senha'         => $_POST['senha'],
            'especialidade' => $_POST['especialidade'],
            'telefone'      => $_POST['telefone'],
            'endereco'      => $_POST['endereco']
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Linha original (Linha 164) agora protegida pelo try
        $medicoController->create($_POST); 
        
        echo "<script>
                alert('Médico cadastrado com sucesso!');
                window.location.href = 'medico.php';
              </script>";
              
    } catch (Exception $e) {
        // Captura o erro do Controller e exibe em um alerta na tela
        echo "<script>
                alert('" . addslashes($e->getMessage()) . "');
                window.history.back(); // Retorna mantendo os dados digitados
              </script>";
        exit;
    }
}


    } elseif ($action == 'edit' && isset($_POST['cod'])) {
        $data = [
            'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
            'cpf'           => $_POST['cpf'],
            'crm'           => $_POST['crm'],
            'rqe'           => $_POST['rqe'],
            'foto'          => $_POST['foto'],      // apenas o nome: medico_xxx.png
            'nome'          => $_POST['nome'],
            'email'         => $_POST['email'],
            'senha'         => $_POST['senha'],
            'especialidade' => $_POST['especialidade'],
            'telefone'      => $_POST['telefone'],
            'endereco'      => $_POST['endereco']
        ];
        $controller->update($_POST['cod'], $data);
        header('Location: medico.php'); exit;
    }
}

/* ══════════════════════════════════════════════════════════
   LISTAGEM — cards com foto buscada da pasta
══════════════════════════════════════════════════════════ */
if ($action == 'list') {
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
    <a href="medico.php?action=create" class="btn btn-verde d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Novo Médico
    </a>
</div>

<div class="section-label">Equipe médica</div>
<div class="row g-4">
<?php foreach ($medicos as $med): ?>
<div class="col-xl-3 col-lg-4 col-md-6">
    <div class="medico-card">
        <div class="medico-card-header">

            <?php if (!empty($med['foto'])): ?>
                <!--
                    Banco guarda: medico_6673abc.png
                    Pasta física: /SMART-CLINIC-A/img/medicos/
                    URL montada : /SMART-CLINIC-A/img/medicos/medico_6673abc.png
                -->
                <img src="<?= FOTO_MEDICO_DIR . htmlspecialchars($med['foto']) ?>"
                     alt="Foto de <?= htmlspecialchars($med['nome']) ?>"
                     class="medico-avatar">
            <?php else: ?>
                <div class="medico-avatar-placeholder"><i class="bi bi-person-fill"></i></div>
            <?php endif; ?>

            <h5><?= htmlspecialchars($med['nome']) ?></h5>
            <span><?= htmlspecialchars($med['especialidade'] ?? 'Especialidade não informada') ?></span>
        </div>
        <div class="medico-card-body">
            <div class="medico-info-row">
                <i class="bi bi-card-text"></i>
                <span>CRM: <strong><?= htmlspecialchars($med['crm'] ?? '—') ?></strong></span>
            </div>
            <div class="medico-info-row">
                <i class="bi bi-telephone-fill"></i>
                <span><?= htmlspecialchars($med['telefone'] ?? '—') ?></span>
            </div>
            <div class="medico-info-row">
                <i class="bi bi-envelope-fill"></i>
                <span style="word-break:break-all;"><?= htmlspecialchars($med['email'] ?? '—') ?></span>
            </div>
            <div class="medico-info-row">
                <i class="bi bi-building"></i>
                <span><?= htmlspecialchars($med['instituicao_nome'] ?? '—') ?></span>
            </div>
        </div>
        <div class="medico-card-footer">
            <a href="medico.php?action=edit&id=<?= $med['cod'] ?>" class="btn-card-edit">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <form method="POST" style="flex:1;" onsubmit="return confirm('Deletar este médico?')">
                <input type="hidden" name="delete_id" value="<?= $med['cod'] ?>">
                <button type="submit" class="btn-card-delete w-100">
                    <i class="bi bi-trash"></i> Deletar
                </button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($medicos)): ?>
<div class="col-12">
    <div class="card-modern text-center py-5">
        <i class="bi bi-person-x" style="font-size:3rem;color:#cbd5e1;"></i>
        <p class="text-muted mt-3 mb-0">Nenhum médico cadastrado ainda.</p>
        <a href="medico.php?action=create" class="btn btn-verde mt-3">Cadastrar primeiro médico</a>
    </div>
</div>
<?php endif; ?>
</div>

<?php } else { /* ══ FORMULÁRIO CRIAR / EDITAR ══ */ ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="medico.php" style="color:#64748b;text-decoration:none;font-size:0.9rem;">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="card-modern">
<h2 class="title mb-1"><?= $action == 'create' ? 'Novo Médico' : 'Editar Médico' ?></h2>
<p class="text-muted mb-4"><?= $action == 'create' ? 'Preencha os dados para cadastrar um novo médico.' : 'Atualize os dados do médico.' ?></p>

<form method="POST" enctype="multipart/form-data">
<?php if ($action == 'edit'): ?>
    <input type="hidden" name="cod" value="<?= $medico['cod'] ?>">
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Instituição</label>
        <select name="fk_instituicao_cod" class="form-control" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
        <?php foreach ($instituicoes as $inst): ?>
            <option value="<?= $inst['cod'] ?>" <?= ($action=='edit' && $medico['fk_instituicao_cod']==$inst['cod']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($inst['nome']) ?>
            </option>
        <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">CPF</label>
        <input class="form-control" name="cpf" value="<?= $action=='edit' ? htmlspecialchars($medico['cpf']) : '' ?>" placeholder="000.000.000-00" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">CRM</label>
        <input class="form-control" name="crm" value="<?= $action=='edit' ? htmlspecialchars($medico['crm']) : '' ?>" placeholder="CRM número" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">RQE</label>
        <input class="form-control" name="rqe" value="<?= $action=='edit' ? htmlspecialchars($medico['rqe']) : '' ?>" placeholder="RQE número" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Nome</label>
        <input class="form-control" name="nome" value="<?= $action=='edit' ? htmlspecialchars($medico['nome']) : '' ?>" placeholder="Nome completo" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Email</label>
        <input class="form-control" name="email" value="<?= $action=='edit' ? htmlspecialchars($medico['email']) : '' ?>" placeholder="email@example.com" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Senha</label>
        <input class="form-control" type="password" name="senha" placeholder="Digite uma senha" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Especialidade</label>
        <input class="form-control" name="especialidade" value="<?= $action=='edit' ? htmlspecialchars($medico['especialidade']) : '' ?>" placeholder="Especialidade médica" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Telefone</label>
        <input class="form-control" name="telefone" value="<?= $action=='edit' ? htmlspecialchars($medico['telefone']) : '' ?>" placeholder="(00) 00000-0000" style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">
    </div>

    <!-- ── Campo Foto ── -->
    <div class="col-md-6 mb-3">
        <label class="form-label" style="color:#475569;">Foto do Médico</label>

        <!-- Preview circular -->
        <div class="foto-preview-wrap" id="fotoPreviewWrap">
            <?php if ($action == 'edit' && !empty($medico['foto'])): ?>
                <img id="fotoPreview"
                     src="<?= FOTO_MEDICO_DIR . htmlspecialchars($medico['foto']) ?>"
                     alt="Preview">
            <?php else: ?>
                <i class="bi bi-person-fill" id="fotoIcon"></i>
            <?php endif; ?>
        </div>

        <!-- Input file -->
        <input type="file" id="fotoFile" accept="image/*" class="form-control"
               style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;">

        <!-- Guarda APENAS o nome do arquivo (ex: medico_6673abc.png) -->
        <input type="hidden" name="foto" id="fotoNome"
               value="<?= ($action=='edit') ? htmlspecialchars($medico['foto'] ?? '') : '' ?>">

        <small id="fotoStatus" class="text-muted d-block mt-1"></small>
    </div>
</div>

<div class="mb-4">
    <label class="form-label" style="color:#475569;">Endereço</label>
    <textarea class="form-control" name="endereco" rows="3" placeholder="Rua, número, complemento..." style="border-radius:8px;border:1px solid #e2e8f0;padding:10px 12px;"><?= $action=='edit' ? htmlspecialchars($medico['endereco']) : '' ?></textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-verde"><i class="bi bi-check-lg me-1"></i> Salvar</button>
    <a href="medico.php" class="btn btn-secondary" style="border-radius:8px;padding:10px 18px;"><i class="bi bi-x-lg me-1"></i> Cancelar</a>
</div>
</form>
</div>

<?php } ?>
</div>
</section>

<footer style="padding:20px 0;" class="text-center">
    <p class="mb-0" style="color:#94a3b8;font-size:.85rem;">© 2026 SMART CLINIC — Todos os direitos reservados</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Upload de foto via fetch ──────────────────────────────────────────────────
const fotoFileInput = document.getElementById('fotoFile');

if (fotoFileInput) {
    fotoFileInput.addEventListener('change', async function () {
        const file = this.files[0];
        if (!file) return;

        const status    = document.getElementById('fotoStatus');
        const nomeInput = document.getElementById('fotoNome');
        const wrap      = document.getElementById('fotoPreviewWrap');

        status.textContent = 'Enviando...';
        status.className   = 'text-muted d-block mt-1';

        const formData = new FormData();
        formData.append('foto', file);

        try {
            const res  = await fetch('upload_foto.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                // Salva APENAS o nome no hidden input → vai pro banco
                nomeInput.value = data.filename; // ex: medico_6673abc.png

                // Monta URL completa só para o preview
                const urlPreview = '/SMART-CLINIC-A/img/medicos/' + data.filename;

                // Atualiza preview circular
                wrap.innerHTML = `<img id="fotoPreview" src="${urlPreview}" alt="Preview"
                                       style="width:100%;height:100%;object-fit:cover;">`;

                status.textContent = '✓ Foto enviada com sucesso!';
                status.className   = 'text-success d-block mt-1';
            } else {
                status.textContent = '✗ ' + data.error;
                status.className   = 'text-danger d-block mt-1';
            }
        } catch (e) {
            status.textContent = '✗ Erro ao conectar com o servidor.';
            status.className   = 'text-danger d-block mt-1';
        }
    });
}
</script>

<?php ob_end_flush(); ?>
</body>
</html>
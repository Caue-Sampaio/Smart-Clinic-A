<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$nomeUsuario  = $_SESSION['user_name'] ?? 'Administrador';
$primeiroNome = explode(' ', $nomeUsuario)[0];
$hora = (int)date('H');
if ($hora < 12)      $saudacao = 'Bom dia';
elseif ($hora < 18)  $saudacao = 'Boa tarde';
else                 $saudacao = 'Boa noite';
?>
<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Painel do Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f1f5f9; font-family:'Segoe UI',sans-serif; }

        /* ── Hero ── */
        .hero-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 45%, #7c3aed 100%);
            border-radius: 20px;
            padding: 40px 48px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 32px;
        }
        .hero-banner::before {
            content:''; position:absolute;
            top:-60px; right:-60px;
            width:280px; height:280px;
            background:rgba(255,255,255,.07); border-radius:50%;
        }
        .hero-banner::after {
            content:''; position:absolute;
            bottom:-80px; right:120px;
            width:200px; height:200px;
            background:rgba(255,255,255,.05); border-radius:50%;
        }
        .hero-banner .saudacao { font-size:.95rem; font-weight:500; opacity:.85; letter-spacing:.5px; margin-bottom:6px; }
        .hero-banner h1        { font-size:2rem; font-weight:700; margin-bottom:8px; letter-spacing:-.5px; }
        .hero-banner p         { opacity:.8; font-size:.95rem; margin:0; }
        .hero-icon {
            width:80px; height:80px;
            background:rgba(255,255,255,.15);
            border-radius:20px;
            display:flex; align-items:center; justify-content:center;
            font-size:2.5rem;
            backdrop-filter:blur(10px);
        }
        .hero-badge {
            display:inline-flex; align-items:center; gap:6px;
            background:rgba(255,255,255,.15);
            border:1px solid rgba(255,255,255,.25);
            border-radius:20px;
            padding:6px 14px;
            font-size:.82rem; font-weight:500;
            backdrop-filter:blur(10px);
            margin-top:16px;
        }

        /* ── Stats ── */
        .stat-card {
            background:white;
            border-radius:14px;
            padding:20px 22px;
            border:1px solid #e2e8f0;
            display:flex; align-items:center; gap:16px;
            transition:all .2s ease;
        }
        .stat-card:hover { box-shadow:0 8px 24px rgba(0,0,0,.08); transform:translateY(-3px); }
        .stat-icon {
            width:52px; height:52px; border-radius:13px;
            display:flex; align-items:center; justify-content:center;
            font-size:1.4rem; flex-shrink:0;
        }
        .stat-info .value { font-size:1.6rem; font-weight:700; color:#0f172a; line-height:1; margin-bottom:3px; }
        .stat-info .label { font-size:.8rem; color:#64748b; font-weight:500; }

        /* ── Module Cards ── */
        .module-card {
            background:white;
            border-radius:16px;
            padding:24px 22px;
            border:1px solid #e2e8f0;
            transition:all .25s cubic-bezier(.4,0,.2,1);
            position:relative; overflow:hidden;
            height:100%; text-decoration:none;
            display:block; color:inherit;
        }
        .module-card::before {
            content:''; position:absolute;
            top:0; left:0; right:0; height:3px;
            background:var(--card-accent, linear-gradient(90deg,#2563eb,#22c55e));
            transform:scaleX(0); transform-origin:left;
            transition:transform .3s ease;
        }
        .module-card:hover { transform:translateY(-6px); box-shadow:0 20px 40px rgba(0,0,0,.10); border-color:transparent; color:inherit; text-decoration:none; }
        .module-card:hover::before { transform:scaleX(1); }
        .module-icon { width:52px; height:52px; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:14px; transition:transform .25s ease; }
        .module-card:hover .module-icon { transform:scale(1.1) rotate(-3deg); }
        .module-card h5 { font-size:.95rem; font-weight:700; color:#0f172a; margin-bottom:5px; letter-spacing:-.3px; }
        .module-card p  { font-size:.82rem; color:#64748b; margin:0; line-height:1.5; }
        .module-arrow { position:absolute; bottom:18px; right:18px; width:30px; height:30px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:.85rem; transition:all .25s ease; }
        .module-card:hover .module-arrow { background:#2563eb; color:white; transform:translateX(3px); }
        .module-badge { position:absolute; top:16px; right:16px; font-size:.65rem; font-weight:700; padding:3px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:.5px; }
        .badge-admin { background:#fef3c7; color:#92400e; }

        /* Cores */
        .card-blue   .module-icon { background:#dbeafe; color:#2563eb; }
        .card-teal   .module-icon { background:#ccfbf1; color:#0d9488; }
        .card-purple .module-icon { background:#ede9fe; color:#7c3aed; }
        .card-green  .module-icon { background:#dcfce7; color:#16a34a; }
        .card-orange .module-icon { background:#ffedd5; color:#ea580c; }
        .card-pink   .module-icon { background:#fce7f3; color:#db2777; }
        .card-yellow .module-icon { background:#fef9c3; color:#ca8a04; }
        .card-red    .module-icon { background:#fee2e2; color:#dc2626; }
        .card-indigo .module-icon { background:#e0e7ff; color:#4338ca; }
        .card-cyan   .module-icon { background:#cffafe; color:#0891b2; }

        .card-blue   { --card-accent: linear-gradient(90deg,#2563eb,#60a5fa); }
        .card-teal   { --card-accent: linear-gradient(90deg,#0d9488,#2dd4bf); }
        .card-purple { --card-accent: linear-gradient(90deg,#7c3aed,#a78bfa); }
        .card-green  { --card-accent: linear-gradient(90deg,#16a34a,#22c55e); }
        .card-orange { --card-accent: linear-gradient(90deg,#ea580c,#fb923c); }
        .card-pink   { --card-accent: linear-gradient(90deg,#db2777,#f472b6); }
        .card-yellow { --card-accent: linear-gradient(90deg,#ca8a04,#facc15); }
        .card-red    { --card-accent: linear-gradient(90deg,#dc2626,#f87171); }
        .card-indigo { --card-accent: linear-gradient(90deg,#4338ca,#818cf8); }
        .card-cyan   { --card-accent: linear-gradient(90deg,#0891b2,#22d3ee); }

        .section-label { font-size:.78rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#94a3b8; margin-bottom:16px; }
        .section-divider { border:none; border-top:1px solid #e2e8f0; margin:28px 0 20px; }
        footer { background:transparent; }
    </style>
</head>
<body>
<?php renderNavbar(); ?>

<?php
// Carrega contagens para os stats
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/PacienteController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/MedicoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/InstituicaoController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/ReceitaController.php';

$totalPacientes   = count((new PacienteController())->getAll());
$totalMedicos     = count((new MedicoController())->getAll());
$totalInstituicoes= count((new InstituicaoController())->getAll());
$totalReceitas    = count((new ReceitaController())->getAll());
?>

<section class="py-5">
<div class="container" style="max-width:1200px;">

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div style="position:relative;z-index:2;">
                <div class="saudacao">🛡️ <?= $saudacao ?>,</div>
                <h1><?= htmlspecialchars($primeiroNome) ?></h1>
                <p>Você tem acesso completo ao sistema. Gerencie usuários, clínicas e toda a operação.</p>
                <div class="hero-badge">
                    <i class="bi bi-circle-fill" style="font-size:8px;color:#4ade80;"></i>
                    Painel Administrativo
                </div>
            </div>
            <div class="hero-icon" style="position:relative;z-index:2;">
                <i class="bi bi-shield-lock"></i>
            </div>
        </div>
    </div>

    <!-- Stats rápidos -->
    <div class="section-label">Resumo do sistema</div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?= $totalPacientes ?></div>
                    <div class="label">Pacientes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="bi bi-person-badge-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?= $totalMedicos ?></div>
                    <div class="label">Médicos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ccfbf1;color:#0d9488;"><i class="bi bi-building"></i></div>
                <div class="stat-info">
                    <div class="value"><?= $totalInstituicoes ?></div>
                    <div class="label">Instituições</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-receipt"></i></div>
                <div class="stat-info">
                    <div class="value"><?= $totalReceitas ?></div>
                    <div class="label">Receitas</div>
                </div>
            </div>
        </div>
    </div>

    <hr class="section-divider">

    <!-- Módulos de Gestão (só admin) -->
    <div class="section-label">Gestão administrativa</div>
    <div class="row g-4 mb-4">

        <div class="col-md-4 col-sm-6">
            <a href="paciente.php" class="module-card card-blue">
                <span class="module-badge badge-admin">Admin</span>
                <div class="module-icon"><i class="bi bi-people-fill"></i></div>
                <h5>Pacientes</h5>
                <p>Cadastre, edite e gerencie todos os pacientes do sistema.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="medico.php" class="module-card card-purple">
                <span class="module-badge badge-admin">Admin</span>
                <div class="module-icon"><i class="bi bi-person-badge-fill"></i></div>
                <h5>Médicos</h5>
                <p>Gerencie o cadastro completo dos profissionais de saúde.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="instituicao.php" class="module-card card-teal">
                <span class="module-badge badge-admin">Admin</span>
                <div class="module-icon"><i class="bi bi-building-fill"></i></div>
                <h5>Instituições</h5>
                <p>Administre clínicas, hospitais e unidades de saúde cadastradas.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

    </div>

    <hr class="section-divider">

    <!-- Módulos Clínicos -->
    <div class="section-label">Módulos clínicos</div>
    <div class="row g-4">

        <div class="col-md-4 col-sm-6">
            <a href="medicamento.php" class="module-card card-green">
                <div class="module-icon"><i class="bi bi-capsule-pill"></i></div>
                <h5>Medicamentos</h5>
                <p>Gerencie o catálogo de medicamentos disponíveis para prescrição.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="agendamento.php" class="module-card card-blue">
                <div class="module-icon"><i class="bi bi-calendar-plus"></i></div>
                <h5>Agendamentos</h5>
                <p>Visualize e organize a agenda de consultas e atendimentos.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="consulta.php" class="module-card card-indigo">
                <div class="module-icon"><i class="bi bi-clipboard-pulse"></i></div>
                <h5>Consultas</h5>
                <p>Acompanhe todas as consultas realizadas no sistema.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="receita.php" class="module-card card-cyan">
                <div class="module-icon"><i class="bi bi-receipt"></i></div>
                <h5>Receitas</h5>
                <p>Consulte e gerencie as receitas médicas emitidas no sistema.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="exame.php" class="module-card card-pink">
                <div class="module-icon"><i class="bi bi-clipboard-data"></i></div>
                <h5>Exames</h5>
                <p>Acompanhe solicitações e resultados de exames dos pacientes.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="monitoramento.php" class="module-card card-yellow">
                <div class="module-icon"><i class="bi bi-activity"></i></div>
                <h5>Monitoramentos</h5>
                <p>Acompanhe indicadores de saúde e monitoramentos contínuos.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="declaracao.php" class="module-card card-orange">
                <div class="module-icon"><i class="bi bi-file-earmark-text"></i></div>
                <h5>Declarações</h5>
                <p>Gerencie atestados e declarações médicas emitidas.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="perfil.php" class="module-card card-red">
                <div class="module-icon"><i class="bi bi-person-circle"></i></div>
                <h5>Meu Perfil</h5>
                <p>Atualize seus dados de acesso e informações da conta admin.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

    </div>
</div>
</section>

<footer class="text-center py-4 mt-3">
    <div class="container">
        <p class="mb-0" style="color:#94a3b8;font-size:.85rem;">© 2026 SMART CLINIC — Todos os direitos reservados</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
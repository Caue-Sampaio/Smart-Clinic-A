<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'paciente') {
    header('Location: login.php');
    exit;
}
$nomeUsuario = $_SESSION['nome'] ?? 'Paciente';
$primeiroNome = explode(' ', $nomeUsuario)[0];
$hora = (int)date('H');
if ($hora < 12) $saudacao = 'Bom dia';
elseif ($hora < 18) $saudacao = 'Boa tarde';
else $saudacao = 'Boa noite';
?>
<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Portal do Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f1f5f9; padding-top: 80px; font-family: 'Segoe UI', sans-serif; }

        /* ── Hero Banner ── */
        .hero-banner {
            background: linear-gradient(135deg, #0d9488 0%, #0891b2 50%, #0ea5e9 100%);
            border-radius: 20px;
            padding: 40px 48px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 32px;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 280px; height: 280px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }
        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: -80px; right: 120px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-banner .saudacao {
            font-size: 0.95rem;
            font-weight: 500;
            opacity: 0.85;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .hero-banner h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .hero-banner p { opacity: 0.8; font-size: 0.95rem; margin: 0; }

        .hero-icon {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.82rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            margin-top: 16px;
        }

        /* ── Aviso de saúde ── */
        .health-tip {
            background: white;
            border-radius: 14px;
            padding: 18px 24px;
            border-left: 4px solid #0d9488;
            margin-bottom: 32px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .health-tip-icon {
            width: 42px; height: 42px; min-width: 42px;
            background: #ccfbf1; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #0d9488; font-size: 1.2rem;
        }
        .health-tip p { margin: 0; font-size: 0.88rem; color: #475569; }
        .health-tip strong { color: #0f172a; font-size: 0.92rem; }

        /* ── Cards de módulos ── */
        .module-card {
            background: white;
            border-radius: 16px;
            padding: 28px 24px;
            border: 1px solid #e2e8f0;
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
            position: relative;
            overflow: hidden;
            height: 100%;
            text-decoration: none;
            display: block;
            color: inherit;
        }
        .module-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--card-accent, linear-gradient(90deg,#0d9488,#0ea5e9));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        .module-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.10);
            border-color: transparent;
            color: inherit;
            text-decoration: none;
        }
        .module-card:hover::before { transform: scaleX(1); }

        .module-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 16px;
            transition: transform 0.25s ease;
        }
        .module-card:hover .module-icon { transform: scale(1.1) rotate(-3deg); }

        .module-card h5 {
            font-size: 1rem; font-weight: 700;
            color: #0f172a; margin-bottom: 6px; letter-spacing: -0.3px;
        }
        .module-card p {
            font-size: 0.85rem; color: #64748b;
            margin: 0; line-height: 1.5;
        }
        .module-arrow {
            position: absolute; bottom: 20px; right: 20px;
            width: 32px; height: 32px; border-radius: 8px;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            color: #94a3b8; font-size: 0.9rem;
            transition: all 0.25s ease;
        }
        .module-card:hover .module-arrow { background: #0d9488; color: white; transform: translateX(3px); }

        /* Cores */
        .card-teal   .module-icon { background: #ccfbf1; color: #0d9488; }
        .card-blue   .module-icon { background: #dbeafe; color: #2563eb; }
        .card-purple .module-icon { background: #ede9fe; color: #7c3aed; }
        .card-orange .module-icon { background: #ffedd5; color: #ea580c; }
        .card-green  .module-icon { background: #dcfce7; color: #16a34a; }
        .card-pink   .module-icon { background: #fce7f3; color: #db2777; }
        .card-yellow .module-icon { background: #fef9c3; color: #ca8a04; }

        .card-teal   { --card-accent: linear-gradient(90deg,#0d9488,#2dd4bf); }
        .card-blue   { --card-accent: linear-gradient(90deg,#2563eb,#60a5fa); }
        .card-purple { --card-accent: linear-gradient(90deg,#7c3aed,#a78bfa); }
        .card-orange { --card-accent: linear-gradient(90deg,#ea580c,#fb923c); }
        .card-green  { --card-accent: linear-gradient(90deg,#16a34a,#22c55e); }
        .card-pink   { --card-accent: linear-gradient(90deg,#db2777,#f472b6); }
        .card-yellow { --card-accent: linear-gradient(90deg,#ca8a04,#facc15); }

        .section-label {
            font-size: 0.78rem; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: #94a3b8; margin-bottom: 16px;
        }

        footer { background: transparent; }
    </style>
</head>
<body>
<?php renderNavbar(); ?>

<section class="py-5">
<div class="container" style="max-width:1200px;">

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div style="position:relative;z-index:2;">
                <div class="saudacao">👋 <?= $saudacao ?>,</div>
                <h1><?= htmlspecialchars($primeiroNome) ?></h1>
                <p>Acesse suas informações de saúde, consultas e exames com segurança.</p>
                <div class="hero-badge">
                    <i class="bi bi-shield-check"></i>
                    Seus dados estão protegidos
                </div>
            </div>
            <div class="hero-icon" style="position:relative;z-index:2;">
                <i class="bi bi-heart-pulse"></i>
            </div>
        </div>
    </div>

    <!-- Dica de saúde -->
    <div class="health-tip">
        <div class="health-tip-icon"><i class="bi bi-lightbulb"></i></div>
        <div>
            <strong>Dica de saúde</strong>
            <p>Mantenha seus dados cadastrais sempre atualizados para que seu médico possa entrar em contato quando necessário.</p>
        </div>
    </div>

    <!-- Módulos -->
    <div class="section-label">Minha área de saúde</div>
    <div class="row g-4">

        <div class="col-md-4 col-sm-6">
            <a href="agendamento.php" class="module-card card-teal">
                <div class="module-icon"><i class="bi bi-calendar-event"></i></div>
                <h5>Meus Agendamentos</h5>
                <p>Visualize e gerencie seus agendamentos de consultas.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="consulta.php" class="module-card card-blue">
                <div class="module-icon"><i class="bi bi-stethoscope"></i></div>
                <h5>Minhas Consultas</h5>
                <p>Acompanhe o histórico completo das suas consultas médicas.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="prescrever.php" class="module-card card-purple">
                <div class="module-icon"><i class="bi bi-prescription"></i></div>
                <h5>Minhas Prescrições</h5>
                <p>Veja todas as prescrições emitidas pelos seus médicos.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="prontuario.php" class="module-card card-orange">
                <div class="module-icon"><i class="bi bi-file-earmark-medical"></i></div>
                <h5>Meu Prontuário</h5>
                <p>Acesse seu prontuário eletrônico com todo seu histórico de saúde.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="receita.php" class="module-card card-green">
                <div class="module-icon"><i class="bi bi-receipt"></i></div>
                <h5>Minhas Receitas</h5>
                <p>Visualize e baixe suas receitas médicas em PDF.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="exame.php" class="module-card card-pink">
                <div class="module-icon"><i class="bi bi-clipboard-data"></i></div>
                <h5>Meus Exames</h5>
                <p>Acompanhe resultados e solicitações de exames laboratoriais.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="monitoramento.php" class="module-card card-yellow">
                <div class="module-icon"><i class="bi bi-activity"></i></div>
                <h5>Meu Monitoramento</h5>
                <p>Acompanhe seu plano de monitoramento de saúde contínuo.</p>
                <div class="module-arrow"><i class="bi bi-arrow-right"></i></div>
            </a>
        </div>

    </div>
</div>
</section>

<footer class="text-center py-4 mt-3">
    <div class="container">
        <p class="mb-0" style="color:#94a3b8;font-size:0.85rem;">© 2026 SMART CLINIC — Todos os direitos reservados</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
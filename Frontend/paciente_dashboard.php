<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'paciente') {
    header('Location: login.php');
    exit;
}
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
</head>
<body>

    <?php renderNavbar(); ?>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h1 class="text-azul fw-bold">Portal do Paciente</h1>
                <p class="text-muted">Acesse suas informações médicas e agendamentos de forma segura.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-calendar-event fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Meus Agendamentos</h5>
                            <p class="card-text">Visualize e gerencie seus agendamentos.</p>
                            <a href="agendamento.php" class="btn btn-verde">Ver Agendamentos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-stethoscope fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Minhas Consultas</h5>
                            <p class="card-text">Acompanhe o histórico de suas consultas.</p>
                            <a href="consulta.php" class="btn btn-verde">Ver Consultas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-prescription fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Minhas Prescrições</h5>
                            <p class="card-text">Veja suas prescrições médicas.</p>
                            <a href="prescrever.php" class="btn btn-verde">Ver Prescrições</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-file-earmark-medical fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Meu Prontuário</h5>
                            <p class="card-text">Acesse seu prontuário médico.</p>
                            <a href="prontuario.php" class="btn btn-verde">Ver Prontuário</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-receipt fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Minhas Receitas</h5>
                            <p class="card-text">Visualize suas receitas emitidas.</p>
                            <a href="receita.php" class="btn btn-verde">Ver Receitas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-clipboard-data fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Meus Exames</h5>
                            <p class="card-text">Acompanhe resultados de exames.</p>
                            <a href="exame.php" class="btn btn-verde">Ver Exames</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-activity fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Meu Monitoramento</h5>
                            <p class="card-text">Veja seu plano de monitoramento.</p>
                            <a href="monitoramento.php" class="btn btn-verde">Ver Monitoramento</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-4">
        <div class="container">
            <div class="d-flex justify-content-center mb-3">
                <a href="#" class="text-white mx-2 fs-4"><i class="bi bi-instagram"></i></a>
            </div>
            <p class="mb-0">© 2026 SMART CLINIC:A - Todos os direitos reservados</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



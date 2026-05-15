<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'medico') {
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
    <title>SMART CLINIC - Médico</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php renderNavbar(); ?>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h1 class="text-azul fw-bold"> Médico</h1>
                <p class="text-muted">Gerencie suas consultas, pacientes e registros médicos.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-person-lines-fill fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Pacientes</h5>
                            <p class="card-text">Visualize e gerencie informações dos pacientes.</p>
                            <a href="paciente.php" class="btn btn-verde">Acessar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-calendar-check fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Consultas</h5>
                            <p class="card-text">Agende e gerencie consultas médicas.</p>
                            <a href="consulta.php" class="btn btn-verde">Acessar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-prescription fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Prescrições</h5>
                            <p class="card-text">Crie e gerencie prescrições médicas.</p>
                            <a href="prescrever.php" class="btn btn-verde">Acessar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-file-earmark-medical fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Prontuários</h5>
                            <p class="card-text">Acesse e atualize prontuários dos pacientes.</p>
                            <a href="prontuario.php" class="btn btn-verde">Acessar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-receipt fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Receitas</h5>
                            <p class="card-text">Emita e gerencie receitas médicas.</p>
                            <a href="receita.php" class="btn btn-verde">Acessar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-clipboard-data fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Exames</h5>
                            <p class="card-text">Solicite e acompanhe exames laboratoriais.</p>
                            <a href="exame.php" class="btn btn-verde">Acessar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-activity fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Monitoramentos</h5>
                            <p class="card-text">Acompanhe o monitoramento de pacientes.</p>
                            <a href="monitoramento.php" class="btn btn-verde">Acessar</a>
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



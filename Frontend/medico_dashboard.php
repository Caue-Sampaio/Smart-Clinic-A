<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'medico') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Dashboard Médico</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --cor-verde: #28A745; 
            --cor-azul: #007BFF;  
        }

        
        .bg-azul { background-color: var(--cor-azul) !important; }
        .bg-verde { background-color: var(--cor-verde) !important; }

        
        .text-azul { color: var(--cor-azul) !important; }
        .text-verde { color: var(--cor-verde) !important; }

        
        .btn-verde {
            background-color: var(--cor-verde);
            color: #fff;
            border: none;
            transition: 0.3s;
        }
        .btn-verde:hover {
            background-color: #218838;
            color: #fff;
        }

        .btn-azul {
            background-color: var(--cor-azul);
            color: #fff;
            border: none;
            transition: transform 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }
        .btn-azul:hover {
            background-color: #0056b3;
            color: #fff;
            transform: scale(1.05);
        }

        .btn-ciano {
            background-color: #09529b;
            color: #fff;
            border: none;
            transition: 0.3s;
        }
        .btn-ciano:hover {
            background-color: #4896e4;
            color: #fff;
        }

        .card-service {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-service:hover {
            transform: scale(1.05);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2);
        }

        .border-verde { border: 2px solid var(--cor-verde) !important; }
        .navbar { padding-top: 1rem; padding-bottom: 1rem; }
        html, body { height: 100%; }
        body { padding-top: 76px; display: flex; flex-direction: column; }
        section { flex: 1; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-azul navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
                <img src="../img/logob.png" alt="Logo" class="me-2" style="height: 40px;">
                SMART CLINIC
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div  class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="medico_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gerenciar Atendimentos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="paciente.php">Pacientes</a></li>
                            <li><a class="dropdown-item" href="consulta.php">Consultas</a></li>
                            <li><a class="dropdown-item" href="prescrever.php">Prescrever</a></li>
                            <li><a class="dropdown-item" href="prontuario.php">Prontuários</a></li>
                            <li><a class="dropdown-item" href="receita.php">Receitas</a></li>
                            <li><a class="dropdown-item" href="exame.php">Exames</a></li>
                            <li><a class="dropdown-item" href="monitoramento.php">Monitoramentos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="logout.php" class="btn btn-ciano fw-semibold px-4 rounded-pill">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h1 class="text-azul fw-bold">Dashboard do Médico</h1>
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

    <footer class="bg-azul text-white text-center py-4">
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
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC</title>
    
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
            transition: transform 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }
        .btn-verde:hover {
            background-color: #218838;
            color: #fff;
            transform: scale(1.05);
        }

        .btn-azul {
            background-color: var(--cor-azul);
            color: #fff;
            border: none;
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .btn-azul:hover {
            color: #fff;
            transform: scale(1.05);
        }

        .btn-ciano {
            background-color: #09529b;
            color: #fff;
            border: none;
            transition: transform 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }
        .btn-ciano:hover {
            background-color: #4896e4;
            color: #fff;
            transform: scale(1.05);
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
        .nav-link {
            transition: transform 0.3s ease;
            display: inline-block;
        }
        .nav-link:hover {
            transform: scale(1.05);
        }
        body { padding-top: 76px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-azul navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="#home">
                <img src="../img/logob.png" alt="Logo" class="me-2" style="height: 40px;">
                SMART CLINIC
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div  class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#sobre">Sobre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#servicos">Serviços</a>
                    </li>

                    <li class="nav-item mt-2 mt-lg-0 me-2">
                        <a href="login.php" class="btn btn-azul fw-semibold px-4 rounded-pill">Login</a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0 me-2">
                        <a href="logout.php" class="btn btn-verde fw-semibold px-4 rounded-pill">Sair</a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="#contato" class="btn btn-ciano fw-semibold px-4 rounded-pill">Agendar Consulta</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header id="home" class="text-dark d-flex align-items-center" style="min-height: 80vh; background-image: url('../img/logo-fundo.avif'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div  class="container text-center">
            <h1  class="display-4 fw-bold mb-3">Bem-vindo à SMART CLINIC</h1>
            <p class="lead mb-4 fs-4">Tecnologia e cuidado humano unidos para sua saúde</p>
            <a href="#servicos" class="btn btn-azul btn-lg px-5 py-3 rounded-pill fw-semibold shadow">Conheça Nossos Serviços</a>
        </div>
    </header>

    <section id="sobre" class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-1">
                    
                    <h5 class="text-azul mb-3">O Significado de Nossa Marca:</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-verde me-2"></i> <strong>Ícone Verde (Cruz e Estetoscópio):</strong> Simboliza saúde e cuidado.</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-verde me-2"></i> <strong>Cor Verde:</strong> Representa esperança, renovação e equilíbrio.</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-verde me-2"></i> <strong>Cruz Médica:</strong> O símbolo universal e atemporal da medicina.</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-verde me-2"></i> <strong>Estetoscópio:</strong> Reflete a prática clínica e a proximidade com o paciente.</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-verde me-2"></i> <strong>Tipografia Azul:</strong> Transmite confiança, seriedade e modernidade.</li>
                    </ul>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 text-center mb-4 mb-lg-0">
                    <img src="../img/logoA.png" alt="Logo Smart Clinic" class="img-fluid rounded" style="max-width: 80%;">
                </div>
            </div>
        </div>
    </section>

    <section id="servicos" class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="text-azul fw-bold">Nossos Serviços</h2>
                <p class="text-muted">Cuidado especializado em cada etapa da sua saúde.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-bandaid fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Consultas Médicas</h5>
                            <p class="card-text">Atendimento humanizado com especialistas em diversas áreas para garantir o seu bem-estar completo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-clipboard2-pulse fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">axendamento medico
                            </h5>
                            <p class="card-text">Tecnologia de ponta para diagnósticos rápidos, precisos e totalmente seguros em um só lugar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-service h-100 border-verde shadow-sm text-center p-4">
                        <div class="card-body">
                            <i class="bi bi-laptop fs-1 text-verde mb-3"></i>
                            <h5 class="card-title text-azul fw-bold">Telemedicina</h5>
                            <p class="card-text">Praticidade e segurança com consultas online no conforto da sua casa, sem perder a qualidade.</p>
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

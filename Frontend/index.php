<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php renderNavbar(); ?>

    <header id="home" class="hero-section">
        <div class="container h-100 d-flex align-items-center">
            <div class="row w-100 align-items-center">
                <div class="col-lg-7 text-lg-start text-center fade-in-up">
                    <h1 class="hero-title mb-4">Bem-vindo à SMART CLINIC</h1>
                    <p class="hero-subtitle mb-5">Tecnologia e cuidado humano unidos para sua saúde</p>
                    <a href="#servicos" class="btn btn-azul btn-lg px-5 py-3 rounded-pill fw-semibold btn-hover">Conheça Nossos Serviços</a>
                </div>
                <div class="col-lg-5 text-center fade-in-right d-none d-lg-block">
                    <div class="hero-image-wrapper">
                        <img src="../img/logo-fundo.avif" alt="Smart Clinic" class="hero-image">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="sobre" class="section-sobre py-6">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1 fade-in-left">
                    <h5 class="text-azul mb-4 fw-bold text-uppercase tracking">O Significado de Nossa Marca:</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-verde me-3 mt-1 flex-shrink-0"></i> 
                            <div>
                                <strong class="text-azul">Ícone Verde (Cruz e Estetoscópio):</strong>
                                <p class="text-muted mb-0">Simboliza saúde e cuidado.</p>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-verde me-3 mt-1 flex-shrink-0"></i> 
                            <div>
                                <strong class="text-azul">Cor Verde:</strong>
                                <p class="text-muted mb-0">Representa esperança, renovação e equilíbrio.</p>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-verde me-3 mt-1 flex-shrink-0"></i> 
                            <div>
                                <strong class="text-azul">Cruz Médica:</strong>
                                <p class="text-muted mb-0">O símbolo universal e atemporal da medicina.</p>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-verde me-3 mt-1 flex-shrink-0"></i> 
                            <div>
                                <strong class="text-azul">Estetoscópio:</strong>
                                <p class="text-muted mb-0">Reflete a prática clínica e a proximidade com o paciente.</p>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-verde me-3 mt-1 flex-shrink-0"></i> 
                            <div>
                                <strong class="text-azul">Tipografia Azul:</strong>
                                <p class="text-muted mb-0">Transmite confiança, seriedade e modernidade.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 text-center mb-4 mb-lg-0 fade-in-right">
                    <div class="logo-wrapper">
                        <img src="../img/logoA.png" alt="Logo Smart Clinic" class="img-fluid logo-image" style="max-width: 80%;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="servicos" class="py-6 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5 fade-in">
                <h2 class="text-azul fw-bold display-5 mb-3">Nossos Serviços</h2>
                <p class="text-muted fs-5 m-0">Cuidado especializado em cada etapa da sua saúde.</p>
                <div class="mt-3" style="width: 60px; height: 3px; background: linear-gradient(90deg, var(--azul), var(--verde)); margin: 0 auto; border-radius: 2px;"></div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4 fade-in-up">
                    <div class="card-service-modern h-100">
                        <div class="service-icon">
                            <i class="bi bi-bandaid"></i>
                        </div>
                        <h5 class="card-title text-azul fw-bold mt-3 mb-2">Consultas Médicas</h5>
                        <p class="card-text text-muted mb-0">Atendimento humanizado com especialistas em diversas áreas para garantir o seu bem-estar completo.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in-up">
                    <div class="card-service-modern h-100">
                        <div class="service-icon">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>
                        <h5 class="card-title text-azul fw-bold mt-3 mb-2">Agendamento Médico</h5>
                        <p class="card-text text-muted mb-0">Tecnologia de ponta para diagnósticos rápidos, precisos e totalmente seguros em um só lugar.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in-up">
                    <div class="card-service-modern h-100">
                        <div class="service-icon">
                            <i class="bi bi-laptop"></i>
                        </div>
                        <h5 class="card-title text-azul fw-bold mt-3 mb-2">Telemedicina</h5>
                        <p class="card-text text-muted mb-0">Praticidade e segurança com consultas online no conforto da sua casa, sem perder a qualidade.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <footer class="footer-modern py-5 mt-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="text-center">
                        <div class="d-flex justify-content-center gap-3 mb-4">
                            <a href="#" class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="social-link" title="Twitter"><i class="bi bi-twitter"></i></a>
                        </div>
                        <p class="mb-0 fw-medium text-dark">© 2026 SMART CLINIC:A</p>
                        <p class="text-muted small mb-0">Todos os direitos reservados</p>
                    </div>
                </div>
            </div>
            <hr class="my-3 border-light">
            <div class="text-center">
                <p class="text-muted small mb-0">Desenvolvido com <i class="bi bi-heart-fill text-verde"></i> para sua saúde</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




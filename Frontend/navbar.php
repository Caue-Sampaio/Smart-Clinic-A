<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('renderNavbar')) {
    function renderNavbar(): void {
        $role = $_SESSION['role'] ?? null;
        $email = $_SESSION['user_email'] ?? '';

        $brandLink = '#';
        $homeLink = 'index.php';
        $serviceButton = ['href' => 'agendamento.php', 'icon' => 'bi bi-calendar-check', 'label' => 'Agendar Consulta'];

        $dropdownLinks = [];
        if ($role === 'paciente') {
            $dropdownLinks = [
                ['href' => 'paciente_dashboard.php', 'icon' => 'bi bi-person-circle', 'label' => 'Meu Portal'],
                ['href' => 'agendamento.php', 'icon' => 'bi bi-calendar-check', 'label' => 'Agendamentos'],
                ['href' => 'consulta.php', 'icon' => 'bi bi-clipboard-pulse', 'label' => 'Consultas'],
                ['href' => 'prescrever.php', 'icon' => 'bi bi-prescription', 'label' => 'Prescrições'],
                ['href' => 'prontuario.php', 'icon' => 'bi bi-file-medical', 'label' => 'Prontuários'],
                ['href' => 'receita.php', 'icon' => 'bi bi-receipt', 'label' => 'Receitas'],
                ['href' => 'exame.php', 'icon' => 'bi bi-file-earmark-medical', 'label' => 'Exames'],
                ['href' => 'monitoramento.php', 'icon' => 'bi bi-heart-pulse', 'label' => 'Monitoramentos'],
            ];
        } elseif ($role === 'medico') {
            $dropdownLinks = [
                ['href' => 'paciente.php', 'icon' => 'bi bi-person', 'label' => 'Pacientes'],
                ['href' => 'medico.php', 'icon' => 'bi bi-person', 'label' => 'Médicos'],
                ['href' => 'instituicao.php', 'icon' => 'bi bi-building', 'label' => 'Instituições'],
                ['href' => 'medicamento.php', 'icon' => 'bi bi-capsule', 'label' => 'Medicamentos'],
                ['href' => 'agendamento.php', 'icon' => 'bi bi-calendar-check', 'label' => 'Agendamentos'],
                ['href' => 'consulta.php', 'icon' => 'bi bi-clipboard-pulse', 'label' => 'Consultas'],
                ['href' => 'prescrever.php', 'icon' => 'bi bi-prescription', 'label' => 'Prescrever'],
                ['href' => 'prontuario.php', 'icon' => 'bi bi-file-earmark-medical', 'label' => 'Prontuários'],
                ['href' => 'receita.php', 'icon' => 'bi bi-receipt', 'label' => 'Receitas'],
                ['href' => 'exame.php', 'icon' => 'bi bi-file-earmark-medical', 'label' => 'Exames'],
                ['href' => 'monitoramento.php', 'icon' => 'bi bi-heart-pulse', 'label' => 'Monitoramentos'],
                ['href' => 'declaracao.php', 'icon' => 'bi bi-file-earmark-text', 'label' => 'Declarações'],
            ];
        }

        echo '<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #1e3a8a 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.1);">';
        echo '<div class="container-fluid px-5">';
        echo '<a class="navbar-brand fw-bold d-flex align-items-center" href="' . $brandLink . '">';
        echo '<img src="../img/logob.png" alt="Logo" class="me-2" style="height: 40px;">';
        echo 'SMART CLINIC';
        echo '</a>';
        echo '<div class="ms-auto d-flex gap-3 align-items-center">';

        if ($role === 'paciente' || $role === 'medico') {
            echo '<div class="dropdown">';
            echo '<button class="btn btn-link text-white text-decoration-none d-flex align-items-center gap-2" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: 500;">';
            echo '<i class="bi bi-gear" style="font-size: 20px;"></i>';
            echo 'Gerenciar Dados';
            echo '<i class="bi bi-chevron-down" style="font-size: 16px;"></i>';
            echo '</button>';
            echo '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">';
            foreach ($dropdownLinks as $link) {
                echo '<li><a class="dropdown-item" href="' . $link['href'] . '"><i class="' . $link['icon'] . ' me-2"></i>' . $link['label'] . '</a></li>';
            }
            echo '<li><hr class="dropdown-divider"></li>';
            echo '<li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>';
            echo '</ul>';
            echo '</div>';
            echo '<a href="' . $serviceButton['href'] . '" class="btn btn-light rounded-pill px-4 d-flex align-items-center gap-2" style="font-weight: 500;">';
            echo '<i class="' . $serviceButton['icon'] . '" style="font-size: 18px;"></i>';
            echo $serviceButton['label'];
            echo '</a>';
        } else {
            echo '<a href="login.php" class="btn btn-azul fw-semibold px-4 rounded-pill">Login</a>';
        }

        echo '</div>'; // .ms-auto
        echo '</div>'; // .container-fluid
        echo '</nav>';
    }
}

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('renderNavbar')) {
    function renderNavbar(): void {
        $role = $_SESSION['role'] ?? null;
        $userName = $_SESSION['user_name'] ?? 'Usuário';
        $userEmail = $_SESSION['user_email'] ?? '';

        $serviceButton = ['href' => 'agendamento.php', 'icon' => 'bi bi-calendar-check', 'label' => 'Agendar Consulta'];

        $dropdownLinks = [];
        if ($role === 'paciente') {
            $dropdownLinks = [
                ['href' => 'paciente_dashboard.php', 'icon' => 'bi bi-person-circle',        'label' => 'Meu Portal'],
                ['href' => 'agendamento.php',         'icon' => 'bi bi-calendar-check',       'label' => 'Agendamentos'],
                ['href' => 'consulta.php',            'icon' => 'bi bi-clipboard-pulse',      'label' => 'Consultas'],
                ['href' => 'prontuario.php',          'icon' => 'bi bi-file-medical',         'label' => 'Prontuários'],
                ['href' => 'receita.php',             'icon' => 'bi bi-receipt',              'label' => 'Receitas'],
                ['href' => 'exame.php',               'icon' => 'bi bi-file-earmark-medical', 'label' => 'Exames'],
                ['href' => 'monitoramento.php',       'icon' => 'bi bi-heart-pulse',          'label' => 'Monitoramentos'],
            ];
        } elseif ($role === 'medico') {
            $dropdownLinks = [
                ['href' => 'paciente.php',    'icon' => 'bi bi-person',                'label' => 'Pacientes'],
                ['href' => 'medico.php',      'icon' => 'bi bi-person-badge',          'label' => 'Médicos'],
                ['href' => 'instituicao.php', 'icon' => 'bi bi-building',              'label' => 'Instituições'],
                ['href' => 'medicamento.php', 'icon' => 'bi bi-capsule',               'label' => 'Medicamentos'],
                ['href' => 'agendamento.php', 'icon' => 'bi bi-calendar-check',        'label' => 'Agendamentos'],
                ['href' => 'consulta.php',    'icon' => 'bi bi-clipboard-pulse',       'label' => 'Consultas'],
                ['href' => 'prontuario.php',  'icon' => 'bi bi-file-earmark-medical',  'label' => 'Prontuários'],
                ['href' => 'receita.php',     'icon' => 'bi bi-receipt',               'label' => 'Receitas'],
                ['href' => 'exame.php',       'icon' => 'bi bi-file-earmark-medical',  'label' => 'Exames'],
                ['href' => 'monitoramento.php','icon' => 'bi bi-heart-pulse',          'label' => 'Monitoramentos'],
                ['href' => 'declaracao.php',  'icon' => 'bi bi-file-earmark-text',     'label' => 'Declarações'],
            ];
        }

        $initials = '';
        if ($userName) {
            $parts = explode(' ', trim($userName));
            $initials = strtoupper(substr($parts[0], 0, 1));
            if (count($parts) > 1) {
                $initials .= strtoupper(substr(end($parts), 0, 1));
            }
        }

        ?>
        <style>
            :root {
                --sidebar-width: 260px;
                --sidebar-collapsed-width: 70px;
                --azul: #2563eb;
                --azul-escuro: #1e40af;
                --azul-mais-escuro: #1e3a8a;
            }

            body.with-sidebar {
                margin-left: var(--sidebar-width);
                transition: margin-left 0.3s ease;
                padding-top: 0 !important;
            }

            body.with-sidebar.sidebar-collapsed {
                margin-left: var(--sidebar-collapsed-width);
            }

            /* ── Sidebar ── */
            .smart-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: var(--sidebar-width);
                background: linear-gradient(180deg, var(--azul-mais-escuro) 0%, var(--azul-escuro) 40%, var(--azul) 100%);
                display: flex;
                flex-direction: column;
                z-index: 1050;
                transition: width 0.3s ease;
                overflow: hidden;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            }

            .smart-sidebar.collapsed {
                width: var(--sidebar-collapsed-width);
            }

            /* ── Topo: logo + toggle ── */
            .sidebar-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1.25rem 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                min-height: 70px;
                flex-shrink: 0;
            }

            .sidebar-brand {
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
                overflow: hidden;
                white-space: nowrap;
                min-width: 0;
            }

            .sidebar-brand img {
                height: 32px;
                flex-shrink: 0;
                transition: height 0.3s ease;
            }

            .sidebar-brand span {
                color: white;
                font-weight: 700;
                font-size: 1rem;
                letter-spacing: 0.5px;
                transition: opacity 0.2s ease, width 0.3s ease;
                overflow: hidden;
            }

            .smart-sidebar.collapsed .sidebar-brand span {
                opacity: 0;
                width: 0;
            }

            .smart-sidebar.collapsed .sidebar-brand img {
                height: 26px;
            }

            .smart-sidebar.collapsed .sidebar-top {
                justify-content: center;
                padding: 1.25rem 0.5rem;
            }

            .smart-sidebar.collapsed .sidebar-toggle {
                display: none;
            }

            .sidebar-toggle {
                background: rgba(255, 255, 255, 0.15);
                border: none;
                color: white;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                flex-shrink: 0;
                transition: background 0.2s ease;
            }

            .sidebar-toggle:hover {
                background: rgba(255, 255, 255, 0.25);
            }

            /* ── Avatar do usuário ── */
            .sidebar-user {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                overflow: hidden;
                flex-shrink: 0;
            }

            .sidebar-avatar {
                width: 38px;
                height: 38px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                border: 2px solid rgba(255, 255, 255, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 0.85rem;
                color: white;
                flex-shrink: 0;
            }

            .sidebar-user-info {
                overflow: hidden;
                white-space: nowrap;
                transition: opacity 0.2s ease;
            }

            .sidebar-user-info .name {
                color: white;
                font-weight: 600;
                font-size: 0.88rem;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .sidebar-user-info .role-badge {
                display: inline-block;
                background: rgba(255, 255, 255, 0.2);
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.7rem;
                padding: 1px 8px;
                border-radius: 20px;
                margin-top: 2px;
            }

            .smart-sidebar.collapsed .sidebar-user-info {
                opacity: 0;
                width: 0;
            }

            /* ── Menu de navegação ── */
            .sidebar-nav {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 0.75rem 0;
                scrollbar-width: thin;
                scrollbar-color: rgba(255,255,255,0.2) transparent;
            }

            .sidebar-nav::-webkit-scrollbar {
                width: 4px;
            }

            .sidebar-nav::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 4px;
            }

            .sidebar-section-title {
                color: rgba(255, 255, 255, 0.45);
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1.2px;
                padding: 0.75rem 1.1rem 0.3rem;
                white-space: nowrap;
                overflow: hidden;
                transition: opacity 0.2s ease;
            }

            .smart-sidebar.collapsed .sidebar-section-title {
                opacity: 0;
            }

            .sidebar-link {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 0.6rem 1.1rem;
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                border-radius: 10px;
                margin: 1px 0.5rem;
                white-space: nowrap;
                overflow: hidden;
                transition: background 0.2s ease, color 0.2s ease;
                position: relative;
            }

            .sidebar-link:hover {
                background: rgba(255, 255, 255, 0.15);
                color: white;
            }

            .sidebar-link.active {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                font-weight: 600;
            }

            .sidebar-link i {
                font-size: 1.1rem;
                flex-shrink: 0;
                width: 22px;
                text-align: center;
            }

            .sidebar-link span {
                transition: opacity 0.2s ease;
                font-size: 0.9rem;
            }

            .smart-sidebar.collapsed .sidebar-link span {
                opacity: 0;
                width: 0;
            }

            /* Tooltip no modo colapsado */
            .smart-sidebar.collapsed .sidebar-link::after {
                content: attr(data-label);
                position: absolute;
                left: calc(var(--sidebar-collapsed-width) + 8px);
                background: #1e293b;
                color: white;
                font-size: 0.82rem;
                padding: 5px 10px;
                border-radius: 6px;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
                z-index: 9999;
            }

            .smart-sidebar.collapsed .sidebar-link:hover::after {
                opacity: 1;
            }

            /* ── Rodapé: botão agendar + sair ── */
            .sidebar-footer {
                padding: 0.75rem 0.5rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .btn-agendar {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: rgba(255, 255, 255, 0.15);
                color: white;
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 10px;
                padding: 0.6rem 1rem;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.88rem;
                white-space: nowrap;
                overflow: hidden;
                transition: background 0.2s ease;
            }

            .btn-agendar:hover {
                background: rgba(255, 255, 255, 0.25);
                color: white;
            }

            .btn-agendar span {
                transition: opacity 0.2s ease;
            }

            .smart-sidebar.collapsed .btn-agendar span {
                opacity: 0;
                width: 0;
            }

            .btn-sair {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: rgba(239, 68, 68, 0.2);
                color: rgba(255, 180, 180, 0.95);
                border: 1px solid rgba(239, 68, 68, 0.3);
                border-radius: 10px;
                padding: 0.6rem 1rem;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.88rem;
                white-space: nowrap;
                overflow: hidden;
                transition: background 0.2s ease;
            }

            .btn-sair:hover {
                background: rgba(239, 68, 68, 0.35);
                color: white;
            }

            .btn-sair span {
                transition: opacity 0.2s ease;
            }

            .smart-sidebar.collapsed .btn-sair span {
                opacity: 0;
                width: 0;
            }

            /* ── Overlay mobile ── */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }

            /* ── Botão hamburguer mobile (aparece só em telas pequenas) ── */
            .sidebar-mobile-toggle {
                display: none;
                position: fixed;
                top: 12px;
                left: 12px;
                z-index: 1060;
                background: var(--azul-escuro);
                border: none;
                color: white;
                width: 42px;
                height: 42px;
                border-radius: 10px;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }

            /* ── Responsivo ── */
            @media (max-width: 991px) {
                body.with-sidebar {
                    margin-left: 0 !important;
                    padding-top: 60px !important;
                }

                .smart-sidebar {
                    transform: translateX(-100%);
                    width: var(--sidebar-width) !important;
                    transition: transform 0.3s ease;
                }

                .smart-sidebar.mobile-open {
                    transform: translateX(0);
                }

                .sidebar-overlay.active {
                    display: block;
                }

                .sidebar-mobile-toggle {
                    display: flex;
                }

                .sidebar-toggle {
                    display: none;
                }
            }
        </style>

        <!-- Botão hamburguer mobile -->
        <button class="sidebar-mobile-toggle" id="mobileSidebarToggle" aria-label="Abrir menu">
            <i class="bi bi-list" style="font-size: 1.4rem;"></i>
        </button>

        <!-- Overlay mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- ════ SIDEBAR ════ -->
        <aside class="smart-sidebar" id="smartSidebar">

            <!-- Topo -->
            <div class="sidebar-top">
                <?php if ($role === 'paciente' || $role === 'medico'): ?>
                <div class="sidebar-brand" style="cursor:default;">
                    <img src="../img/logob.png" alt="Logo Smart Clinic">
                    <span>SMART CLINIC</span>
                </div>
                <?php else: ?>
                <a class="sidebar-brand" href="index.php">
                    <img src="../img/logob.png" alt="Logo Smart Clinic">
                    <span>SMART CLINIC</span>
                </a>
                <?php endif; ?>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Recolher menu">
                    <i class="bi bi-layout-sidebar-reverse" style="font-size: 1rem;"></i>
                </button>
            </div>

            <?php if ($role === 'paciente' || $role === 'medico'): ?>

            <!-- Avatar -->
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?php echo htmlspecialchars($initials); ?></div>
                <div class="sidebar-user-info">
                    <div class="name"><?php echo htmlspecialchars($userName); ?></div>
                    <span class="role-badge">
                        <?php echo $role === 'medico' ? '👨‍⚕️ Médico' : '👤 Paciente'; ?>
                    </span>
                </div>
            </div>

            <!-- Links de navegação -->
            <nav class="sidebar-nav" aria-label="Menu principal">
                <div class="sidebar-section-title">Menu</div>
                <?php
                $currentPage = basename($_SERVER['PHP_SELF']);
                foreach ($dropdownLinks as $link):
                    $isActive = ($currentPage === $link['href']) ? 'active' : '';
                ?>
                <a href="<?php echo $link['href']; ?>"
                   class="sidebar-link <?php echo $isActive; ?>"
                   data-label="<?php echo htmlspecialchars($link['label']); ?>">
                    <i class="<?php echo $link['icon']; ?>"></i>
                    <span><?php echo htmlspecialchars($link['label']); ?></span>
                </a>
                <?php endforeach; ?>
            </nav>

            <!-- Rodapé -->
            <div class="sidebar-footer">
                <a href="<?php echo $serviceButton['href']; ?>" class="btn-agendar" data-label="Agendar">
                    <i class="<?php echo $serviceButton['icon']; ?>" style="font-size: 1rem; flex-shrink:0;"></i>
                    <span><?php echo $serviceButton['label']; ?></span>
                </a>
                <a href="logout.php" class="btn-sair" data-label="Sair">
                    <i class="bi bi-box-arrow-right" style="font-size: 1rem; flex-shrink:0;"></i>
                    <span>Sair</span>
                </a>
            </div>

            <?php else: ?>

            <!-- Visitante: só botão de login -->
            <div class="sidebar-footer" style="margin-top: auto;">
                <a href="login.php" class="btn-agendar" data-label="Login">
                    <i class="bi bi-box-arrow-in-right" style="font-size: 1rem; flex-shrink:0;"></i>
                    <span>Login</span>
                </a>
            </div>

            <?php endif; ?>

        </aside>
        <!-- ════ FIM SIDEBAR ════ -->

        <script>
        (function () {
            const sidebar  = document.getElementById('smartSidebar');
            const toggle   = document.getElementById('sidebarToggle');
            const mToggle  = document.getElementById('mobileSidebarToggle');
            const overlay  = document.getElementById('sidebarOverlay');
            const body     = document.body;
            const KEY      = 'sc_sidebar_collapsed';

            // Ativa margem lateral no body
            body.classList.add('with-sidebar');

            // Estado salvo — fechada por padrão se nunca abriu antes
            const saved = localStorage.getItem(KEY);
            if (saved === null || saved === '1') {
                sidebar.classList.add('collapsed');
                body.classList.add('sidebar-collapsed');
                if (saved === null) localStorage.setItem(KEY, '1');
            }

            // Desktop: recolher/expandir
            if (toggle) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                    body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem(KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
                });
            }

            // Clicar na logo quando colapsado também expande
            const brand = sidebar.querySelector('.sidebar-brand');
            if (brand) {
                brand.addEventListener('click', function (e) {
                    if (sidebar.classList.contains('collapsed')) {
                        e.preventDefault();
                        sidebar.classList.remove('collapsed');
                        body.classList.remove('sidebar-collapsed');
                        localStorage.setItem(KEY, '0');
                    }
                });
            }

            // Mobile: abrir
            if (mToggle) {
                mToggle.addEventListener('click', function () {
                    sidebar.classList.add('mobile-open');
                    overlay.classList.add('active');
                });
            }

            // Mobile: fechar pelo overlay
            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                });
            }
        })();
        </script>
        <?php
    }
}

if (!function_exists('renderNavbarTop')) {
    function renderNavbarTop(): void {
        $role = $_SESSION['role'] ?? null;
        ?>
        <style>
            .navbar-top {
                background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
                padding: 0.85rem 0;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1050;
            }

            .navbar-top .brand {
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
                color: white;
                font-weight: 700;
                font-size: 1.1rem;
                letter-spacing: 0.5px;
            }

            .navbar-top .brand img {
                height: 38px;
            }

            .navbar-top .nav-actions {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .navbar-top .btn-nav-outline {
                color: white;
                border: 1.5px solid rgba(255, 255, 255, 0.5);
                border-radius: 50px;
                padding: 0.45rem 1.2rem;
                font-weight: 600;
                font-size: 0.9rem;
                text-decoration: none;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .navbar-top .btn-nav-outline:hover {
                background: rgba(255, 255, 255, 0.15);
                border-color: white;
                color: white;
            }

            .navbar-top .btn-nav-solid {
                background: white;
                color: #1e40af;
                border-radius: 50px;
                padding: 0.45rem 1.2rem;
                font-weight: 700;
                font-size: 0.9rem;
                text-decoration: none;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 6px;
                border: none;
            }

            .navbar-top .btn-nav-solid:hover {
                background: #e0e7ff;
                color: #1e3a8a;
            }

            body.with-navbar-top {
                padding-top: 70px !important;
                margin-left: 0 !important;
            }
        </style>

        <nav class="navbar-top">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="brand" href="index.php">
                    <img src="../img/logob.png" alt="Logo Smart Clinic">
                    SMART CLINIC
                </a>
                <div class="nav-actions">
                    <?php if ($role === 'paciente' || $role === 'medico'): ?>
                        <a href="agendamento.php" class="btn-nav-outline">
                            <i class="bi bi-calendar-check"></i> Agendar Consulta
                        </a>
                        <a href="<?php echo $role === 'medico' ? 'medico_dashboard.php' : 'paciente_dashboard.php'; ?>" class="btn-nav-solid">
                            <i class="bi bi-grid"></i> Meu Portal
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn-nav-solid">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <script>document.body.classList.add('with-navbar-top');</script>
        <?php
    }
}
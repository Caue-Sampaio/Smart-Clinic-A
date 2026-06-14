<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('renderNavbar')) {
    function renderNavbar(): void {
        $role      = $_SESSION['role']       ?? null;
        $userName  = $_SESSION['user_name']  ?? 'Usuário';
        $userEmail = $_SESSION['user_email'] ?? '';

        // ── Links por perfil ─────────────────────────────────────────────────
        $dropdownLinks  = [];
        $serviceButton  = ['href'=>'agendamento.php','icon'=>'bi bi-calendar-check','label'=>'Agendar Consulta'];

        if ($role === 'admin') {
            // Admin: acesso total
            $dropdownLinks = [
                // Gestão
                ['section' => 'Gestão'],
                ['href'=>'paciente.php',    'icon'=>'bi bi-people',                'label'=>'Pacientes'],
                ['href'=>'medico.php',      'icon'=>'bi bi-person-badge',          'label'=>'Médicos'],
                ['href'=>'instituicao.php', 'icon'=>'bi bi-building',              'label'=>'Instituições'],
                ['href'=>'medicamento.php', 'icon'=>'bi bi-capsule',               'label'=>'Medicamentos'],
                // Clínico
                ['section' => 'Clínico'],
                ['href'=>'agendamento.php', 'icon'=>'bi bi-calendar-check',        'label'=>'Agendamentos'],
                ['href'=>'consulta.php',    'icon'=>'bi bi-clipboard-pulse',       'label'=>'Consultas'],
                ['href'=>'receita.php',     'icon'=>'bi bi-receipt',               'label'=>'Receitas'],
                ['href'=>'exame.php',       'icon'=>'bi bi-file-earmark-medical',  'label'=>'Exames'],
                ['href'=>'monitoramento.php','icon'=>'bi bi-heart-pulse',          'label'=>'Monitoramentos'],
                ['href'=>'declaracao.php',  'icon'=>'bi bi-file-earmark-text',     'label'=>'Declarações'],
            ];
            $serviceButton = ['href'=>'paciente.php','icon'=>'bi bi-shield-lock','label'=>'Painel Admin'];

        } elseif ($role === 'medico') {
            // Médico: vê pacientes (só leitura), sem médicos nem instituições
            $dropdownLinks = [
                ['section' => 'Atendimento'],
                ['href'=>'paciente.php',    'icon'=>'bi bi-people',                'label'=>'Pacientes'],
                ['href'=>'medicamento.php', 'icon'=>'bi bi-capsule',               'label'=>'Medicamentos'],
                ['section' => 'Clínico'],
                ['href'=>'agendamento.php', 'icon'=>'bi bi-calendar-check',        'label'=>'Agendamentos'],
                ['href'=>'consulta.php',    'icon'=>'bi bi-clipboard-pulse',       'label'=>'Consultas'],
                ['href'=>'receita.php',     'icon'=>'bi bi-receipt',               'label'=>'Receitas'],
                ['href'=>'exame.php',       'icon'=>'bi bi-file-earmark-medical',  'label'=>'Exames'],
                ['href'=>'monitoramento.php','icon'=>'bi bi-heart-pulse',          'label'=>'Monitoramentos'],
                ['href'=>'declaracao.php',  'icon'=>'bi bi-file-earmark-text',     'label'=>'Declarações'],
            ];

        } elseif ($role === 'paciente') {
            $dropdownLinks = [
                ['section' => 'Minha Área'],
                ['href'=>'paciente_dashboard.php','icon'=>'bi bi-person-circle',   'label'=>'Meu Portal'],
                ['href'=>'agendamento.php',       'icon'=>'bi bi-calendar-check',  'label'=>'Agendamentos'],
                ['href'=>'consulta.php',          'icon'=>'bi bi-clipboard-pulse', 'label'=>'Consultas'],
                ['href'=>'receita.php',           'icon'=>'bi bi-receipt',         'label'=>'Receitas'],
                ['href'=>'exame.php',             'icon'=>'bi bi-file-earmark-medical','label'=>'Exames'],
                ['href'=>'monitoramento.php',     'icon'=>'bi bi-heart-pulse',     'label'=>'Monitoramentos'],
            ];
        }

        // Iniciais do nome
        $initials = '';
        if ($userName) {
            $parts    = explode(' ', trim($userName));
            $initials = strtoupper(substr($parts[0], 0, 1));
            if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
        }

        $roleBadgeMap = [
            'admin'    => '🛡️ Admin',
            'medico'   => '👨‍⚕️ Médico',
            'paciente' => '👤 Paciente',
        ];
        $roleBadge = $roleBadgeMap[$role] ?? '';
        ?>
        <style>
            :root { --sidebar-width:260px; --sidebar-collapsed-width:70px; --azul:#2563eb; --azul-escuro:#1e40af; --azul-mais-escuro:#1e3a8a; }
            body.with-sidebar { margin-left:var(--sidebar-width); transition:margin-left .3s ease; padding-top:0 !important; }
            body.with-sidebar.sidebar-collapsed { margin-left:var(--sidebar-collapsed-width); }
            .smart-sidebar { position:fixed; top:0; left:0; height:100vh; width:var(--sidebar-width); background:linear-gradient(180deg,var(--azul-mais-escuro) 0%,var(--azul-escuro) 40%,var(--azul) 100%); display:flex; flex-direction:column; z-index:1050; transition:width .3s ease; overflow:hidden; box-shadow:4px 0 20px rgba(0,0,0,.15); }
            .smart-sidebar.collapsed { width:var(--sidebar-collapsed-width); }
            .sidebar-top { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1rem; border-bottom:1px solid rgba(255,255,255,.1); min-height:70px; flex-shrink:0; }
            .sidebar-brand { display:flex; align-items:center; gap:10px; text-decoration:none; overflow:hidden; white-space:nowrap; min-width:0; }
            .sidebar-brand img { height:32px; flex-shrink:0; transition:height .3s ease; }
            .sidebar-brand span { color:white; font-weight:700; font-size:1rem; letter-spacing:.5px; transition:opacity .2s ease,width .3s ease; overflow:hidden; }
            .smart-sidebar.collapsed .sidebar-brand span { opacity:0; width:0; }
            .smart-sidebar.collapsed .sidebar-brand img { height:26px; }
            .smart-sidebar.collapsed .sidebar-top { justify-content:center; padding:1.25rem .5rem; }
            .smart-sidebar.collapsed .sidebar-toggle { display:none; }
            .sidebar-toggle { background:rgba(255,255,255,.15); border:none; color:white; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; transition:background .2s ease; }
            .sidebar-toggle:hover { background:rgba(255,255,255,.25); }
            .sidebar-user { display:flex; align-items:center; gap:10px; padding:1rem; border-bottom:1px solid rgba(255,255,255,.1); overflow:hidden; flex-shrink:0; }
            .sidebar-avatar { width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.2); border:2px solid rgba(255,255,255,.4); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.85rem; color:white; flex-shrink:0; }
            .sidebar-user-info { overflow:hidden; white-space:nowrap; transition:opacity .2s ease; }
            .sidebar-user-info .name { color:white; font-weight:600; font-size:.88rem; overflow:hidden; text-overflow:ellipsis; }
            .sidebar-user-info .role-badge { display:inline-block; background:rgba(255,255,255,.2); color:rgba(255,255,255,.9); font-size:.7rem; padding:1px 8px; border-radius:20px; margin-top:2px; }
            .smart-sidebar.collapsed .sidebar-user-info { opacity:0; width:0; }

            /* badge especial para admin */
            .sidebar-user-info .role-badge.admin-badge { background:rgba(251,191,36,.25); color:#fde68a; border:1px solid rgba(251,191,36,.3); }

            .sidebar-nav { flex:1; overflow-y:auto; overflow-x:hidden; padding:.75rem 0; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.2) transparent; }
            .sidebar-nav::-webkit-scrollbar { width:4px; }
            .sidebar-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,.2); border-radius:4px; }
            .sidebar-section-title { color:rgba(255,255,255,.45); font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; padding:.75rem 1.1rem .3rem; white-space:nowrap; overflow:hidden; transition:opacity .2s ease; }
            .smart-sidebar.collapsed .sidebar-section-title { opacity:0; }
            .sidebar-link { display:flex; align-items:center; gap:12px; padding:.6rem 1.1rem; color:rgba(255,255,255,.8); text-decoration:none; border-radius:10px; margin:1px .5rem; white-space:nowrap; overflow:hidden; transition:background .2s ease,color .2s ease; position:relative; }
            .sidebar-link:hover { background:rgba(255,255,255,.15); color:white; }
            .sidebar-link.active { background:rgba(255,255,255,.2); color:white; font-weight:600; }
            .sidebar-link i { font-size:1.1rem; flex-shrink:0; width:22px; text-align:center; }
            .sidebar-link span { transition:opacity .2s ease; font-size:.9rem; }
            .smart-sidebar.collapsed .sidebar-link span { opacity:0; width:0; }
            .smart-sidebar.collapsed .sidebar-link::after { content:attr(data-label); position:absolute; left:calc(var(--sidebar-collapsed-width) + 8px); background:#1e293b; color:white; font-size:.82rem; padding:5px 10px; border-radius:6px; white-space:nowrap; opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:9999; }
            .smart-sidebar.collapsed .sidebar-link:hover::after { opacity:1; }
            .sidebar-footer { padding:.75rem .5rem; border-top:1px solid rgba(255,255,255,.1); flex-shrink:0; display:flex; flex-direction:column; gap:6px; }
            .btn-agendar { display:flex; align-items:center; justify-content:center; gap:8px; background:rgba(255,255,255,.15); color:white; border:1px solid rgba(255,255,255,.3); border-radius:10px; padding:.6rem 1rem; text-decoration:none; font-weight:600; font-size:.88rem; white-space:nowrap; overflow:hidden; transition:background .2s ease; }
            .btn-agendar:hover { background:rgba(255,255,255,.25); color:white; }
            .btn-agendar span { transition:opacity .2s ease; }
            .smart-sidebar.collapsed .btn-agendar span { opacity:0; width:0; }
            .btn-sair { display:flex; align-items:center; justify-content:center; gap:8px; background:rgba(239,68,68,.2); color:rgba(255,180,180,.95); border:1px solid rgba(239,68,68,.3); border-radius:10px; padding:.6rem 1rem; text-decoration:none; font-weight:600; font-size:.88rem; white-space:nowrap; overflow:hidden; transition:background .2s ease; }
            .btn-sair:hover { background:rgba(239,68,68,.35); color:white; }
            .btn-sair span { transition:opacity .2s ease; }
            .smart-sidebar.collapsed .btn-sair span { opacity:0; width:0; }
            .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1040; }
            .sidebar-mobile-toggle { display:none; position:fixed; top:12px; left:12px; z-index:1060; background:var(--azul-escuro); border:none; color:white; width:42px; height:42px; border-radius:10px; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,.2); }
            @media (max-width:991px) {
                body.with-sidebar { margin-left:0 !important; padding-top:60px !important; }
                .smart-sidebar { transform:translateX(-100%); width:var(--sidebar-width) !important; transition:transform .3s ease; }
                .smart-sidebar.mobile-open { transform:translateX(0); }
                .sidebar-overlay.active { display:block; }
                .sidebar-mobile-toggle { display:flex; }
                .sidebar-toggle { display:none; }
            }
        </style>

        <button class="sidebar-mobile-toggle" id="mobileSidebarToggle" aria-label="Abrir menu">
            <i class="bi bi-list" style="font-size:1.4rem;"></i>
        </button>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="smart-sidebar" id="smartSidebar">

            <div class="sidebar-top">
                <?php if ($role === 'paciente' || $role === 'medico'): ?>
                <div class="sidebar-brand" style="cursor:default;">
                <?php else: ?>
                <a class="sidebar-brand" href="index.php">
                <?php endif; ?>
                    <img src="../img/logob.png" alt="Logo Smart Clinic">
                    <span>SMART CLINIC</span>
                <?php echo ($role === 'paciente' || $role === 'medico') ? '</div>' : '</a>'; ?>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Recolher menu">
                    <i class="bi bi-layout-sidebar-reverse" style="font-size:1rem;"></i>
                </button>
            </div>

            <?php if ($role): ?>
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= htmlspecialchars($initials) ?></div>
                <div class="sidebar-user-info">
                    <div class="name"><?= htmlspecialchars($userName) ?></div>
                    <span class="role-badge <?= $role === 'admin' ? 'admin-badge' : '' ?>">
                        <?= $roleBadge ?>
                    </span>
                </div>
            </div>

            <nav class="sidebar-nav" aria-label="Menu principal">
                <?php
                $currentPage = basename($_SERVER['PHP_SELF']);
                foreach ($dropdownLinks as $link):
                    if (isset($link['section'])):
                ?>
                <div class="sidebar-section-title"><?= htmlspecialchars($link['section']) ?></div>
                <?php else:
                    $isActive = ($currentPage === $link['href']) ? 'active' : '';
                ?>
                <a href="<?= $link['href'] ?>"
                   class="sidebar-link <?= $isActive ?>"
                   data-label="<?= htmlspecialchars($link['label']) ?>">
                    <i class="<?= $link['icon'] ?>"></i>
                    <span><?= htmlspecialchars($link['label']) ?></span>
                </a>
                <?php endif; endforeach; ?>

                <?php if ($role === 'admin' || $role === 'medico'): ?>
                <div class="sidebar-section-title">Conta</div>
                <a href="perfil.php" class="sidebar-link <?= ($currentPage==='perfil.php')?'active':'' ?>" data-label="Perfil">
                    <i class="bi bi-person-circle"></i>
                    <span>Meu Perfil</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= $serviceButton['href'] ?>" class="btn-agendar" data-label="<?= $serviceButton['label'] ?>">
                    <i class="<?= $serviceButton['icon'] ?>" style="font-size:1rem;flex-shrink:0;"></i>
                    <span><?= $serviceButton['label'] ?></span>
                </a>
                <a href="logout.php" class="btn-sair" data-label="Sair">
                    <i class="bi bi-box-arrow-right" style="font-size:1rem;flex-shrink:0;"></i>
                    <span>Sair</span>
                </a>
            </div>

            <?php else: ?>
            <div class="sidebar-footer" style="margin-top:auto;">
                <a href="login.php" class="btn-agendar" data-label="Login">
                    <i class="bi bi-box-arrow-in-right" style="font-size:1rem;flex-shrink:0;"></i>
                    <span>Login</span>
                </a>
            </div>
            <?php endif; ?>

        </aside>

        <script>
        (function(){
            const sidebar = document.getElementById('smartSidebar');
            const toggle  = document.getElementById('sidebarToggle');
            const mToggle = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const body    = document.body;
            const KEY     = 'sc_sidebar_collapsed';
            body.classList.add('with-sidebar');
            const saved = localStorage.getItem(KEY);
            if (saved === null || saved === '1') {
                sidebar.classList.add('collapsed');
                body.classList.add('sidebar-collapsed');
                if (saved === null) localStorage.setItem(KEY,'1');
            }
            if (toggle) {
                toggle.addEventListener('click', function(){
                    sidebar.classList.toggle('collapsed');
                    body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem(KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
                });
            }
            const brand = sidebar.querySelector('.sidebar-brand');
            if (brand) {
                brand.addEventListener('click', function(e){
                    if (sidebar.classList.contains('collapsed')) {
                        e.preventDefault();
                        sidebar.classList.remove('collapsed');
                        body.classList.remove('sidebar-collapsed');
                        localStorage.setItem(KEY,'0');
                    }
                });
            }
            if (mToggle) mToggle.addEventListener('click', function(){ sidebar.classList.add('mobile-open'); overlay.classList.add('active'); });
            if (overlay) overlay.addEventListener('click', function(){ sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); });
        })();
        </script>
        <?php
    }
}
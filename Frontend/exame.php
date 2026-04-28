<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Exames</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --azul: #2563eb;
            --azul-escuro: #1e40af;
            --verde: #22c55e;
            --fundo: #f1f5f9;
        }

        body {
            background-color: var(--fundo);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .btn-verde {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-verde:hover {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }

        .btn-azul {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-azul:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .card-modern {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: none;
            transition: all 0.3s ease;
        }
        .card-modern:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .table-modern {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .table-modern thead {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: #fff;
        }
        .table-modern th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        .table-modern td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .table-modern tr:hover {
            background-color: #f8fafc;
        }

        .navbar {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #1e3a8a 100%) !important;
            padding: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #22c55e !important;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .dropdown-item {
            transition: all 0.2s ease;
            padding: 0.5rem 1rem;
        }
        .dropdown-item:hover {
            background-color: #2563eb;
            color: #fff;
        }

        .page-header {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            color: #fff;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
                <i class="bi bi-hospital me-2"></i>SMART CLINIC
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-1"></i>Gerenciar Dados
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="paciente.php"><i class="bi bi-people me-2"></i>Pacientes</a></li>
                            <li><a class="dropdown-item" href="medico.php"><i class="bi bi-doctor me-2"></i>Médicos</a></li>
                            <li><a class="dropdown-item" href="agendamento.php"><i class="bi bi-calendar-check me-2"></i>Agendamentos</a></li>
                            <li><a class="dropdown-item" href="consulta.php"><i class="bi bi-clipboard-pulse me-2"></i>Consultas</a></li>
                            <li><a class="dropdown-item" href="prescrever.php"><i class="bi bi-prescription me-2"></i>Prescrever</a></li>
                            <li><a class="dropdown-item" href="instituicao.php"><i class="bi bi-building me-2"></i>Instituições</a></li>
                            <li><a class="dropdown-item" href="medicamento.php"><i class="bi bi-capsule me-2"></i>Medicamentos</a></li>
                            <li><a class="dropdown-item" href="declaracao.php"><i class="bi bi-file-earmark-text me-2"></i>Declarações</a></li>
                            <li><a class="dropdown-item" href="exame.php"><i class="bi bi-file-earmark-medical me-2"></i>Exames</a></li>
                            <li><a class="dropdown-item" href="monitoramento.php"><i class="bi bi-heart-pulse me-2"></i>Monitoramentos</a></li>
                            <li><a class="dropdown-item" href="prontuario.php"><i class="bi bi-file-person me-2"></i>Prontuários</a></li>
                            <li><a class="dropdown-item" href="receita.php"><i class="bi bi-receipt me-2"></i>Receitas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0 ms-lg-3">
                        <a href="logout.php" class="btn btn-outline-light fw-semibold px-4 rounded-pill">
                            <i class="bi bi-box-arrow-right me-2"></i>Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/ExameController.php';
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/SolicitacaoController.php';
            $controller = new ExameController();
            $solicitacaoController = new SolicitacaoController();
            $solicitacoes = $solicitacaoController->getAll();

            $action = isset($_GET['action']) ? $_GET['action'] : 'list';
            $exame = null;

            if ($action == 'edit' && isset($_GET['id'])) {
                $exame = $controller->getById($_GET['id']);
                if (!$exame) {
                    echo "<p>Exame não encontrado.</p>";
                    exit;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['delete_id'])) {
                    $controller->delete($_POST['delete_id']);
                    header('Location: exame.php');
                    exit;
                } elseif ($action == 'create') {
                    $data = [
                        'fk_solicitacao_cod' => $_POST['fk_solicitacao_cod'],
                        'arquivo' => $_POST['arquivo'] ?: null
                    ];
                    $controller->create($data);
                    header('Location: exame.php');
                    exit;
                } elseif ($action == 'edit' && isset($_POST['cod'])) {
                    $data = [
                        'fk_solicitacao_cod' => $_POST['fk_solicitacao_cod'],
                        'arquivo' => $_POST['arquivo'] ?: null
                    ];
                    $controller->update($_POST['cod'], $data);
                    header('Location: exame.php');
                    exit;
                }
            }

            if ($action == 'list') {
                ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-azul fw-bold">Lista de Exames</h2>
                    <?php if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente'): ?>
                    <a href="exame.php?action=create" class="btn btn-verde">Adicionar Novo Exame</a>
                    <?php endif; ?>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Solicitação</th>
                                <th>Arquivo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $exames = $controller->getAll();
                            foreach ($exames as $ex) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($ex['cod']) . "</td>";
                                echo "<td>" . htmlspecialchars($ex['solicitacao_tipo'] . ' - ' . $ex['solicitacao_motivo']) . "</td>";
                                echo "<td>" . htmlspecialchars($ex['arquivo']) . "</td>";
                                echo "<td>";
                                if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente') {
                                    echo "<a href='exame.php?action=edit&id=" . $ex['cod'] . "' class='btn btn-sm btn-primary me-2'>Editar</a>";
                                    echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja deletar?\")'>";
                                    echo "<input type='hidden' name='delete_id' value='" . $ex['cod'] . "'>";
                                    echo "<button type='submit' class='btn btn-sm btn-danger'>Deletar</button>";
                                    echo "</form>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } elseif ($action == 'create' || $action == 'edit') {
                $title = $action == 'create' ? 'Adicionar Novo Exame' : 'Editar Exame';
                ?>
                <h2 class="text-azul fw-bold mb-4"><?php echo $title; ?></h2>
                
                <form method="POST" action="">
                    <?php if ($action == 'edit') { ?>
                        <input type="hidden" name="cod" value="<?php echo htmlspecialchars($exame['cod']); ?>">
                    <?php } ?>
                    <div class="mb-3">
                        <label for="fk_solicitacao_cod" class="form-label">Solicitação</label>
                        <select class="form-control" id="fk_solicitacao_cod" name="fk_solicitacao_cod" required>
                            <option value="">Selecione uma solicitação</option>
                            <?php foreach ($solicitacoes as $sol) { 
                                $selected = ($action == 'edit' && $sol['cod'] == $exame['fk_solicitacao_cod']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo htmlspecialchars($sol['cod']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($sol['tipo'] . ' - ' . $sol['motivo']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="arquivo" class="form-label">Arquivo</label>
                        <input type="text" class="form-control" id="arquivo" name="arquivo" value="<?php echo $action == 'edit' ? htmlspecialchars($exame['arquivo']) : ''; ?>" placeholder="Caminho ou nome do arquivo">
                    </div>
                    <button type="submit" class="btn btn-verde">Salvar</button>
                    <a href="exame.php" class="btn btn-secondary">Cancelar</a>
                </form>
                <?php
            }
            ?>
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
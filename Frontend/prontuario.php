<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Prontuários</title>
    
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
        body { padding-top: 76px; }
    </style>
</head>
<body>

    <?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>

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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gerenciar Dados
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="paciente.php">Pacientes</a></li>
                            <li><a class="dropdown-item" href="medico.php">Médicos</a></li>
                            <li><a class="dropdown-item" href="agendamento.php">Agendamentos</a></li>
                            <li><a class="dropdown-item" href="consulta.php">Consultas</a></li>
                            <li><a class="dropdown-item" href="prescrever.php">Prescrever</a></li>
                            <li><a class="dropdown-item" href="instituicao.php">Instituições</a></li>
                            <li><a class="dropdown-item" href="medicamento.php">Medicamentos</a></li>
                            <li><a class="dropdown-item" href="declaracao.php">Declarações</a></li>
                            <li><a class="dropdown-item" href="exame.php">Exames</a></li>
                            <li><a class="dropdown-item" href="monitoramento.php">Monitoramentos</a></li>
                            <li><a class="dropdown-item" href="prontuario.php">Prontuários</a></li>
                            <li><a class="dropdown-item" href="receita.php">Receitas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="#contato" class="btn btn-ciano fw-semibold px-4 rounded-pill">Agendar Consulta</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/ProntuarioController.php';
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/PacienteController.php';
            $controller = new ProntuarioController();
            $pacienteController = new PacienteController();
            $pacientes = $pacienteController->getAll();

            $action = isset($_GET['action']) ? $_GET['action'] : 'list';
            $prontuario = null;

            if ($action == 'edit' && isset($_GET['id'])) {
                $prontuario = $controller->getById($_GET['id']);
                if (!$prontuario) {
                    echo "<p>Prontuário não encontrado.</p>";
                    exit;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['delete_id'])) {
                    $controller->delete($_POST['delete_id']);
                    header('Location: prontuario.php');
                    exit;
                } elseif ($action == 'create') {
                    $data = [
                        'fk_paciente_cpf' => $_POST['fk_paciente_cpf'],
                        'foto' => $_POST['foto'],
                        'tipo_sanguineo' => $_POST['tipo_sanguineo'],
                        'doencas_cronicas' => $_POST['doencas_cronicas'],
                        'doencas_geneticas' => $_POST['doencas_geneticas'],
                        'doencas_autoimunes' => $_POST['doencas_autoimunes'],
                        'outros' => $_POST['outros']
                    ];
                    $controller->create($data);
                    header('Location: prontuario.php');
                    exit;
                } elseif ($action == 'edit' && isset($_POST['cod'])) {
                    $data = [
                        'fk_paciente_cpf' => $_POST['fk_paciente_cpf'],
                        'foto' => $_POST['foto'],
                        'tipo_sanguineo' => $_POST['tipo_sanguineo'],
                        'doencas_cronicas' => $_POST['doencas_cronicas'],
                        'doencas_geneticas' => $_POST['doencas_geneticas'],
                        'doencas_autoimunes' => $_POST['doencas_autoimunes'],
                        'outros' => $_POST['outros']
                    ];
                    $controller->update($_POST['cod'], $data);
                    header('Location: prontuario.php');
                    exit;
                }
            }

            if ($action == 'list') {
                ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-azul fw-bold">Lista de Prontuários</h2>
                    <?php if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente'): ?>
                    <a href="prontuario.php?action=create" class="btn btn-verde">Adicionar Novo Prontuário</a>
                    <?php endif; ?>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Paciente</th>
                                <th>Foto</th>
                                <th>Tipo Sanguíneo</th>
                                <th>Doenças Crônicas</th>
                                <th>Doenças Genéticas</th>
                                <th>Doenças Autoimunes</th>
                                <th>Outros</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $prontuarios = $controller->getAll();
                            foreach ($prontuarios as $pron) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($pron['cod']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['paciente_nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['foto']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['tipo_sanguineo']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['doencas_cronicas']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['doencas_geneticas']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['doencas_autoimunes']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['outros']) . "</td>";
                                echo "<td>";
                                if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente') {
                                    echo "<a href='prontuario.php?action=edit&id=" . $pron['cod'] . "' class='btn btn-sm btn-primary me-2'>Editar</a>";
                                    echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja deletar?\")'>";
                                    echo "<input type='hidden' name='delete_id' value='" . $pron['cod'] . "'>";
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
                $title = $action == 'create' ? 'Adicionar Novo Prontuário' : 'Editar Prontuário';
                ?>
                <h2 class="text-azul fw-bold mb-4"><?php echo $title; ?></h2>
                
                <form method="POST" action="">
                    <?php if ($action == 'edit') { ?>
                        <input type="hidden" name="cod" value="<?php echo htmlspecialchars($prontuario['cod']); ?>">
                    <?php } ?>
                    <div class="mb-3">
                        <label for="fk_paciente_cpf" class="form-label">Paciente</label>
                        <select class="form-control" id="fk_paciente_cpf" name="fk_paciente_cpf" required>
                            <?php foreach ($pacientes as $pac) { 
                                $selected = ($action == 'edit' && $pac['cpf'] == $prontuario['fk_paciente_cpf']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo htmlspecialchars($pac['cpf']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($pac['nome']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="text" class="form-control" id="foto" name="foto" placeholder="Caminho da foto" value="<?php echo $action == 'edit' ? htmlspecialchars($prontuario['foto']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                            <input type="text" class="form-control" id="tipo_sanguineo" name="tipo_sanguineo" value="<?php echo $action == 'edit' ? htmlspecialchars($prontuario['tipo_sanguineo']) : ''; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="doencas_cronicas" class="form-label">Doenças Crônicas</label>
                        <textarea class="form-control" id="doencas_cronicas" name="doencas_cronicas" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['doencas_cronicas']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="doencas_geneticas" class="form-label">Doenças Genéticas</label>
                        <textarea class="form-control" id="doencas_geneticas" name="doencas_geneticas" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['doencas_geneticas']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="doencas_autoimunes" class="form-label">Doenças Autoimunes</label>
                        <textarea class="form-control" id="doencas_autoimunes" name="doencas_autoimunes" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['doencas_autoimunes']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="outros" class="form-label">Outros</label>
                        <textarea class="form-control" id="outros" name="outros" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['outros']) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-verde">Salvar</button>
                    <a href="prontuario.php" class="btn btn-secondary">Cancelar</a>
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
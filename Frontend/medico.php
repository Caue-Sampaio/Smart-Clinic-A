<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Médicos</title>
    
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
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/MedicoController.php';
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/InstituicaoController.php';
            $controller = new MedicoController();
            $instituicaoController = new InstituicaoController();
            $instituicoes = $instituicaoController->getAll();

            $action = isset($_GET['action']) ? $_GET['action'] : 'list';
            $medico = null;

            if ($action == 'edit' && isset($_GET['id'])) {
                $medico = $controller->getById($_GET['id']);
                if (!$medico) {
                    echo "<p>Médico não encontrado.</p>";
                    exit;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['delete_id'])) {
                    $controller->delete($_POST['delete_id']);
                    header('Location: medico.php');
                    exit;
                } elseif ($action == 'create') {
                    $data = [
                        'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
                        'cpf' => $_POST['cpf'],
                        'crm' => $_POST['crm'],
                        'rqe' => $_POST['rqe'],
                        'foto' => $_POST['foto'],
                        'nome' => $_POST['nome'],
                        'email' => $_POST['email'],
                        'senha' => $_POST['senha'],
                        'especialidade' => $_POST['especialidade'],
                        'telefone' => $_POST['telefone'],
                        'endereco' => $_POST['endereco']
                    ];
                    $controller->create($data);
                    header('Location: medico.php');
                    exit;
                } elseif ($action == 'edit' && isset($_POST['cod'])) {
                    $data = [
                        'fk_instituicao_cod' => $_POST['fk_instituicao_cod'],
                        'cpf' => $_POST['cpf'],
                        'crm' => $_POST['crm'],
                        'rqe' => $_POST['rqe'],
                        'foto' => $_POST['foto'],
                        'nome' => $_POST['nome'],
                        'email' => $_POST['email'],
                        'senha' => $_POST['senha'],
                        'especialidade' => $_POST['especialidade'],
                        'telefone' => $_POST['telefone'],
                        'endereco' => $_POST['endereco']
                    ];
                    $controller->update($_POST['cod'], $data);
                    header('Location: medico.php');
                    exit;
                }
            }

            if ($action == 'list') {
                ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-azul fw-bold">Lista de Médicos</h2>
                    <a href="medico.php?action=create" class="btn btn-verde">Adicionar Novo Médico</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Instituição</th>
                                <th>CPF</th>
                                <th>CRM</th>
                                <th>RQE</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Especialidade</th>
                                <th>Telefone</th>
                                <th>Endereço</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $medicos = $controller->getAll();
                            foreach ($medicos as $med) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($med['cod']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['instituicao_nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['cpf']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['crm']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['rqe']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['foto']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['especialidade']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['telefone']) . "</td>";
                                echo "<td>" . htmlspecialchars($med['endereco']) . "</td>";
                                echo "<td>";
                                echo "<a href='medico.php?action=edit&id=" . $med['cod'] . "' class='btn btn-sm btn-primary me-2'>Editar</a>";
                                echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja deletar?\")'>";
                                echo "<input type='hidden' name='delete_id' value='" . $med['cod'] . "'>";
                                echo "<button type='submit' class='btn btn-sm btn-danger'>Deletar</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } elseif ($action == 'create' || $action == 'edit') {
                $title = $action == 'create' ? 'Adicionar Novo Médico' : 'Editar Médico';
                ?>
                <h2 class="text-azul fw-bold mb-4"><?php echo $title; ?></h2>
                
                <form method="POST" action="">
                    <?php if ($action == 'edit') { ?>
                        <input type="hidden" name="cod" value="<?php echo htmlspecialchars($medico['cod']); ?>">
                    <?php } ?>
                    <div class="mb-3">
                        <label for="fk_instituicao_cod" class="form-label">Instituição</label>
                        <select class="form-control" id="fk_instituicao_cod" name="fk_instituicao_cod" required>
                            <?php foreach ($instituicoes as $inst) { 
                                $selected = ($action == 'edit' && $inst['cod'] == $medico['fk_instituicao_cod']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo htmlspecialchars($inst['cod']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($inst['nome']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['cpf']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="crm" class="form-label">CRM</label>
                        <input type="text" class="form-control" id="crm" name="crm" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['crm']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="rqe" class="form-label">RQE</label>
                        <input type="text" class="form-control" id="rqe" name="rqe" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['rqe']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="text" class="form-control" id="foto" name="foto" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['foto']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['nome']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['senha']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="especialidade" class="form-label">Especialidade</label>
                        <input type="text" class="form-control" id="especialidade" name="especialidade" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['especialidade']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $action == 'edit' ? htmlspecialchars($medico['telefone']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <textarea class="form-control" id="endereco" name="endereco" required><?php echo $action == 'edit' ? htmlspecialchars($medico['endereco']) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-verde">Salvar</button>
                    <a href="medico.php" class="btn btn-secondary">Cancelar</a>
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

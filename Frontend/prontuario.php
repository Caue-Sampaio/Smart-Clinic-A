<?php
session_start();
?>
<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Prontuários</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>

    <?php renderNavbar(); ?>

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



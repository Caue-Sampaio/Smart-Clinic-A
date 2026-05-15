<?php
session_start();
?>
<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Receitas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php renderNavbar(); ?>

    <?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>

    <section class="py-5">
        <div class="container py-4">
            <div class="card-modern">
                <?php
                require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/ReceitaController.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/PacienteController.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/MedicoController.php';
                $controller = new ReceitaController();
                $pacienteController = new PacienteController();
                $medicoController = new MedicoController();
                $pacientes = $pacienteController->getAll();
                $medicos = $medicoController->getAll();

                $action = isset($_GET['action']) ? $_GET['action'] : 'list';
                $receita = null;

                if ($action == 'edit' && isset($_GET['id'])) {
                    $receita = $controller->getById($_GET['id']);
                    if (!$receita) {
                        echo "<p>Receita não encontrada.</p>";
                        exit;
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($_POST['delete_id'])) {
                        $controller->delete($_POST['delete_id']);
                        header('Location: receita.php');
                        exit;
                    } elseif ($action == 'create') {
                        $data = [
                            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
                            'fk_medico_cod' => $_POST['fk_medico_cod'],
                            'data_receita' => $_POST['data_receita'],
                            'tipo' => $_POST['tipo']
                        ];
                        $controller->create($data);
                        header('Location: receita.php');
                        exit;
                    } elseif ($action == 'edit' && isset($_POST['cod'])) {
                        $data = [
                            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
                            'fk_medico_cod' => $_POST['fk_medico_cod'],
                            'data_receita' => $_POST['data_receita'],
                            'tipo' => $_POST['tipo']
                        ];
                        $controller->update($_POST['cod'], $data);
                        header('Location: receita.php');
                        exit;
                    }
                }

                if ($action == 'list') {
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-azul fw-bold">Lista de Receitas</h2>
                        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente'): ?>
                        <a href="receita.php?action=create" class="btn btn-verde">Adicionar Nova Receita</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Data Receita</th>
                                    <th>Tipo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $receitas = $controller->getAll();
                                foreach ($receitas as $rec) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($rec['cod']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rec['paciente_nome']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rec['medico_nome']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rec['data_receita']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rec['tipo']) . "</td>";
                                    echo "<td>";
                                    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente') {
                                        echo "<a href='receita.php?action=edit&id=" . $rec['cod'] . "' class='btn btn-sm btn-azul me-2'>Editar</a>";
                                        echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja deletar?\")'>";
                                        echo "<input type='hidden' name='delete_id' value='" . $rec['cod'] . "'>";
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
                    $title = $action == 'create' ? 'Adicionar Nova Receita' : 'Editar Receita';
                    ?>
                    <h2 class="text-azul fw-bold mb-4"><?php echo $title; ?></h2>
                    
                    <form method="POST" action="">
                        <?php if ($action == 'edit') { ?>
                            <input type="hidden" name="cod" value="<?php echo htmlspecialchars($receita['cod']); ?>">
                        <?php } ?>
                        <div class="row gy-3 mb-3">
                            <div class="col-md-6">
                                <label for="fk_paciente_cod" class="form-label">Paciente</label>
                                <select class="form-control" id="fk_paciente_cod" name="fk_paciente_cod" required>
                                    <?php foreach ($pacientes as $pac) { 
                                        $selected = ($action == 'edit' && $pac['cod'] == $receita['fk_paciente_cod']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo htmlspecialchars($pac['cod']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($pac['nome']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fk_medico_cod" class="form-label">Médico</label>
                                <select class="form-control" id="fk_medico_cod" name="fk_medico_cod" required>
                                    <?php foreach ($medicos as $med) { 
                                        $selected = ($action == 'edit' && $med['cod'] == $receita['fk_medico_cod']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo htmlspecialchars($med['cod']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($med['nome']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row gy-3 mb-3">
                            <div class="col-md-6">
                                <label for="data_receita" class="form-label">Data Receita</label>
                                <input type="date" class="form-control" id="data_receita" name="data_receita" value="<?php echo $action == 'edit' ? htmlspecialchars($receita['data_receita']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo</label>
                                <input type="text" class="form-control" id="tipo" name="tipo" value="<?php echo $action == 'edit' ? htmlspecialchars($receita['tipo']) : ''; ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-verde">Salvar</button>
                        <a href="receita.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                    <?php
                }
                ?>
            </div>
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



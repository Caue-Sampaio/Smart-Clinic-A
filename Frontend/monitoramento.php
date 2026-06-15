<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Monitoramentos</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>

    <?php renderNavbar(); ?>

    <section class="py-5">
        <div class="container py-4">
            <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/MonitoramentoController.php';
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/PacienteController.php';
            $controller = new MonitoramentoController();
            $pacienteController = new PacienteController();
            $pacientes = $pacienteController->getAll();
            $isPaciente = isset($_SESSION['role']) && $_SESSION['role'] === 'paciente';
            $pacienteLogadoCod = $_SESSION['user_id'] ?? null;
            $pacienteLogadoCpf = null;
            if ($isPaciente && !empty($pacienteLogadoCod)) {
                $pacienteAtual = $pacienteController->getById($pacienteLogadoCod);
                $pacienteLogadoCpf = $pacienteAtual['cpf'] ?? null;
            }

            $action = isset($_GET['action']) ? $_GET['action'] : 'list';
            $monitoramento = null;

            if ($action == 'edit' && isset($_GET['id'])) {
                $monitoramento = $controller->getById($_GET['id']);
                if (!$monitoramento) {
                    echo "<p>Monitoramento não encontrado.</p>";
                    exit;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['delete_id']) && !$isPaciente) {
                    $controller->delete($_POST['delete_id']);
                    header('Location: monitoramento.php');
                    exit;
                } elseif ($action == 'create' && !$isPaciente) {
                    $data = [
                        'fk_paciente_cpf' => $_POST['fk_paciente_cpf'],
                        'altura' => $_POST['altura'] ?: null,
                        'peso' => $_POST['peso'] ?: null,
                        'imc' => $_POST['imc'] ?: null
                    ];
                    $controller->create($data);
                    header('Location: monitoramento.php');
                    exit;
                } elseif ($action == 'edit' && isset($_POST['cod'])) {
                    $data = [
                        'fk_paciente_cpf' => $_POST['fk_paciente_cpf'],
                        'altura' => $_POST['altura'] ?: null,
                        'peso' => $_POST['peso'] ?: null,
                        'imc' => $_POST['imc'] ?: null
                    ];
                    $controller->update($_POST['cod'], $data);
                    header('Location: monitoramento.php');
                    exit;
                }
            }

            if ($isPaciente && ($action == 'create' || $action == 'edit')) {
                header('Location: monitoramento.php');
                exit;
            }

            if ($action == 'list') {
                ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-azul fw-bold">Lista de Monitoramentos</h2>
                    <?php if (!$isPaciente): ?>
                    <a href="monitoramento.php?action=create" class="btn btn-verde">Adicionar Novo Monitoramento</a>
                    <?php endif; ?>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Paciente</th>
                                <th>Altura</th>
                                <th>Peso</th>
                                <th>IMC</th>
                             
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $monitoramentos = $controller->getAll();
                            if ($isPaciente && !empty($pacienteLogadoCpf)) {
                                $monitoramentos = array_filter($monitoramentos, function($mon) use ($pacienteLogadoCpf) {
                                    return $mon['fk_paciente_cpf'] === $pacienteLogadoCpf;
                                });
                            }
                            foreach ($monitoramentos as $mon) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($mon['cod']) . "</td>";
                                echo "<td>" . htmlspecialchars($mon['paciente_nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($mon['altura']) . "</td>";
                                echo "<td>" . htmlspecialchars($mon['peso']) . "</td>";
                                echo "<td>" . htmlspecialchars($mon['imc']) . "</td>";
                                echo "<td>";
                                if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente') {
                                    echo "<a href='monitoramento.php?action=edit&id=" . $mon['cod'] . "' class='btn btn-sm btn-primary me-2'>Editar</a>";
                                    echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja deletar?\")'>";
                                    echo "<input type='hidden' name='delete_id' value='" . $mon['cod'] . "'>";
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
                $title = $action == 'create' ? 'Adicionar Novo Monitoramento' : 'Editar Monitoramento';
                ?>
                <div class="card-modern">
                    <h2 class="title mb-3"><?php echo $title; ?></h2>
                    <p class="text-muted mb-4"><?php echo $action == 'create' ? 'Registre as medidas do paciente.' : 'Atualize as medidas.'; ?></p>

                    <form method="POST" action="">
                        <?php if ($action == 'edit') { ?>
                            <input type="hidden" name="cod" value="<?php echo htmlspecialchars($monitoramento['cod']); ?>">
                        <?php } ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fk_paciente_cpf" class="form-label">Paciente</label>
                                <select class="form-control form-control-lg" id="fk_paciente_cpf" name="fk_paciente_cpf" required <?php echo $isPaciente ? 'disabled' : ''; ?>>
                                    <?php foreach ($pacientes as $pac) {
                                        $selected = ($action == 'edit' && $pac['cpf'] == ($monitoramento['fk_paciente_cpf'] ?? '')) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo htmlspecialchars($pac['cpf']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($pac['nome']); ?></option>
                                    <?php } ?>
                                </select>
                                <?php if ($isPaciente): ?>
                                    <input type="hidden" name="fk_paciente_cpf" value="<?php echo htmlspecialchars($monitoramento['fk_paciente_cpf'] ?? $pacienteLogadoCpf); ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label for="altura" class="form-label">Altura (m)</label>
                                <input type="number" step="0.01" class="form-control form-control-lg" id="altura" name="altura" value="<?php echo $action == 'edit' ? htmlspecialchars($monitoramento['altura']) : ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="number" step="0.01" class="form-control form-control-lg" id="peso" name="peso" value="<?php echo $action == 'edit' ? htmlspecialchars($monitoramento['peso']) : ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="imc" class="form-label">IMC</label>
                                <input type="number" step="0.01" class="form-control form-control-lg" id="imc" name="imc" value="<?php echo $action == 'edit' ? htmlspecialchars($monitoramento['imc']) : ''; ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-verde btn-lg"><i class="bi bi-check-lg"></i> Salvar</button>
                            <a href="monitoramento.php" class="btn btn-secondary btn-lg">Cancelar</a>
                        </div>
                    </form>
                </div>
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



<?php
session_start();
?>
<?php require_once __DIR__ . '/navbar.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Exames</title>
    
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
                    <table class="table table-modern mb-0">
                        <thead>
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


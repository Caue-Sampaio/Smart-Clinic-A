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
    <title>SMART CLINIC - Receitas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
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
                require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/PrescreverController.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/MedicamentoController.php';
                $controller = new ReceitaController();
                $pacienteController = new PacienteController();
                $prescreverController = new PrescreverController();
                $medicamentoController = new MedicamentoController();
                $action = isset($_GET['action']) ? $_GET['action'] : 'list';
                $isPaciente = isset($_SESSION['role']) && $_SESSION['role'] === 'paciente';
                $pacienteLogadoCod = $_SESSION['user_id'] ?? null;
                $medicamentosSelecionados = [];

                if ($isPaciente && ($action == 'create' || $action == 'edit')) {
                    header('Location: receita.php');
                    exit;
                }
                $medicoController = new MedicoController();
                $pacientes = $pacienteController->getAll();
                $medicos = $medicoController->getAll();

                $receita = null;

                if (($action == 'edit' || $action == 'print') && isset($_GET['id'])) {
                    if ($action == 'edit') {
                        $receita = $controller->getById($_GET['id']);
                        if (!$receita) {
                            echo "<p>Receita não encontrada.</p>";
                            exit;
                        }
                        $medicamentosSelecionados = $prescreverController->getByReceita($receita['cod']);
                    } else {
                        $receita = $controller->getDetailedById($_GET['id']);
                        if (!$receita) {
                            echo "<p>Receita não encontrada.</p>";
                            exit;
                        }
                        if ($isPaciente && !empty($pacienteLogadoCod) && $receita['fk_paciente_cod'] != $pacienteLogadoCod) {
                            header('Location: receita.php');
                            exit;
                        }
                        $medicamentosImpressao = $prescreverController->getMedicamentosByReceita($receita['cod']);
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if ($isPaciente) {
                        header('Location: receita.php');
                        exit;
                    }
                    if (isset($_POST['delete_id'])) {
                        $controller->delete($_POST['delete_id']);
                        header('Location: receita.php');
                        exit;
                    } elseif ($action == 'create') {
                        $data = [
                            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
                            'fk_medico_cod' => $_POST['fk_medico_cod'],
                            'data_receita' => $_POST['data_receita'],
                            'descricao' => $_POST['descricao'] ?? null,
                            'tipo' => 'Receita genérica'
                        ];
                        $receitaId = $controller->create($data);
                        $medicamentos = $_POST['medicamentos'] ?? [];
                        if (!empty($medicamentos) && is_array($medicamentos)) {
                            foreach ($medicamentos as $medicamentoCod) {
                                $prescreverController->create([
                                    'fk_receita_cod' => $receitaId,
                                    'fk_medicamento_cod' => (int)$medicamentoCod,
                                    'descricao' => null,
                                    'modo_uso' => null
                                ]);
                            }
                        }
                        header('Location: receita.php');
                        exit;
                    } elseif ($action == 'edit' && isset($_POST['cod'])) {
                        $data = [
                            'fk_paciente_cod' => $_POST['fk_paciente_cod'],
                            'fk_medico_cod' => $_POST['fk_medico_cod'],
                            'data_receita' => $_POST['data_receita'],
                            'descricao' => $_POST['descricao'] ?? null,
                            'tipo' => 'Receita genérica'
                        ];
                        $controller->update($_POST['cod'], $data);
                        $medicamentos = $_POST['medicamentos'] ?? [];
                        $prescreverController->deleteByReceita($_POST['cod']);
                        if (!empty($medicamentos) && is_array($medicamentos)) {
                            foreach ($medicamentos as $medicamentoCod) {
                                $prescreverController->create([
                                    'fk_receita_cod' => $_POST['cod'],
                                    'fk_medicamento_cod' => (int)$medicamentoCod,
                                    'descricao' => null,
                                    'modo_uso' => null
                                ]);
                            }
                        }
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
                                    <th>Descrição</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($isPaciente && !empty($pacienteLogadoCod)) {
                                    $receitas = $controller->getByPaciente($pacienteLogadoCod);
                                } else {
                                    $receitas = $controller->getAll();
                                }
                                foreach ($receitas as $rec) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($rec['cod']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rec['paciente_nome']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rec['medico_nome']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rec['data_receita']) . "</td>";
                                    echo "<td>" . nl2br(htmlspecialchars($rec['descricao'] ?? '')) . "</td>";
                                    echo "<td>";
                                    echo "<a href='receita_pdf.php?id=" . $rec['cod'] . "' class='btn btn-sm btn-secondary-custom me-2'>Baixar PDF</a>";
                                    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente') {
                                        echo "<a href='receita.php?action=edit&id=" . $rec['cod'] . "' class='btn btn-sm btn-azul me-2'>Editar</a>";
                                        echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja deletar?\")'>";
                                        echo "<input type='hidden' name='delete_id' value='" . $rec['cod'] . "'>";
                                        echo "<button type='submit' class='btn btn-sm btn-danger-custom'>Deletar</button>";
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
                } elseif ($action == 'print') {
                    ?>
                    <div class="card-modern print-area">
                        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                            <div>
                                <h2 class="title mb-1">Receita #<?php echo htmlspecialchars($receita['cod']); ?></h2>
                                <p class="text-muted mb-0">Paciente: <?php echo htmlspecialchars($receita['paciente_nome'] ?? ''); ?> | Médico: <?php echo htmlspecialchars($receita['medico_nome'] ?? ''); ?></p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-verde" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
                                <button type="button" class="btn btn-secondary-custom" onclick="generatePdf()"><i class="bi bi-file-earmark-pdf"></i> Baixar PDF</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>Data da Receita:</strong> <?php echo htmlspecialchars($receita['data_receita']); ?><br>
                            <strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($receita['descricao'] ?? '')); ?>
                        </div>
                        <div class="mb-4">
                            <h5 class="mb-3">Medicamentos</h5>
                            <?php if (!empty($medicamentosImpressao)) { ?>
                                <ul class="list-group">
                                    <?php foreach ($medicamentosImpressao as $med) { ?>
                                        <li class="list-group-item">
                                            <strong><?php echo htmlspecialchars($med['nome']); ?></strong>
                                            <?php if (!empty($med['dosagem'])) { ?> - <?php echo htmlspecialchars($med['dosagem']); ?><?php } ?>
                                            <?php if (!empty($med['forma'])) { ?> (<?php echo htmlspecialchars($med['forma']); ?>)<?php } ?>
                                            <?php if (!empty($med['descricao'])) { ?><div><?php echo htmlspecialchars($med['descricao']); ?></div><?php } ?>
                                            <?php if (!empty($med['prescricao_descricao'])) { ?><div><small><strong>Descrição:</strong> <?php echo htmlspecialchars($med['prescricao_descricao']); ?></small></div><?php } ?>
                                            <?php if (!empty($med['modo_uso'])) { ?><div><small><strong>Modo de uso:</strong> <?php echo htmlspecialchars($med['modo_uso']); ?></small></div><?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <div class="text-muted">Nenhum medicamento vinculado a esta receita.</div>
                            <?php } ?>
                        </div>
                        <a href="receita.php" class="btn btn-secondary">Voltar para Receitas</a>
                    </div>
                <?php } elseif ($action == 'create' || $action == 'edit') {
                    $title = $action == 'create' ? 'Adicionar Nova Receita' : 'Editar Receita';
                    ?>
                    <div class="card-modern">
                        <h2 class="title mb-3"><?php echo $title; ?></h2>
                        <p class="text-muted mb-4"><?php echo $action == 'create' ? 'Preencha os dados para criar uma receita.' : 'Atualize os dados da receita.'; ?></p>

                        <form method="POST" action="">
                            <?php if ($action == 'edit') { ?>
                                <input type="hidden" name="cod" value="<?php echo htmlspecialchars($receita['cod']); ?>">
                            <?php } ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fk_paciente_cod" class="form-label">Paciente</label>
                                    <select class="form-control form-control-lg" id="fk_paciente_cod" name="fk_paciente_cod" required <?php echo $isPaciente ? 'disabled' : ''; ?> >
                                        <?php foreach ($pacientes as $pac) { 
                                            $selected = ($action == 'edit' && $pac['cod'] == ($receita['fk_paciente_cod'] ?? '')) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo htmlspecialchars($pac['cod']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($pac['nome']); ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($isPaciente): ?>
                                        <input type="hidden" name="fk_paciente_cod" value="<?php echo htmlspecialchars($receita['fk_paciente_cod'] ?? $pacienteLogadoCod); ?>">
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="fk_medico_cod" class="form-label">Médico</label>
                                    <select class="form-control form-control-lg" id="fk_medico_cod" name="fk_medico_cod" required>
                                        <?php foreach ($medicos as $med) { 
                                            $selected = ($action == 'edit' && $med['cod'] == ($receita['fk_medico_cod'] ?? '')) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo htmlspecialchars($med['cod']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($med['nome']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="data_receita" class="form-label">Data Receita</label>
                                    <input type="date" class="form-control form-control-lg" id="data_receita" name="data_receita" value="<?php echo $action == 'edit' ? htmlspecialchars($receita['data_receita']) : ''; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control form-control-lg" id="descricao" name="descricao" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($receita['descricao'] ?? '') : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="mb-4 mt-4">
                                <label class="form-label">Medicamentos</label>
                                <div class="row gx-2 gy-2">
                                    <?php $medicamentos = $medicamentoController->getAll();
                                    if (!empty($medicamentos)) {
                                        foreach ($medicamentos as $med) {
                                            $checked = in_array($med['cod'], $medicamentosSelecionados) ? 'checked' : '';
                                            ?>
                                            <div class="col-md-6">
                                                <label class="form-check form-check-inline" style="width: 100%;">
                                                    <input class="form-check-input" type="checkbox" name="medicamentos[]" value="<?= htmlspecialchars($med['cod']) ?>" <?= $checked ?> />
                                                    <span class="form-check-label"><?= htmlspecialchars($med['nome']) ?><?php echo !empty($med['dosagem']) ? ' - ' . htmlspecialchars($med['dosagem']) : ''; ?></span>
                                                </label>
                                            </div>
                                        <?php }
                                    } else {
                                        echo '<div class="col-12 text-muted">Nenhum medicamento cadastrado.</div>';
                                    }
                                    ?>
                                </div>
                                <small class="text-muted">Selecione um ou mais medicamentos para esta receita.</small>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-verde btn-lg"><i class="bi bi-check-lg"></i> Salvar</button>
                                <a href="receita.php" class="btn btn-secondary-custom btn-lg">Cancelar</a>
                            </div>
                        </form>
                    </div>
                <?php }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        async function generatePdf() {
            const { jsPDF } = window.jspdf;
            const element = document.querySelector('.print-area');
            if (!element) return;
            const canvas = await html2canvas(element, { scale: 2 });
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const imgWidth = pageWidth - 20;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let position = 10;
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            if (imgHeight > pageHeight - 20) {
                let heightLeft = imgHeight - pageHeight + 20;
                while (heightLeft > 0) {
                    pdf.addPage();
                    position = 10 - (imgHeight - heightLeft);
                    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight - 20;
                }
            }
            pdf.save('receita_<?php echo htmlspecialchars($receita['cod']); ?>.pdf');
        }
    </script>
</body>
</html>



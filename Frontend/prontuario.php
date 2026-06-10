<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function normalizaFotoPath(string $path = null): string {
    $defaultPath = '/SMART-CLINIC-A/img/default-avatar.svg';

    if (empty($path)) {
        return $defaultPath;
    }

    if (preg_match('#^(https?://|/)#i', $path)) {
        return $path;
    }

    return '/SMART-CLINIC-A/' . ltrim($path, '/\\');
}

require_once __DIR__ . '/navbar.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/ProntuarioController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/PacienteController.php';

$controller = new ProntuarioController();
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
$prontuario = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $prontuario = $controller->getById($_GET['id']);
    if (!$prontuario) {
        echo "<p>Prontuário não encontrado.</p>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id']) && !$isPaciente) {
        $controller->delete($_POST['delete_id']);
        header('Location: prontuario.php');
        exit;
    } elseif ($action == 'create' && !$isPaciente) {
        $foto_to_store = null;
        if (isset($_FILES['foto']) && isset($_FILES['foto']['tmp_name']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/pegaimagem.php';
            $res = pegaImagem($_FILES['foto']);
            if ($res['success']) {
                $foto_to_store = $res['path'];
            } else {
                $foto_to_store = $_POST['existing_foto'] ?? null;
            }
        } else {
            $foto_to_store = $_POST['existing_foto'] ?? ($_POST['foto'] ?? null);
        }

        if (empty($foto_to_store)) {
            $foto_to_store = '/SMART-CLINIC-A/img/default-avatar.svg';
        }

        $data = [
            'fk_paciente_cpf' => $_POST['fk_paciente_cpf'] ?? '',
            'foto' => $foto_to_store,
            'tipo_sanguineo' => $_POST['tipo_sanguineo'] ?? '',
            'doencas_cronicas' => $_POST['doencas_cronicas'] ?? '',
            'doencas_geneticas' => $_POST['doencas_geneticas'] ?? '',
            'doencas_autoimunes' => $_POST['doencas_autoimunes'] ?? '',
            'outros' => $_POST['outros'] ?? ''
        ];
        $controller->create($data);
        header('Location: prontuario.php');
        exit;
    } elseif ($action == 'edit' && isset($_POST['cod'])) {
        $foto_to_store = null;
        if (isset($_FILES['foto']) && isset($_FILES['foto']['tmp_name']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/pegaimagem.php';
            $res = pegaImagem($_FILES['foto']);
            if ($res['success']) {
                $foto_to_store = $res['path'];
            } else {
                $foto_to_store = $_POST['existing_foto'] ?? null;
            }
        } else {
            $foto_to_store = $_POST['existing_foto'] ?? ($_POST['foto'] ?? null);
        }

        if (empty($foto_to_store)) {
            $foto_to_store = '/SMART-CLINIC-A/img/default-avatar.svg';
        }

        $data = [
            'fk_paciente_cpf' => $_POST['fk_paciente_cpf'] ?? '',
            'foto' => $foto_to_store,
            'tipo_sanguineo' => $_POST['tipo_sanguineo'] ?? '',
            'doencas_cronicas' => $_POST['doencas_cronicas'] ?? '',
            'doencas_geneticas' => $_POST['doencas_geneticas'] ?? '',
            'doencas_autoimunes' => $_POST['doencas_autoimunes'] ?? '',
            'outros' => $_POST['outros'] ?? ''
        ];
        $controller->update($_POST['cod'], $data);
        header('Location: prontuario.php');
        exit;
    }
}

if ($isPaciente && ($action == 'create' || $action == 'edit')) {
    header('Location: prontuario.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Prontuários</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --azul: #2563eb;
            --azul-escuro: #1e40af;
            --verde: #22c55e;
            --fundo: #f1f5f9;
        }

        body {
            background: var(--fundo);
            padding-top: 80px;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, var(--azul), var(--azul-escuro));
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .card-modern {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .title {
            font-weight: 600;
            color: #0f172a;
        }

        .btn-verde {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 10px 18px;
            transition: 0.2s;
        }
        .btn-verde:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34,197,94,0.4);
        }

        .form-control:focus {
            border-color: var(--azul);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .table-modern {
            table-layout: fixed;
            width: 100%;
        }
        .table-modern thead {
            background: #f1f5f9;
        }
        .table-modern th,
        .table-modern td {
            color: #475569;
            white-space: nowrap;
        }
        .table-modern td,
        .table-modern th {
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .table-modern td.actions-col,
        .table-modern th.actions-col {
            width: 130px;
            max-width: 130px;
            white-space: nowrap;
            overflow: visible;
            text-overflow: clip;
        }
        .table-modern tr:hover {
            background: #eef2ff;
        }
        .table-modern th.foto-col,
        .table-modern td.foto-col {
            width: 70px;
            max-width: 70px;
            text-align: center;
        }
        .table-modern td.foto-col img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }

        footer {
            background: transparent;
            color: #64748b;
        }
    </style>
</head>
<body>

    <?php ini_set('display_errors', 1); error_reporting(E_ALL); ?>

    <?php renderNavbar(); ?>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <?php
            if ($action == 'list') {
                ?>
                <div class="d-flex justify-content-between align-items-start mb-5">
                    <div class="d-flex align-items-start gap-3">
                        <div style="background: #dbeafe; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-file-earmark-medical" style="font-size: 30px; color: var(--azul);"></i>
                        </div>
                        <div>
                            <h2 class="title mb-1">Lista de Prontuários</h2>
                            <p class="text-muted mb-0"><?= $isPaciente ? 'Aqui estão seus prontuários' : 'Gerencie os prontuários cadastrados no sistema' ?></p>
                        </div>
                    </div>

                    <?php if (!$isPaciente): ?>
                    <a href="prontuario.php?action=create" class="btn btn-verde d-flex align-items-center gap-2" style="margin-top: 5px;">
                        <i class="bi bi-plus-lg" style="font-size: 18px;"></i> Adicionar Novo Prontuário
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="card-modern">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead style="background: #f1f5f9;">
                            <tr>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Código</th>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Paciente</th>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Foto</th>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Tipo Sanguíneo</th>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Doenças Crônicas</th>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Doenças Genéticas</th>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Doenças Autoimunes</th>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Outros</th>
                                <?php if (!$isPaciente): ?>
                                <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $prontuarios = $controller->getAll();
                            if ($isPaciente && !empty($pacienteLogadoCpf)) {
                                $prontuarios = array_filter($prontuarios, function($pron) use ($pacienteLogadoCpf) {
                                    return $pron['fk_paciente_cpf'] === $pacienteLogadoCpf;
                                });
                            }
                            foreach ($prontuarios as $pron) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($pron['cod']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['paciente_nome']) . "</td>";
                                $fotoPath = normalizaFotoPath($pron['foto']);
                                if (!empty($fotoPath)) {
                                    echo "<td class='foto-col'><img src='" . htmlspecialchars($fotoPath) . "' alt='Foto'></td>";
                                } else {
                                    echo "<td class='foto-col text-muted'>Sem foto</td>";
                                }
                                echo "<td>" . htmlspecialchars($pron['tipo_sanguineo']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['doencas_cronicas']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['doencas_geneticas']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['doencas_autoimunes']) . "</td>";
                                echo "<td>" . htmlspecialchars($pron['outros']) . "</td>";
                                if (!isset($_SESSION['role']) || $_SESSION['role'] != 'paciente') {
                                    echo "<td class='actions-col'>";
                                    echo "<a href='prontuario.php?action=edit&id=" . $pron['cod'] . "' class='btn btn-sm btn-primary me-2'>Editar</a>";
                                    echo "<form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja deletar?\")'>";
                                    echo "<input type='hidden' name='delete_id' value='" . $pron['cod'] . "'>";
                                    echo "<button type='submit' class='btn btn-sm btn-danger'>Deletar</button>";
                                    echo "</form>";
                                    echo "</td>";
                                }
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                </div>
                <?php
            } elseif ($action == 'create' || $action == 'edit') {
                $title = $action == 'create' ? 'Adicionar Novo Prontuário' : 'Editar Prontuário';
                ?>
                <div class="card-modern">
                    <h2 class="title mb-3"><?php echo $title; ?></h2>
                    <p class="text-muted mb-4"><?php echo $action == 'create' ? 'Preencha os dados para criar um prontuário.' : 'Atualize os campos e salve as alterações.'; ?></p>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <?php if ($action == 'edit') { ?>
                            <input type="hidden" name="cod" value="<?php echo htmlspecialchars($prontuario['cod']); ?>">
                        <?php } ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fk_paciente_cpf" class="form-label">Paciente</label>
                                <select class="form-control form-control-lg" id="fk_paciente_cpf" name="fk_paciente_cpf" required <?php echo $isPaciente ? 'disabled' : ''; ?>>
                                    <?php foreach ($pacientes as $pac) {
                                        $selected = ($action == 'edit' && $pac['cpf'] == ($prontuario['fk_paciente_cpf'] ?? '')) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo htmlspecialchars($pac['cpf']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($pac['nome']); ?></option>
                                    <?php } ?>
                                </select>
                                <?php if ($isPaciente): ?>
                                    <input type="hidden" name="fk_paciente_cpf" value="<?php echo htmlspecialchars($prontuario['fk_paciente_cpf'] ?? $pacienteLogadoCpf); ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label for="foto" class="form-label">Foto (imagem)</label>
                                <input type="file" accept="image/*" class="form-control form-control-lg" id="foto" name="foto">
                                <?php if ($action == 'edit'): ?>
                                    <input type="hidden" name="existing_foto" value="<?php echo htmlspecialchars(normalizaFotoPath($prontuario['foto'] ?? '')); ?>">
                                <?php endif; ?>
                                <div class="mt-3" id="fotoPreviewWrapper">
                                    <?php if ($action == 'edit' && !empty($prontuario['foto'])): ?>
                                        <img id="fotoPreview" src="<?= htmlspecialchars(normalizaFotoPath($prontuario['foto'])) ?>" alt="Pré-visualização da foto" style="max-width:180px; max-height:180px; border-radius:8px;">
                                    <?php else: ?>
                                        <div id="fotoPreviewPlaceholder" class="text-muted" style="font-size:.95rem;">Escolha uma imagem para pré-visualizar aqui.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                                <select class="form-control form-control-lg" id="tipo_sanguineo" name="tipo_sanguineo">
                                    <?php
                                    $blood_types = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                                    $selectedType = $action == 'edit' ? ($prontuario['tipo_sanguineo'] ?? '') : '';
                                    foreach ($blood_types as $bt) {
                                        $sel = $bt === $selectedType ? 'selected' : '';
                                        echo "<option value=\"{$bt}\" {$sel}>{$bt}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="outros" class="form-label">Outros (resumo)</label>
                                <textarea class="form-control form-control-lg" id="outros" name="outros" rows="2"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['outros'] ?? '') : ''; ?></textarea>
                            </div>

                            <div class="col-12">
                                <label for="doencas_cronicas" class="form-label">Doenças Crônicas</label>
                                <textarea class="form-control" id="doencas_cronicas" name="doencas_cronicas" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['doencas_cronicas']) : ''; ?></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="doencas_geneticas" class="form-label">Doenças Genéticas</label>
                                <textarea class="form-control" id="doencas_geneticas" name="doencas_geneticas" rows="2"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['doencas_geneticas']) : ''; ?></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="doencas_autoimunes" class="form-label">Doenças Autoimunes</label>
                                <textarea class="form-control" id="doencas_autoimunes" name="doencas_autoimunes" rows="2"><?php echo $action == 'edit' ? htmlspecialchars($prontuario['doencas_autoimunes']) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-verde btn-lg">
                                <i class="bi bi-check-lg"></i> Salvar
                            </button>
                            <a href="prontuario.php" class="btn btn-secondary btn-lg">Cancelar</a>
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
    <script>
        const fotoInput = document.getElementById('foto');
        const previewWrapper = document.getElementById('fotoPreviewWrapper');
        const previewPlaceholder = document.getElementById('fotoPreviewPlaceholder');

        if (fotoInput) {
            fotoInput.addEventListener('change', function () {
                const file = this.files[0];
                const existingImg = document.getElementById('fotoPreview');

                if (!file) {
                    if (existingImg) {
                        existingImg.remove();
                    }
                    if (previewPlaceholder) {
                        previewPlaceholder.style.display = 'block';
                    }
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    let img = document.getElementById('fotoPreview');
                    if (!img) {
                        if (previewPlaceholder) {
                            previewPlaceholder.style.display = 'none';
                        }
                        img = document.createElement('img');
                        img.id = 'fotoPreview';
                        img.alt = 'Pré-visualização da foto';
                        img.style.maxWidth = '180px';
                        img.style.maxHeight = '180px';
                        img.style.borderRadius = '8px';
                        previewWrapper.appendChild(img);
                    }
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
</body>
</html>



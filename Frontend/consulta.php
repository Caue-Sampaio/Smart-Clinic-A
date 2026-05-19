<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
require_once __DIR__ . '/navbar.php'; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Consultas</title>
    
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

        .table-modern thead {
            background: #f1f5f9;
        }
        .table-modern th {
            color: #475569;
        }
        .table-modern tr:hover {
            background: #eef2ff;
        }

        footer {
            background: transparent;
            color: #64748b;
        }
    </style>
</head>

<body>

<?php renderNavbar(); ?>

<section class="py-5">
<div class="container-lg" style="max-width: 1200px;">

<?php
require_once '../Backend/controller/ConsultaController.php';
require_once '../Backend/controller/AgendamentoController.php';
$controller = new ConsultaController();
$agendamentoController = new AgendamentoController();

$isPaciente = isset($_SESSION['role']) && $_SESSION['role'] === 'paciente';
$pacienteLogadoCod = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id']) && !$isPaciente) {
    $controller->delete($_POST['delete_id']);
    header('Location: consulta.php');
    exit;
}
?>

<div class="d-flex justify-content-between align-items-start mb-5">
    <div class="d-flex align-items-start gap-3">
        <div style="background: #dbeafe; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-calendar-check" style="font-size: 30px; color: var(--azul);"></i>
        </div>
        <div>
            <h2 class="title mb-1">Lista de Consultas</h2>
            <p class="text-muted mb-0">Gerencie as consultas cadastradas no sistema</p>
        </div>
    </div>
</div>

<div class="card-modern">
<div class="table-responsive">
<table class="table table-modern mb-0">
<thead style="background: #f1f5f9;">
<tr>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">CÓDIGO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">AGENDAMENTO</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">DATA CONSULTA</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">SÍNTESE</th>
    <th style="padding: 15px; font-weight: 600; color: #475569; font-size: 13px; text-transform: uppercase;">AÇÕES</th>
</tr>
</thead>

<tbody>
<?php
if ($isPaciente && !empty($pacienteLogadoCod)) {
    $consultas = $agendamentoController->getByPaciente($pacienteLogadoCod);
} else {
    $consultas = $controller->getAll();
}
foreach ($consultas as $consulta) {
    echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
    echo "<td style='padding: 15px; color: #0f172a; font-weight: 500;'>{$consulta['cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$consulta['fk_agendamento_cod']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$consulta['data_consulta']}</td>";
    echo "<td style='padding: 15px; color: #0f172a;'>{$consulta['sintese']}</td>";
    echo "<td style='padding: 15px;'>";
    if (!$isPaciente) {
        echo "<form method='POST' style='display:inline;'>
            <input type='hidden' name='delete_id' value='{$consulta['cod']}'>
            <button class='btn btn-sm' style='background: #ef4444; color: white; border: none; border-radius: 6px; padding: 6px 12px;'>
                <i class='bi bi-trash' style='font-size: 14px;'></i> Deletar
            </button>
        </form>";
    }
    echo "</td>";
    echo "</tr>";
}
?>
</tbody>
</table>
</div>
</div>

</div>
</section>

<footer style="background: transparent; color: #64748b; padding: 20px 0;">
    <div class="container-lg text-center">
        <p class="mb-0">© 2026 SMART CLINIC - Todos os direitos reservados</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php ob_end_flush(); ?>
</body>
</html>


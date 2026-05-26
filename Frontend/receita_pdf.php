<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/lib/fpdf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/ReceitaController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SmartClinic-A/Backend/controller/PrescreverController.php';

$controller = new ReceitaController();
$prescreverController = new PrescreverController();
$isPaciente = isset($_SESSION['role']) && $_SESSION['role'] === 'paciente';
$pacienteLogadoCod = $_SESSION['user_id'] ?? null;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: receita.php');
    exit;
}

$receita = $controller->getDetailedById($id);
if (!$receita) {
    header('Location: receita.php');
    exit;
}

if ($isPaciente && !empty($pacienteLogadoCod) && $receita['fk_paciente_cod'] != $pacienteLogadoCod) {
    header('Location: receita.php');
    exit;
}

$medicamentos = $prescreverController->getMedicamentosByReceita($id);

function pdfText($text) {
    return utf8_decode($text ?? '');
}

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();
$pdf->SetTitle(pdfText('Receita #' . $receita['cod']));
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, pdfText('SMART CLINIC - Receita'), 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 8, pdfText('Receita #:'), 0, 0);
$pdf->Cell(0, 8, pdfText($receita['cod']), 0, 1);
$pdf->Cell(40, 8, pdfText('Paciente:'), 0, 0);
$pdf->Cell(0, 8, pdfText($receita['paciente_nome'] ?? ''), 0, 1);
$pdf->Cell(40, 8, pdfText('Médico:'), 0, 0);
$pdf->Cell(0, 8, pdfText($receita['medico_nome'] ?? ''), 0, 1);
$pdf->Cell(40, 8, pdfText('Data da Receita:'), 0, 0);
$pdf->Cell(0, 8, pdfText(date('d/m/Y', strtotime($receita['data_receita']))), 0, 1);
$pdf->Cell(40, 8, pdfText('Descrição:'), 0, 0);
$pdf->Cell(0, 8, pdfText($receita['descricao'] ?? ''), 0, 1);
$pdf->Ln(6);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Medicamentos:', 0, 1);
$pdf->SetFont('Arial', '', 11);

if (!empty($medicamentos)) {
    foreach ($medicamentos as $index => $med) {
        $medText = ($index + 1) . '. ' . ($med['nome'] ?? '');
        if (!empty($med['dosagem'])) {
            $medText .= ' - ' . $med['dosagem'];
        }
        if (!empty($med['forma'])) {
            $medText .= ' (' . $med['forma'] . ')';
        }
        $pdf->MultiCell(0, 6, pdfText($medText), 0, 'L');

        if (!empty($med['descricao'])) {
            $pdf->MultiCell(0, 5, pdfText('   Descrição: ' . $med['descricao']), 0, 'L');
        }
        if (!empty($med['prescricao_descricao'])) {
            $pdf->MultiCell(0, 5, pdfText('   Prescrição: ' . $med['prescricao_descricao']), 0, 'L');
        }
        if (!empty($med['modo_uso'])) {
            $pdf->MultiCell(0, 5, pdfText('   Modo de uso: ' . $med['modo_uso']), 0, 'L');
        }
        $pdf->Ln(3);
    }
} else {
    $pdf->MultiCell(0, 6, pdfText('Nenhum medicamento vinculado a esta receita.'), 0, 'L');
}

$pdf->Ln(6);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 6, pdfText('Este documento foi gerado automaticamente pelo sistema SMART CLINIC.'), 0, 1);

$pdf->Output('D', 'receita_' . $receita['cod'] . '.pdf');
exit;

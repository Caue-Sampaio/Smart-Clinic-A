<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/lib/fpdf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/ReceitaController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/Backend/controller/PrescreverController.php';

$controller          = new ReceitaController();
$prescreverController = new PrescreverController();
$isPaciente          = isset($_SESSION['role']) && $_SESSION['role'] === 'paciente';
$pacienteLogadoCod   = $_SESSION['user_id'] ?? null;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) { header('Location: receita.php'); exit; }

$receita = $controller->getDetailedById($id);
if (!$receita) { header('Location: receita.php'); exit; }

if ($isPaciente && !empty($pacienteLogadoCod) && $receita['fk_paciente_cod'] != $pacienteLogadoCod) {
    header('Location: receita.php'); exit;
}

$medicamentos = $prescreverController->getMedicamentosByReceita($id);

// Formata data do banco (Y-m-d) para DD/MM/AAAA
$dataFormatada = '';
if (!empty($receita['data_receita'])) {
    $dt = DateTime::createFromFormat('Y-m-d', $receita['data_receita']);
    $dataFormatada = $dt ? $dt->format('d/m/Y') : $receita['data_receita'];
}

function pdfText($text) {
    return utf8_decode($text ?? '');
}

class ReceitaPDF extends FPDF {

    // Propriedade para guardar a data da receita
    public string $dataReceita = '';

    function Header() {
        // Linha azul topo
        $this->SetFillColor(26, 86, 219);
        $this->Rect(0, 0, 210, 2, 'F');

        $this->SetFillColor(255, 255, 255);
        $this->Ln(6);

        // Nome da clínica
        $this->SetFont('Arial', 'B', 22);
        $this->SetTextColor(26, 86, 219);
        $this->Cell(0, 10, 'SMART CLINIC', 0, 1, 'C');

        // Subtítulo
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, utf8_decode('Sistema de Gestão Clínica'), 0, 1, 'C');

        $this->Ln(2);

        // Contato
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 4, utf8_decode('contato@SMART-CLINIC.com  |  (00) 0000-0000'), 0, 1, 'C');

        $this->Ln(3);

        // Linha divisória
        $this->SetDrawColor(26, 86, 219);
        $this->SetLineWidth(0.8);
        $this->Line(10, $this->GetY(), 200, $this->GetY());

        // Título RECEITA
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(26, 86, 219);
        $this->Cell(0, 8, 'RECEITA', 0, 1, 'C');

        // Linha fina
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(6);

        $this->SetTextColor(0, 0, 0);
    }

    function Footer() {
        $this->SetY(-28);

        // Linha separadora
        $this->SetDrawColor(180, 180, 180);
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(4);

        // Data da receita vinda do banco — não pode ser alterada
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(40, 40, 40);
        $this->Cell(30, 5, 'Data da Receita:', 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(26, 86, 219);
        $this->Cell(0, 5, $this->dataReceita, 0, 1, 'L');
        $this->Ln(2);

        // Linha de assinatura
        $this->SetDrawColor(100, 100, 100);
        $this->SetLineWidth(0.4);
        $centerX = 70;
        $this->Line($centerX, $this->GetY(), $centerX + 70, $this->GetY());
        $this->Ln(2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 4, utf8_decode('Assinatura e Carimbo do Médico'), 0, 1, 'C');
        $this->Ln(2);

        // Rodapé
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 4, utf8_decode('Documento gerado automaticamente pelo SMART CLINIC  —  Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new ReceitaPDF('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(true, 58);

// Passa a data formatada para o rodapé
$pdf->dataReceita = $dataFormatada;

$pdf->AddPage();
$pdf->SetTitle(pdfText('Receita #' . $receita['cod']));

// ── Médico ────────────────────────────────────────────────────
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 6, pdfText($receita['medico_nome'] ?? 'Médico'), 0, 1, 'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, pdfText('CRM: __________'), 0, 1, 'L');
$pdf->Ln(4);

// ── Nome, Nascimento, Endereço ────────────────────────────────
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(40, 40, 40);

$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.3);

// Nome e Nasc.
$pdf->Cell(15, 7, pdfText('Nome:'), 0, 0);
$nameX = $pdf->GetX();
$nameY = $pdf->GetY() + 6;
$pdf->Cell(100, 7, pdfText($receita['paciente_nome'] ?? ''), 0, 0);
$pdf->Line($nameX, $nameY, $nameX + 100, $nameY);

$pdf->Cell(15, 7, pdfText('Nasc.:'), 0, 0);
$nascX = $pdf->GetX();
$pdf->Cell(0, 7, pdfText('____ / ____ / ______'), 0, 1);
$pdf->Line($nascX, $nameY, $nascX + 55, $nameY);

// Endereço
$pdf->Cell(20, 7, pdfText('Endereço:'), 0, 0);
$endX = $pdf->GetX();
$endY = $pdf->GetY() + 6;
$pdf->Cell(0, 7, '', 0, 1);
$pdf->Line($endX, $endY, 200, $endY);

$pdf->Ln(2);

// ── Linha separadora ──────────────────────────────────────────
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetLineWidth(0.3);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(4);

// ── Recomendações / Medicamentos ──────────────────────────────
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 6, pdfText('Recomendações:'), 0, 1);
$pdf->Ln(2);

if (!empty($receita['descricao'])) {
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->MultiCell(0, 5, pdfText($receita['descricao']), 0, 'L');
    $pdf->Ln(3);
}

if (!empty($medicamentos)) {
    foreach ($medicamentos as $index => $med) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(26, 86, 219);
        $pdf->Cell(8, 7, ($index + 1) . '.', 0, 0);

        $pdf->SetTextColor(20, 20, 20);
        $medNome = pdfText($med['nome'] ?? '');
        if (!empty($med['dosagem'])) $medNome .= ' - ' . pdfText($med['dosagem']);
        if (!empty($med['forma']))   $medNome .= ' (' . pdfText($med['forma']) . ')';
        $pdf->MultiCell(0, 7, $medNome, 0, 'L');

        $pdf->SetFont('Arial', 'I', 9);
        $pdf->SetTextColor(80, 80, 80);

        if (!empty($med['modo_uso'])) {
            $pdf->SetX(18);
            $pdf->MultiCell(0, 5, pdfText('Modo de uso: ' . $med['modo_uso']), 0, 'L');
        }
        if (!empty($med['prescricao_descricao'])) {
            $pdf->SetX(18);
            $pdf->MultiCell(0, 5, pdfText($med['prescricao_descricao']), 0, 'L');
        }
        if (!empty($med['descricao'])) {
            $pdf->SetX(18);
            $pdf->MultiCell(0, 5, pdfText($med['descricao']), 0, 'L');
        }

        $pdf->Ln(3);
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->MultiCell(0, 6, pdfText('Nenhum medicamento vinculado a esta receita.'), 0, 'L');
}

// ── Assinatura do médico (corpo do documento) ─────────────────
$pdf->Ln(8);
$assinaturaX = 120;
$assinaturaY = $pdf->GetY();
$pdf->SetDrawColor(80, 80, 80);
$pdf->SetLineWidth(0.4);
$pdf->Line($assinaturaX, $assinaturaY, 200, $assinaturaY);

$pdf->SetXY($assinaturaX, $assinaturaY + 2);
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(80, 5, pdfText('Assinatura e Carimbo'), 0, 1, 'C');

$pdf->SetXY($assinaturaX, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(80, 5, pdfText($receita['medico_nome'] ?? ''), 0, 1, 'C');

// ── Marca d'água (logo centralizada) ─────────────────────────
$logoPath = 'C:\\xampp\\htdocs\\SMART-CLINIC-A\\img\\LogoA_transparente.png';
if (file_exists($logoPath)) {
    $logoW = 130;
    $logoH = 130;
    $logoX = (210 - $logoW) / 2;
    $logoY = 140;
    $pdf->Image($logoPath, $logoX, $logoY, $logoW, $logoH, 'PNG');
}

$pdf->Output('D', 'receita_' . $receita['cod'] . '.pdf');
exit;
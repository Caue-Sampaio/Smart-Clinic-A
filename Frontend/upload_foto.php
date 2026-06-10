<?php
header('Content-Type: application/json');

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/SMART-CLINIC-A/img/medicos/';

// Cria a pasta se não existir
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Nenhum arquivo enviado.']);
    exit;
}

$file    = $_FILES['foto'];
$ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Formato inválido. Use JPG, PNG ou WEBP.']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'Arquivo muito grande. Máximo 5 MB.']);
    exit;
}

// Nome único para evitar conflitos
$filename = 'medico_' . uniqid() . '.' . $ext;
$destPath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $destPath)) {
    // Retorna APENAS o nome do arquivo — a pasta fica no frontend
    echo json_encode([
        'success'  => true,
        'filename' => $filename  // ex: medico_6673abc.png
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Falha ao salvar o arquivo. Verifique as permissões da pasta.']);
}
<?php
// Valida e armazena um arquivo de imagem enviado via form
function pegaImagem($file, $destDir = null) {
    if ($destDir === null) {
        $destDir = __DIR__ . '/../img/uploads';
    }

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Nenhum arquivo enviado ou erro no upload'];
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/avif' => 'avif'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!array_key_exists($mime, $allowed)) {
        return ['success' => false, 'error' => 'Tipo de arquivo não permitido'];
    }

    // Limite: 5 MB
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Arquivo muito grande'];
    }

    if (!is_dir($destDir)) {
        if (!mkdir($destDir, 0755, true)) {
            return ['success' => false, 'error' => 'Falha ao criar diretório de destino'];
        }
    }

    $ext = $allowed[$mime];
    try {
        $basename = bin2hex(random_bytes(6));
    } catch (Exception $e) {
        $basename = uniqid();
    }
    $filename = $basename . '.' . $ext;
    $destPath = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['success' => false, 'error' => 'Falha ao mover arquivo'];
    }

    return ['success' => true, 'filename' => $filename];
}

?>
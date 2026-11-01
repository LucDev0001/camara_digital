<?php
/**
 * Câmara Digital - Troca de Tenant Ativo
 */
require_once __DIR__ . '/../config/app.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token de segurança inválido.');
        header('Location: ' . url('dashboard.php'));
        exit;
    }

    $tenantId = (int)($_POST['tenant_id'] ?? 1);
    if (Tenant::switch($tenantId)) {
        setFlash('success', 'Contexto institucional alternado com sucesso.');
    } else {
        setFlash('error', 'Câmara não encontrada ou inativa.');
    }
}

$referer = $_SERVER['HTTP_REFERER'] ?? url('dashboard.php');
header('Location: ' . $referer);
exit;

<?php
/**
 * Câmara Digital - Ações de Ouvidoria e SIC
 */
require_once __DIR__ . '/../config/app.php';

$action = $_POST['action'] ?? '';
$db = Database::getConnection();

try {
    if ($action === 'submit_public') {
        $tenantId = (int)($_POST['tenant_id'] ?? Tenant::id());
        $requestType = trim($_POST['request_type'] ?? 'Reclamação');
        $citizenName = trim($_POST['citizen_name'] ?? '');
        $citizenEmail = trim($_POST['citizen_email'] ?? '');
        $citizenPhone = trim($_POST['citizen_phone'] ?? '');
        $isAnonymous = !empty($_POST['is_anonymous']) ? 1 : 0;
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($isAnonymous) {
            $citizenName = 'Cidadão Anônimo';
            $citizenEmail = null;
            $citizenPhone = null;
        } elseif (empty($citizenName) || empty($citizenEmail)) {
            throw new Exception("Nome e E-mail são obrigatórios para manifestações identificadas.");
        }

        if (empty($subject) || empty($message)) {
            throw new Exception("Assunto e Mensagem são obrigatórios.");
        }

        // Gera código de protocolo único e seguro
        $year = date('Y');
        $randomSeq = rand(10000, 99999);
        $prefix = str_contains($requestType, 'SIC') ? 'SIC' : 'OUV';
        $protocolCode = "{$prefix}-{$year}-{$randomSeq}";

        // Prazo legal: 20 dias corridos conforme Lei 12.527/11
        $deadline = date('Y-m-d', strtotime('+20 days'));

        $stmt = $db->prepare("
            INSERT INTO citizen_requests (tenant_id, protocol_code, request_type, citizen_name, citizen_email, citizen_phone, is_anonymous, subject, message, status, deadline_date, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Recebido', ?, NOW())
        ");
        $stmt->execute([$tenantId, $protocolCode, $requestType, $citizenName, $citizenEmail, $citizenPhone, $isAnonymous, $subject, $message, $deadline]);

        Audit::log('OUVIDORIA_PUBLICA', 'citizen_requests', (int)$db->lastInsertId(), "Manifestação registrada sob o protocolo: {$protocolCode}");

        setFlash('success', "Sua manifestação foi registrada com sucesso! Guarde seu protocolo para acompanhamento: {$protocolCode}");
        header('Location: ' . url("portal/ouvidoria.php?protocol={$protocolCode}"));
        exit;
    }

    if ($action === 'reply_internal') {
        if (!Auth::check()) {
            throw new Exception("Não autorizado.");
        }
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            throw new Exception("Token de autenticação inválido.");
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $replyText = trim($_POST['reply_text'] ?? '');
        $user = Auth::user();

        if (!$requestId || empty($replyText)) {
            throw new Exception("A resposta não pode estar vazia.");
        }

        $stmt = $db->prepare("
            UPDATE citizen_requests 
            SET status = 'Respondido', reply_text = ?, replied_at = NOW(), replied_by = ? 
            WHERE id = ?
        ");
        $stmt->execute([$replyText, $user['name'] . " (" . $user['role_name'] . ")", $requestId]);

        Audit::log('OUVIDORIA_RESPOSTA', 'citizen_requests', $requestId, "Resposta oficial enviada por {$user['name']}");
        setFlash('success', "Resposta oficial registrada e protocolada com sucesso.");
        header('Location: ' . url('modulos/ouvidoria.php'));
        exit;
    }
} catch (Exception $e) {
    setFlash('error', $e->getMessage());
    $referer = $_SERVER['HTTP_REFERER'] ?? url('portal/ouvidoria.php');
    header('Location: ' . $referer);
    exit;
}

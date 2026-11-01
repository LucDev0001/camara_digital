<?php
/**
 * Câmara Digital - Ações de Protocolo Geral e Assinatura Digital
 * Gera hash SHA-256 para verificação pública de integridade
 */
require_once __DIR__ . '/../config/app.php';

if (!Auth::check()) {
    header('Location: ' . url('login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('modulos/protocolo.php'));
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Token de autenticação inválido.');
    header('Location: ' . url('modulos/protocolo.php'));
    exit;
}

$action = $_POST['action'] ?? '';
$db = Database::getConnection();
$user = Auth::user();
$tenantId = Tenant::id();

try {
    if ($action === 'create_protocol') {
        $subject = trim($_POST['subject'] ?? '');
        $senderName = trim($_POST['sender_name'] ?? '');
        $senderType = trim($_POST['sender_type'] ?? 'Cidadão');
        $destDept = trim($_POST['destination_department'] ?? 'Secretaria Geral');
        $urgency = trim($_POST['urgency'] ?? 'Normal');
        $summary = trim($_POST['summary'] ?? '');

        if (empty($subject) || empty($senderName)) {
            throw new Exception("Assunto e Remetente são obrigatórios.");
        }

        $year = (int)date('Y');
        // Conta protocolos do ano
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM protocols WHERE tenant_id = ? AND year = ?");
        $stmtCount->execute([$tenantId, $year]);
        $seq = (int)$stmtCount->fetchColumn() + 1;
        $protocolNum = "PROT-" . $year . "/" . str_pad((string)$seq, 5, '0', STR_PAD_LEFT);

        // Cria Hash SHA-256 de autenticidade (carimbo temporal e conteúdo)
        $rawStringToSign = $tenantId . '|' . $protocolNum . '|' . $subject . '|' . $senderName . '|' . microtime();
        $hashSha256 = hash('sha256', $rawStringToSign);

        $stmt = $db->prepare("
            INSERT INTO protocols (tenant_id, protocol_number, year, sender_name, sender_type, subject, summary, destination_department, urgency, status, verification_hash, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Aberto', ?, NOW())
        ");
        $stmt->execute([$tenantId, $protocolNum, $year, $senderName, $senderType, $subject, $summary, $destDept, $urgency, $hashSha256]);
        $newId = (int)$db->lastInsertId();

        Audit::log('PROTOCOLO_CRIADO', 'protocols', $newId, "Protocolo {$protocolNum} gerado com hash SHA-256: " . substr($hashSha256, 0, 16) . "...");
        setFlash('success', "Protocolo {$protocolNum} cadastrado com sucesso! Código de validação gerado.");
    }

    if ($action === 'dispatch') {
        $protocolId = (int)($_POST['protocol_id'] ?? 0);
        $toDept = trim($_POST['to_department'] ?? '');
        $dispatchText = trim($_POST['dispatch_text'] ?? '');

        if (!$protocolId || empty($toDept) || empty($dispatchText)) {
            throw new Exception("Setor de destino e texto do despacho são obrigatórios.");
        }

        $stmt = $db->prepare("
            INSERT INTO protocol_dispatches (protocol_id, dispatch_text, user_name, to_department, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$protocolId, $dispatchText, $user['name'], $toDept]);

        $db->prepare("UPDATE protocols SET destination_department = ?, status = 'Despachado' WHERE id = ?")
           ->execute([$toDept, $protocolId]);

        Audit::log('PROTOCOLO_DESPACHADO', 'protocols', $protocolId, "Despachado para o setor: {$toDept}");
        setFlash('success', "Despacho eletrônico registrado e encaminhado ao setor {$toDept}.");
    }
} catch (Exception $e) {
    setFlash('error', $e->getMessage());
}

header('Location: ' . url('modulos/protocolo.php'));
exit;

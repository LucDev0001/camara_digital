<?php
/**
 * Câmara Digital - Endpoint da Inteligência Artificial e RAG
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/AIAssistant.php';

header('Content-Type: application/json; charset=utf-8');

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'query';
$userId = Auth::id();

try {
    if ($action === 'query') {
        $prompt = trim($_POST['prompt'] ?? '');
        $module = trim($_POST['module'] ?? 'Legislativo');

        if (empty($prompt)) {
            echo json_encode(['error' => 'A consulta não pode estar vazia']);
            exit;
        }

        $result = AIAssistant::query($prompt, $module, $userId);
        echo json_encode($result);
        exit;
    }

    if ($action === 'generate_draft') {
        $type = trim($_POST['type'] ?? 'Projeto de Lei Ordinária');
        $theme = trim($_POST['theme'] ?? 'Infraestrutura e Cidadania');
        $targetGroup = trim($_POST['target_group'] ?? 'Munícipes em Geral');
        $justification = trim($_POST['justification'] ?? '');

        $result = AIAssistant::generateDraft($type, $theme, $targetGroup, $justification);
        echo json_encode($result);
        exit;
    }

    if ($action === 'summarize') {
        $matterId = (int)($_POST['matter_id'] ?? 0);
        $result = AIAssistant::summarizeMatter($matterId);
        echo json_encode($result);
        exit;
    }

    if ($action === 'classify') {
        $message = trim($_POST['message'] ?? '');
        $result = AIAssistant::classifyCitizenMessage($message);
        echo json_encode($result);
        exit;
    }

    if ($action === 'plain_language') {
        $matterId = (int)($_POST['matter_id'] ?? 0);
        $result = AIAssistant::generatePlainLanguageSummary($matterId);
        echo json_encode($result);
        exit;
    }

    echo json_encode(['error' => 'Ação inválida']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha no processamento de IA: ' . $e->getMessage()]);
}

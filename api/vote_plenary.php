<?php
/**
 * Câmara Digital - Votação Nominal em Sessão Plenária
 */
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método inválido']);
    exit;
}

$agendaItemId = (int)($_POST['agenda_item_id'] ?? 0);
$councilorId = (int)($_POST['councilor_id'] ?? 0);
$vote = trim($_POST['vote'] ?? 'Sim'); // Sim, Não, Abstenção

if (!$agendaItemId || !$councilorId || !in_array($vote, ['Sim', 'Não', 'Abstenção'])) {
    echo json_encode(['error' => 'Parâmetros de votação inválidos']);
    exit;
}

$db = Database::getConnection();

try {
    $db->beginTransaction();

    // Registra ou atualiza o voto do vereador
    $stmt = $db->prepare("
        INSERT INTO session_votes (agenda_item_id, councilor_id, vote, voted_at) 
        VALUES (?, ?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE vote = VALUES(vote), voted_at = NOW()
    ");
    $stmt->execute([$agendaItemId, $councilorId, $vote]);

    // Recalcula totais
    $stmtVotes = $db->prepare("
        SELECT 
            SUM(CASE WHEN vote = 'Sim' THEN 1 ELSE 0 END) as yes_count,
            SUM(CASE WHEN vote = 'Não' THEN 1 ELSE 0 END) as no_count,
            SUM(CASE WHEN vote = 'Abstenção' THEN 1 ELSE 0 END) as abstention_count
        FROM session_votes 
        WHERE agenda_item_id = ?
    ");
    $stmtVotes->execute([$agendaItemId]);
    $counts = $stmtVotes->fetch();

    $yes = (int)($counts['yes_count'] ?? 0);
    $no = (int)($counts['no_count'] ?? 0);
    $abstention = (int)($counts['abstention_count'] ?? 0);

    $result = ($yes > $no) ? 'Aprovado' : (($no > $yes) ? 'Rejeitado' : 'Empate');

    $stmtUpdate = $db->prepare("
        UPDATE session_agenda_items 
        SET votes_yes = ?, votes_no = ?, votes_abstention = ?, status = 'Votado', result = ? 
        WHERE id = ?
    ");
    $stmtUpdate->execute([$yes, $no, $abstention, $result, $agendaItemId]);

    $db->commit();

    Audit::log('VOTO_PLENARIO', 'session_agenda_items', $agendaItemId, "Voto [{$vote}] registrado para o vereador ID: {$councilorId}");

    echo json_encode([
        'status' => 'success',
        'message' => 'Voto computado com sucesso no painel eletrônico',
        'votes_yes' => $yes,
        'votes_no' => $no,
        'votes_abstention' => $abstention,
        'result' => $result
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar votação: ' . $e->getMessage()]);
}

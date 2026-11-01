<?php
/**
 * Câmara Digital - Ações do Almoxarifado e Estoque
 * Conforme Regras Críticas da Seção 4.4 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';

if (!Auth::check()) {
    header('Location: ' . url('login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('modulos/almoxarifado.php'));
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Token de autenticação inválido.');
    header('Location: ' . url('modulos/almoxarifado.php'));
    exit;
}

$action = $_POST['action'] ?? '';
$db = Database::getConnection();
$user = Auth::user();

try {
    if ($action === 'movement') {
        $itemId = (int)($_POST['material_item_id'] ?? 0);
        $type = trim($_POST['movement_type'] ?? 'ENTRADA'); // ENTRADA, SAIDA, AJUSTE
        $qty = (int)($_POST['quantity'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Movimentação operacional');
        $docRef = trim($_POST['document_ref'] ?? '');

        if ($qty <= 0) {
            throw new Exception("A quantidade deve ser maior que zero.");
        }

        $db->beginTransaction();

        // Bloqueio pessimista para evitar condição de corrida (race condition)
        $stmt = $db->prepare("SELECT * FROM material_items WHERE id = ? FOR UPDATE");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();

        if (!$item) {
            throw new Exception("Item de material não encontrado.");
        }

        $previousStock = (int)$item['current_stock'];
        $newStock = $previousStock;

        if ($type === 'ENTRADA') {
            $newStock = $previousStock + $qty;
        } elseif ($type === 'SAIDA') {
            if ($previousStock < $qty) {
                throw new Exception("Saldo insuficiente no estoque. Saldo atual: {$previousStock}, saída solicitada: {$qty}. Saldo negativo não é permitido.");
            }
            $newStock = $previousStock - $qty;
        } elseif ($type === 'AJUSTE') {
            $newStock = $qty; // Ajuste para contagem de inventário
        }

        // 1. Atualiza item
        $stmtUpdate = $db->prepare("UPDATE material_items SET current_stock = ? WHERE id = ?");
        $stmtUpdate->execute([$newStock, $itemId]);

        // 2. Grava histórico estrito de movimentação
        $stmtMov = $db->prepare("
            INSERT INTO stock_movements (material_item_id, movement_type, quantity, previous_stock, new_stock, reason, document_ref, user_name, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmtMov->execute([$itemId, $type, $qty, $previousStock, $newStock, $reason, $docRef, $user['name']]);

        $db->commit();

        Audit::log('ESTOQUE_MOVIMENTO', 'material_items', $itemId, "{$type} de {$qty} un. no item '{$item['name']}'. Saldo anterior: {$previousStock}, novo: {$newStock}");
        setFlash('success', "Movimentação de {$type} registrada com sucesso. Novo saldo: {$newStock} unidades.");
    }

    if ($action === 'approve_requisition') {
        $reqId = (int)($_POST['requisition_id'] ?? 0);

        $db->beginTransaction();

        $stmt = $db->prepare("SELECT * FROM stock_requisitions WHERE id = ? FOR UPDATE");
        $stmt->execute([$reqId]);
        $req = $stmt->fetch();

        if (!$req || $req['status'] === 'Atendida') {
            throw new Exception("Requisição inválida ou já atendida.");
        }

        // Busca itens da requisição
        $stmtItems = $db->prepare("SELECT * FROM stock_requisition_items WHERE requisition_id = ?");
        $stmtItems->execute([$reqId]);
        $items = $stmtItems->fetchAll();

        foreach ($items as $reqItem) {
            $stmtMat = $db->prepare("SELECT * FROM material_items WHERE id = ? FOR UPDATE");
            $stmtMat->execute([$reqItem['material_item_id']]);
            $mat = $stmtMat->fetch();

            $needed = (int)$reqItem['quantity_requested'];
            $current = (int)$mat['current_stock'];

            if ($current < $needed) {
                throw new Exception("Estoque insuficiente para o item '{$mat['name']}'. Saldo atual: {$current}, requerido: {$needed}.");
            }

            $newBal = $current - $needed;
            $db->prepare("UPDATE material_items SET current_stock = ? WHERE id = ?")->execute([$newBal, $mat['id']]);

            // Grava baixa
            $stmtMov = $db->prepare("
                INSERT INTO stock_movements (material_item_id, movement_type, quantity, previous_stock, new_stock, reason, document_ref, user_name, created_at)
                VALUES (?, 'SAIDA', ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmtMov->execute([$mat['id'], $needed, $current, $newBal, "Atendimento da requisição {$req['code']}", $req['code'], $user['name']]);

            // Atualiza entregue
            $db->prepare("UPDATE stock_requisition_items SET quantity_delivered = ? WHERE id = ?")->execute([$needed, $reqItem['id']]);
        }

        // Marca requisição como atendida
        $db->prepare("UPDATE stock_requisitions SET status = 'Atendida' WHERE id = ?")->execute([$reqId]);

        $db->commit();
        Audit::log('REQUISICAO_ATENDIDA', 'stock_requisitions', $reqId, "Requisição {$req['code']} atendida e baixada no estoque.");
        setFlash('success', "Requisição {$req['code']} aprovada e atendida! O estoque foi baixado com sucesso.");
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    setFlash('error', $e->getMessage());
}

header('Location: ' . url('modulos/almoxarifado.php'));
exit;

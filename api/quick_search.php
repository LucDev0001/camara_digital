<?php
/**
 * Câmara Digital - Busca Rápida Global
 */
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');
if (mb_strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$tenantId = Tenant::id();
$db = Database::getConnection();
$results = [];
$param = "%{$query}%";

// 1. Leis
$stmt = $db->prepare("
    SELECT id, norm_type, number, year, title, summary 
    FROM legal_norms 
    WHERE tenant_id = ? AND (title LIKE ? OR summary LIKE ?) 
    LIMIT 4
");
$stmt->execute([$tenantId, $param, $param]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = [
        'category' => 'Lei Municipal',
        'title' => "{$row['norm_type']} nº {$row['number']}/{$row['year']} — {$row['title']}",
        'summary' => $row['summary'],
        'date' => "Ano {$row['year']}",
        'url' => url("portal/leis.php?id={$row['id']}")
    ];
}

// 2. Proposições
$stmt = $db->prepare("
    SELECT id, code, title, summary, status, year 
    FROM legislative_matters 
    WHERE tenant_id = ? AND (title LIKE ? OR summary LIKE ? OR code LIKE ?) 
    LIMIT 4
");
$stmt->execute([$tenantId, $param, $param, $param]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = [
        'category' => 'Processo Legislativo',
        'title' => "{$row['code']} — {$row['title']}",
        'summary' => $row['summary'],
        'date' => "Situação: {$row['status']}",
        'url' => url("modulos/legislativo.php#matter-{$row['id']}")
    ];
}

// 3. Fornecedores e Contratos
$stmt = $db->prepare("
    SELECT id, contract_number, year, object_summary, total_value 
    FROM contracts 
    WHERE tenant_id = ? AND object_summary LIKE ? 
    LIMIT 3
");
$stmt->execute([$tenantId, $param]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = [
        'category' => 'Contrato',
        'title' => "Contrato nº {$row['contract_number']}/{$row['year']}",
        'summary' => $row['object_summary'],
        'date' => formatCurrency($row['total_value']),
        'url' => url("modulos/compras.php")
    ];
}

// 4. Servidores
$stmt = $db->prepare("
    SELECT id, full_name, job_title, department 
    FROM employees 
    WHERE tenant_id = ? AND (full_name LIKE ? OR job_title LIKE ?) 
    LIMIT 3
");
$stmt->execute([$tenantId, $param, $param]);
foreach ($stmt->fetchAll() as $row) {
    $results[] = [
        'category' => 'Recursos Humanos',
        'title' => "Servidor(a): {$row['full_name']}",
        'summary' => "Cargo: {$row['job_title']} — Setor: {$row['department']}",
        'date' => 'Ativo',
        'url' => url("modulos/rh.php")
    ];
}

echo json_encode($results);

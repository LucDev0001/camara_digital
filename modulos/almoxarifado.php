<?php
/**
 * Câmara Digital - Módulo 4: Almoxarifado e Controle de Estoque
 * Conforme Seção 4.4 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Almoxarifado e Estoque';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Processa Novo Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_item'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'Material de Escritório');
        $unit = trim($_POST['unit'] ?? 'UN');
        $initialStock = (int)($_POST['initial_stock'] ?? 0);
        $minStock = (int)($_POST['minimum_stock'] ?? 5);
        $cost = (float)str_replace(['.', ','], ['', '.'], $_POST['unit_cost'] ?? '0');
        $location = trim($_POST['location'] ?? 'Almoxarifado Central');

        if (!empty($name) && !empty($code)) {
            $stmt = $db->prepare("
                INSERT INTO material_items (tenant_id, code, name, category, unit, current_stock, minimum_stock, unit_cost, location, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$tenantId, $code, $name, $category, $unit, $initialStock, $minStock, $cost, $location]);
            Audit::log('CRIAR_ITEM_ESTOQUE', 'material_items', (int)$db->lastInsertId(), "Item '{$name}' cadastrado com estoque inicial {$initialStock}");
            setFlash('success', "Item '{$name}' cadastrado com sucesso!");
            header("Location: " . url('modulos/almoxarifado.php'));
            exit;
        }
    }
}

// Consultas
$items = $db->query("SELECT * FROM material_items WHERE tenant_id = {$tenantId} ORDER BY name ASC")->fetchAll();
$movements = $db->query("
    SELECT sm.*, mi.name as item_name, mi.code as item_code, mi.unit 
    FROM stock_movements sm 
    INNER JOIN material_items mi ON sm.material_item_id = mi.id 
    WHERE mi.tenant_id = {$tenantId} 
    ORDER BY sm.created_at DESC 
    LIMIT 20
")->fetchAll();

$requisitions = $db->query("
    SELECT sr.*, 
        (SELECT COUNT(*) FROM stock_requisition_items WHERE requisition_id = sr.id) as item_count 
    FROM stock_requisitions sr 
    WHERE sr.tenant_id = {$tenantId} 
    ORDER BY sr.id DESC
")->fetchAll();

$criticalCount = count(array_filter($items, fn($i) => $i['current_stock'] <= $i['minimum_stock']));

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 4</span>
                    <span>•</span>
                    <span>Materiais e Logística</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Almoxarifado & Controle de Estoque</h1>
                <p class="text-xs sm:text-sm text-slate-500">Gestão de materiais de consumo, conferência de saldos, entradas por nota fiscal e atendimento a requisições internas.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('movement-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-right-left"></i>
                    <span>Entrada / Saída Rápida</span>
                </button>
                <button onclick="openModal('new-item-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    <span>Cadastrar Novo Item</span>
                </button>
            </div>
        </div>

        <!-- Banner de Alerta de Estoque Mínimo se houver -->
        <?php if ($criticalCount > 0): ?>
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="font-bold text-xs">Atenção: <?= $criticalCount ?> <?= $criticalCount > 1 ? 'itens estão' : 'item está' ?> em nível de estoque crítico ou abaixo do mínimo!</p>
                        <p class="text-[11px] text-amber-700">Recomenda-se iniciar processo de reposição junto à Divisão de Compras.</p>
                    </div>
                </div>
                <button onclick="document.getElementById('items-filter').value = 'crítico'; filterTable('items-filter', 'items-table')" class="text-xs font-semibold text-amber-800 bg-amber-200/60 hover:bg-amber-200 px-3 py-1.5 rounded-lg transition-colors">
                    Filtrar Itens Críticos
                </button>
            </div>
        <?php endif; ?>

        <!-- Seção 1: Catálogo de Itens e Saldos -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Catálogo de Materiais e Saldos Atuais</h2>
                    <span class="text-[11px] text-slate-500">(<?= count($items) ?> cadastrados)</span>
                </div>
                <div class="w-full sm:w-64">
                    <input type="text" id="items-filter" placeholder="Filtrar por nome, código ou categoria..."
                           onkeyup="filterTable('items-filter', 'items-table')"
                           class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600" id="items-table">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Código</th>
                            <th class="p-3">Descrição do Material</th>
                            <th class="p-3">Categoria</th>
                            <th class="p-3">Unidade</th>
                            <th class="p-3">Localização</th>
                            <th class="p-3">Estoque Mínimo</th>
                            <th class="p-3">Saldo Disponível</th>
                            <th class="p-3">Valor Unitário</th>
                            <th class="p-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($items as $item): ?>
                            <?php $isCritical = $item['current_stock'] <= $item['minimum_stock']; ?>
                            <tr class="hover:bg-slate-50 <?= $isCritical ? 'bg-amber-50/40' : '' ?>">
                                <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($item['code']) ?></td>
                                <td class="p-3 font-bold text-slate-900">
                                    <?= htmlspecialchars($item['name']) ?>
                                    <?php if ($isCritical): ?>
                                        <span class="hidden">crítico</span>
                                        <span class="ml-1 text-[9px] font-bold bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded uppercase">Crítico</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-slate-500"><?= htmlspecialchars($item['category']) ?></td>
                                <td class="p-3 font-mono text-slate-700"><?= htmlspecialchars($item['unit']) ?></td>
                                <td class="p-3 text-slate-500"><?= htmlspecialchars($item['location']) ?></td>
                                <td class="p-3 font-mono text-slate-600"><?= $item['minimum_stock'] ?></td>
                                <td class="p-3 font-mono">
                                    <span class="font-bold text-sm <?= $isCritical ? 'text-amber-600' : 'text-slate-900' ?>">
                                        <?= $item['current_stock'] ?>
                                    </span>
                                </td>
                                <td class="p-3 font-mono text-slate-700"><?= formatCurrency($item['unit_cost']) ?></td>
                                <td class="p-3 text-right">
                                    <button onclick="openQuickMove(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>')" 
                                            class="px-2.5 py-1 bg-slate-100 hover:bg-sky-50 text-sky-700 font-semibold rounded text-[11px] border border-slate-200 transition-colors">
                                        Movimentar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção 2: Requisições de Setor e Histórico de Movimentações (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Coluna 1: Requisições por Setor -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-sky-600"></i>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Requisições Internas de Materiais</h2>
                    </div>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($requisitions as $req): ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-sky-700"><?= htmlspecialchars($req['code']) ?></span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded <?= $req['status'] === 'Atendida' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                    <?= htmlspecialchars($req['status']) ?>
                                </span>
                            </div>
                            <p class="font-bold text-slate-800 mt-1"><?= htmlspecialchars($req['department']) ?></p>
                            <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($req['notes'] ?: 'Solicitação de suprimentos periódicos.') ?></p>
                            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                                <span>Solicitante: <strong><?= htmlspecialchars($req['requester_name']) ?></strong></span>
                                <?php if ($req['status'] !== 'Atendida'): ?>
                                    <form action="<?= url('api/stock_action.php') ?>" method="POST">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="approve_requisition">
                                        <input type="hidden" name="requisition_id" value="<?= $req['id'] ?>">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded text-[10px] shadow-sm">
                                            Aprovar e Baixar Estoque
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-emerald-600 font-semibold text-[10px]"><i class="fa-solid fa-check mr-1"></i> Entregue</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Coluna 2: Histórico de Movimentações Auditadas -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-600"></i>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Trilha de Movimentações (Rastreabilidade)</h2>
                    </div>
                </div>
                <div class="divide-y divide-slate-100 text-xs max-h-96 overflow-y-auto">
                    <?php foreach ($movements as $mov): ?>
                        <div class="p-3 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($mov['item_name']) ?></span>
                                <span class="font-bold font-mono text-xs <?= $mov['movement_type'] === 'ENTRADA' ? 'text-emerald-600' : 'text-rose-600' ?>">
                                    <?= $mov['movement_type'] === 'ENTRADA' ? '+' : '-' ?><?= $mov['quantity'] ?> <?= $mov['unit'] ?>
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-600 mt-0.5"><?= htmlspecialchars($mov['reason']) ?></p>
                            <div class="mt-2 flex items-center justify-between text-[10px] text-slate-400">
                                <span>Saldo: <?= $mov['previous_stock'] ?> &rarr; <strong><?= $mov['new_stock'] ?></strong></span>
                                <span><?= formatDate($mov['created_at'], true) ?> por <?= htmlspecialchars($mov['user_name']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </div>
</main>

<!-- Modal: Novo Item -->
<div id="new-item-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Cadastrar Novo Item de Material</h3>
            <button onclick="closeModal('new-item-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/almoxarifado.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_item" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Código do Material</label>
                    <input type="text" name="code" required placeholder="Ex: MAT-010"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Unidade de Medida</label>
                    <input type="text" name="unit" required placeholder="UN, CX, PCT, KG" value="UN"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block font-medium text-slate-700">Descrição Completa</label>
                    <input type="text" name="name" required placeholder="Ex: Grampeador de Mesa Médio 26/6"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Categoria</label>
                    <select name="category" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Material de Escritório">Material de Escritório</option>
                        <option value="Informática e Impressão">Informática e Impressão</option>
                        <option value="Copa e Cozinha">Copa e Cozinha</option>
                        <option value="Higiene e Limpeza">Higiene e Limpeza</option>
                        <option value="Arquivo e Protocolo">Arquivo e Protocolo</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Local de Armazenamento</label>
                    <input type="text" name="location" placeholder="Ex: Prateleira B3"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Estoque Mínimo de Alerta</label>
                    <input type="number" name="minimum_stock" value="10" required
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Estoque Inicial</label>
                    <input type="number" name="initial_stock" value="0" required
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-item-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Item
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Movimentação de Estoque -->
<div id="movement-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Registrar Entrada / Saída de Material</h3>
            <button onclick="closeModal('movement-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('api/stock_action.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="movement">

            <div>
                <label class="block font-medium text-slate-700">Selecione o Material</label>
                <select name="material_item_id" id="move-item-select" required class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <?php foreach ($items as $i): ?>
                        <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['code']) ?> — <?= htmlspecialchars($i['name']) ?> (Saldo: <?= $i['current_stock'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Tipo da Operação</label>
                    <select name="movement_type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="ENTRADA">Entrada (Compra / NF)</option>
                        <option value="SAIDA">Saída / Baixa Operacional</option>
                        <option value="AJUSTE">Ajuste de Inventário</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Quantidade</label>
                    <input type="number" name="quantity" required min="1" value="1"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none font-bold">
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Motivo / Justificativa</label>
                <input type="text" name="reason" required placeholder="Ex: Entrega conforme NF 12345 da empresa Pantanal"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Documento de Referência (NF / Requerimento)</label>
                <input type="text" name="document_ref" placeholder="NF-12345"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('movement-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg shadow-sm">
                    Confirmar Movimentação
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openQuickMove(itemId, itemName) {
    const select = document.getElementById('move-item-select');
    if (select) {
        select.value = itemId;
    }
    openModal('movement-modal');
}
</script>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

<?php
/**
 * Câmara Digital - Módulo 7: Financeiro, Orçamento e Contabilidade
 * Conforme Seção 4.7 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Financeiro e Orçamento';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Processa Nova Despesa / Diária
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_expense'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $budgetId = (int)($_POST['budget_allocation_id'] ?? 0);
        $type = trim($_POST['expense_type'] ?? 'Geral');
        $beneficiary = trim($_POST['beneficiary_name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $amount = (float)str_replace(['.', ','], ['', '.'], $_POST['amount'] ?? '0');
        $reqDate = $_POST['request_date'] ?? date('Y-m-d');

        if (!empty($beneficiary) && $amount > 0) {
            $stmt = $db->prepare("
                INSERT INTO expense_requests (tenant_id, budget_allocation_id, expense_type, beneficiary_name, description, amount, request_date, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Autorizada', NOW())
            ");
            $stmt->execute([$tenantId, $budgetId ?: null, $type, $beneficiary, $desc, $amount, $reqDate]);

            // Atualiza empenhado na dotação
            if ($budgetId) {
                $db->prepare("UPDATE budget_allocations SET committed_budget = committed_budget + ? WHERE id = ?")
                   ->execute([$amount, $budgetId]);
            }

            Audit::log('CRIAR_DESPESA', 'expense_requests', (int)$db->lastInsertId(), "Despesa/Diária no valor de R$ {$amount} autorizada para: {$beneficiary}");
            setFlash('success', "Despesa registrada e empenhada na dotação orçamentária!");
            header("Location: " . url('modulos/financeiro.php'));
            exit;
        }
    }
}

$allocations = $db->query("SELECT * FROM budget_allocations WHERE tenant_id = {$tenantId} ORDER BY code ASC")->fetchAll();
$expenses = $db->query("
    SELECT er.*, ba.code as budget_code 
    FROM expense_requests er 
    LEFT JOIN budget_allocations ba ON er.budget_allocation_id = ba.id 
    WHERE er.tenant_id = {$tenantId} 
    ORDER BY er.request_date DESC
")->fetchAll();

$totalInitial = array_sum(array_column($allocations, 'initial_budget'));
$totalCommitted = array_sum(array_column($allocations, 'committed_budget'));
$totalRealized = array_sum(array_column($allocations, 'realized_budget'));

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 7</span>
                    <span>•</span>
                    <span>Gestão Orçamentária e Fiscal</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Financeiro, Orçamento & Diárias</h1>
                <p class="text-xs sm:text-sm text-slate-500">Execução orçamentária, saldo disponível por programa de trabalho, empenhos e controle de diárias de viagem.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('new-expense-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>Registrar Despesa / Diária</span>
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dotação Inicial (2026)</span>
                <p class="text-xl font-bold text-slate-900 font-mono mt-2"><?= formatCurrency($totalInitial) ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Aprovada na LOA</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Recursos Empenhados</span>
                <p class="text-xl font-bold text-indigo-600 font-mono mt-2"><?= formatCurrency($totalCommitted) ?></p>
                <span class="text-[11px] text-indigo-700 mt-1 block font-semibold"><?= $totalInitial > 0 ? round(($totalCommitted / $totalInitial) * 100, 1) : 0 ?>% do orçamento</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Liquidado / Pago</span>
                <p class="text-xl font-bold text-emerald-600 font-mono mt-2"><?= formatCurrency($totalRealized) ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Despesas efetivadas</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Saldo Orçamentário Livre</span>
                <p class="text-xl font-bold text-sky-700 font-mono mt-2"><?= formatCurrency($totalInitial - $totalCommitted) ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Disponível para aplicação</span>
            </div>
        </div>

        <!-- Seção 1: Programas de Trabalho e Dotações -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Dotações Orçamentárias por Programa</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Dotação / Código</th>
                            <th class="p-3">Descrição da Ação</th>
                            <th class="p-3">Dotação Inicial</th>
                            <th class="p-3">Empenhado</th>
                            <th class="p-3">Liquidado / Pago</th>
                            <th class="p-3">Saldo Disponível</th>
                            <th class="p-3">% Execução</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($allocations as $a): ?>
                            <?php $freeBalance = $a['initial_budget'] - $a['committed_budget']; ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($a['code']) ?></td>
                                <td class="p-3 font-bold text-slate-800"><?= htmlspecialchars($a['description']) ?></td>
                                <td class="p-3 font-mono font-medium text-slate-700"><?= formatCurrency($a['initial_budget']) ?></td>
                                <td class="p-3 font-mono font-bold text-indigo-700"><?= formatCurrency($a['committed_budget']) ?></td>
                                <td class="p-3 font-mono font-bold text-emerald-700"><?= formatCurrency($a['realized_budget']) ?></td>
                                <td class="p-3 font-mono font-bold text-slate-900"><?= formatCurrency($freeBalance) ?></td>
                                <td class="p-3">
                                    <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-sky-600 h-2 rounded-full" style="width: <?= min(100, round(($a['committed_budget'] / $a['initial_budget']) * 100)) ?>%"></div>
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-0.5 block"><?= round(($a['committed_budget'] / $a['initial_budget']) * 100, 1) ?>%</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção 2: Despesas e Diárias de Viagem -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Solicitações de Despesas e Diárias Autorizadas</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Tipo</th>
                            <th class="p-3">Favorecido / Beneficiário</th>
                            <th class="p-3">Descrição / Justificativa</th>
                            <th class="p-3">Dotação Vinculada</th>
                            <th class="p-3">Data</th>
                            <th class="p-3">Valor (R$)</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($expenses as $exp): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $exp['expense_type'] === 'Diária de Viagem' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' ?>">
                                        <?= htmlspecialchars($exp['expense_type']) ?>
                                    </span>
                                </td>
                                <td class="p-3 font-bold text-slate-900 whitespace-nowrap"><?= htmlspecialchars($exp['beneficiary_name']) ?></td>
                                <td class="p-3 text-slate-700 line-clamp-1"><?= htmlspecialchars($exp['description']) ?></td>
                                <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= htmlspecialchars($exp['budget_code'] ?: 'Geral') ?></td>
                                <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= formatDate($exp['request_date']) ?></td>
                                <td class="p-3 font-bold font-mono text-slate-900 whitespace-nowrap"><?= formatCurrency($exp['amount']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <?= htmlspecialchars($exp['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Nova Despesa -->
<div id="new-expense-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Registrar Solicitação de Despesa / Diária</h3>
            <button onclick="closeModal('new-expense-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/financeiro.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_expense" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Tipo de Despesa</label>
                    <select name="expense_type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Diária de Viagem">Diária de Viagem</option>
                        <option value="Serviço de Terceiros">Serviço de Terceiros PJ/PF</option>
                        <option value="Material de Consumo">Material de Consumo</option>
                        <option value="Adiantamento">Adiantamento para Pequenas Despesas</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Dotação Orçamentária</label>
                    <select name="budget_allocation_id" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <?php foreach ($allocations as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['code']) ?> — <?= htmlspecialchars($a['description']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Favorecido / Beneficiário</label>
                <input type="text" name="beneficiary_name" required placeholder="Nome do Parlamentar, Servidor ou Empresa"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Descrição / Justificativa da Despesa</label>
                <textarea name="description" rows="2" required placeholder="Participação em congresso, aquisição ou serviço..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Valor Total (R$)</label>
                    <input type="text" name="amount" required placeholder="1.250,00"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Data do Pedido</label>
                    <input type="date" name="request_date" required value="<?= date('Y-m-d') ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-expense-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Autorizar Despesa
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

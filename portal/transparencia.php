<?php
/**
 * Câmara Digital - Portal da Transparência Pública (Lei 12.527/11 e LRF)
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Portal da Transparência Pública';
$tenantId = Tenant::id();
$db = Database::getConnection();

$tab = $_GET['tab'] ?? 'despesas';

$contracts = $db->query("
    SELECT c.*, s.corporate_name, s.cnpj_cpf 
    FROM contracts c 
    INNER JOIN suppliers s ON c.supplier_id = s.id 
    WHERE c.tenant_id = {$tenantId} 
    ORDER BY c.start_date DESC
")->fetchAll();

$procurements = $db->query("SELECT * FROM procurements WHERE tenant_id = {$tenantId} ORDER BY opening_date DESC")->fetchAll();
$employees = $db->query("SELECT * FROM employees WHERE tenant_id = {$tenantId} ORDER BY full_name ASC")->fetchAll();
$expenses = $db->query("SELECT * FROM expense_requests WHERE tenant_id = {$tenantId} ORDER BY request_date DESC")->fetchAll();
$allocations = $db->query("SELECT * FROM budget_allocations WHERE tenant_id = {$tenantId} ORDER BY code ASC")->fetchAll();

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 uppercase tracking-wider">
                <i class="fa-solid fa-award"></i>
                <span>Transparência Pública e Controle Social</span>
            </div>
            <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Portal da Transparência Oficial</h1>
            <p class="text-xs sm:text-sm text-slate-500">Acesso livre e irrestrito aos gastos, contratos, licitações, folha de servidores e orçamento municipal.</p>
        </div>
        <div class="text-right">
            <span class="text-[11px] text-slate-400 block">Atualização em tempo real</span>
            <span class="text-xs font-semibold text-emerald-600"><i class="fa-solid fa-circle-check mr-1"></i> Dados Auditados</span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 text-xs font-semibold">
        <a href="<?= url('portal/transparencia.php?tab=despesas') ?>" 
           class="px-4 py-2 rounded-xl transition-all <?= $tab === 'despesas' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <i class="fa-solid fa-money-bill-wave mr-1.5"></i> Despesas & Orçamento
        </a>
        <a href="<?= url('portal/transparencia.php?tab=contratos') ?>" 
           class="px-4 py-2 rounded-xl transition-all <?= $tab === 'contratos' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <i class="fa-solid fa-file-contract mr-1.5"></i> Licitações & Contratos (14.133)
        </a>
        <a href="<?= url('portal/transparencia.php?tab=servidores') ?>" 
           class="px-4 py-2 rounded-xl transition-all <?= $tab === 'servidores' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <i class="fa-solid fa-users mr-1.5"></i> Quadro de Pessoal
        </a>
        <a href="<?= url('portal/transparencia.php?tab=diarias') ?>" 
           class="px-4 py-2 rounded-xl transition-all <?= $tab === 'diarias' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <i class="fa-solid fa-plane-departure mr-1.5"></i> Diárias & Viagens
        </a>
        <a href="<?= url('modulos/transparencia.php?export=contratos') ?>" 
           class="ml-auto px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors text-xs font-semibold flex items-center gap-1.5">
            <i class="fa-solid fa-download text-emerald-600"></i>
            <span>Exportar Dados Abertos (CSV)</span>
        </a>
    </div>

    <!-- Conteúdo da Aba Ativa -->
    <?php if ($tab === 'despesas'): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Execução do Orçamento Anual (LOA 2026)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Código</th>
                            <th class="p-3">Ação Orçamentária</th>
                            <th class="p-3">Dotação Fixada</th>
                            <th class="p-3">Valor Empenhado</th>
                            <th class="p-3">Liquidado / Pago</th>
                            <th class="p-3">Saldo Disponível</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($allocations as $a): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($a['code']) ?></td>
                                <td class="p-3 font-semibold text-slate-900"><?= htmlspecialchars($a['description']) ?></td>
                                <td class="p-3 font-mono"><?= formatCurrency($a['initial_budget']) ?></td>
                                <td class="p-3 font-mono text-indigo-700 font-bold"><?= formatCurrency($a['committed_budget']) ?></td>
                                <td class="p-3 font-mono text-emerald-700 font-bold"><?= formatCurrency($a['realized_budget']) ?></td>
                                <td class="p-3 font-mono font-bold text-slate-900"><?= formatCurrency($a['initial_budget'] - $a['committed_budget']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($tab === 'contratos'): ?>
        <div class="space-y-6">
            <!-- Licitações -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Licitações e Contratações Diretas (Lei 14.133/21)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="p-3">Processo</th>
                                <th class="p-3">Modalidade</th>
                                <th class="p-3">Objeto</th>
                                <th class="p-3">Valor Estimado</th>
                                <th class="p-3">Data</th>
                                <th class="p-3">PNCP</th>
                                <th class="p-3">Situação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($procurements as $p): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($p['process_number']) ?></td>
                                    <td class="p-3 font-medium text-slate-700"><?= htmlspecialchars($p['modality']) ?></td>
                                    <td class="p-3 text-slate-900"><?= htmlspecialchars($p['object_description']) ?></td>
                                    <td class="p-3 font-mono font-bold text-slate-900"><?= formatCurrency($p['estimated_value']) ?></td>
                                    <td class="p-3 font-mono text-slate-500"><?= formatDate($p['opening_date']) ?></td>
                                    <td class="p-3">
                                        <a href="<?= htmlspecialchars($p['pncp_link']) ?>" target="_blank" class="text-sky-600 font-semibold hover:underline">
                                            Acessar PNCP &rarr;
                                        </a>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800"><?= htmlspecialchars($p['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Contratos -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Contratos Celebrados e Fiscais Designados</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="p-3">Contrato</th>
                                <th class="p-3">Contratado / CNPJ</th>
                                <th class="p-3">Objeto</th>
                                <th class="p-3">Valor Global</th>
                                <th class="p-3">Vigência</th>
                                <th class="p-3">Fiscal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($contracts as $c): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($c['contract_number']) ?>/<?= $c['year'] ?></td>
                                    <td class="p-3">
                                        <p class="font-bold text-slate-900"><?= htmlspecialchars($c['corporate_name']) ?></p>
                                        <p class="text-[10px] text-slate-400 font-mono"><?= formatCnpj($c['cnpj_cpf']) ?></p>
                                    </td>
                                    <td class="p-3 text-slate-800"><?= htmlspecialchars($c['object_summary']) ?></td>
                                    <td class="p-3 font-mono font-bold text-emerald-700"><?= formatCurrency($c['total_value']) ?></td>
                                    <td class="p-3 font-mono text-slate-500"><?= formatDate($c['start_date']) ?> a <?= formatDate($c['end_date']) ?></td>
                                    <td class="p-3 text-slate-700"><?= htmlspecialchars($c['inspector_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($tab === 'servidores'): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Relação de Servidores e Remuneração Base (Transparência Ativa)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Matrícula</th>
                            <th class="p-3">Nome do Servidor</th>
                            <th class="p-3">Cargo / Função</th>
                            <th class="p-3">Lotação</th>
                            <th class="p-3">Vínculo</th>
                            <th class="p-3">Remuneração Base</th>
                            <th class="p-3">Admissão</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($employees as $e): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($e['registration_number']) ?></td>
                                <td class="p-3 font-bold text-slate-900"><?= htmlspecialchars($e['full_name']) ?></td>
                                <td class="p-3 text-slate-700"><?= htmlspecialchars($e['job_title']) ?></td>
                                <td class="p-3 text-slate-600"><?= htmlspecialchars($e['department']) ?></td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700"><?= htmlspecialchars($e['employment_type']) ?></span>
                                </td>
                                <td class="p-3 font-mono font-bold text-slate-900"><?= formatCurrency($e['salary']) ?></td>
                                <td class="p-3 font-mono text-slate-500"><?= formatDate($e['admission_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($tab === 'diarias'): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Concessão de Diárias e Indenizações de Viagem</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Beneficiário</th>
                            <th class="p-3">Destino e Justificativa da Viagem</th>
                            <th class="p-3">Data</th>
                            <th class="p-3">Valor Concedido</th>
                            <th class="p-3">Situação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($expenses as $exp): ?>
                            <?php if ($exp['expense_type'] === 'Diária de Viagem'): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-bold text-slate-900"><?= htmlspecialchars($exp['beneficiary_name']) ?></td>
                                    <td class="p-3 text-slate-700"><?= htmlspecialchars($exp['description']) ?></td>
                                    <td class="p-3 font-mono text-slate-500"><?= formatDate($exp['request_date']) ?></td>
                                    <td class="p-3 font-mono font-bold text-emerald-700"><?= formatCurrency($exp['amount']) ?></td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800"><?= htmlspecialchars($exp['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

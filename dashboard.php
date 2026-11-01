<?php
/**
 * Câmara Digital - Painel Geral Integrado (Dashboard)
 */
require_once __DIR__ . '/config/app.php';

$pageTitle = 'Painel Geral Integrado';
$tenantId = Tenant::id();
$db = Database::getConnection();

// 1. Métricas Principais
$stats = [
    'matters' => (int)$db->query("SELECT COUNT(*) FROM legislative_matters WHERE tenant_id = {$tenantId}")->fetchColumn(),
    'sessions' => (int)$db->query("SELECT COUNT(*) FROM sessions WHERE tenant_id = {$tenantId}")->fetchColumn(),
    'laws' => (int)$db->query("SELECT COUNT(*) FROM legal_norms WHERE tenant_id = {$tenantId}")->fetchColumn(),
    'employees' => (int)$db->query("SELECT COUNT(*) FROM employees WHERE tenant_id = {$tenantId} AND status = 'Ativo'")->fetchColumn(),
    'contracts' => (int)$db->query("SELECT COUNT(*) FROM contracts WHERE tenant_id = {$tenantId} AND status = 'Vigente'")->fetchColumn(),
    'contracts_val' => (float)$db->query("SELECT COALESCE(SUM(total_value),0) FROM contracts WHERE tenant_id = {$tenantId} AND status = 'Vigente'")->fetchColumn(),
    'stock_alerts' => (int)$db->query("SELECT COUNT(*) FROM material_items WHERE tenant_id = {$tenantId} AND current_stock <= minimum_stock")->fetchColumn(),
    'citizen_pending' => (int)$db->query("SELECT COUNT(*) FROM citizen_requests WHERE tenant_id = {$tenantId} AND status != 'Respondido'")->fetchColumn(),
    'protocols_open' => (int)$db->query("SELECT COUNT(*) FROM protocols WHERE tenant_id = {$tenantId} AND status = 'Aberto'")->fetchColumn(),
    'budget_initial' => (float)$db->query("SELECT COALESCE(SUM(initial_budget),0) FROM budget_allocations WHERE tenant_id = {$tenantId}")->fetchColumn(),
    'budget_committed' => (float)$db->query("SELECT COALESCE(SUM(committed_budget),0) FROM budget_allocations WHERE tenant_id = {$tenantId}")->fetchColumn(),
    'budget_realized' => (float)$db->query("SELECT COALESCE(SUM(realized_budget),0) FROM budget_allocations WHERE tenant_id = {$tenantId}")->fetchColumn(),
];

// 2. Matérias por tipo para o gráfico
$stmtTypes = $db->prepare("
    SELECT type, COUNT(*) as qty 
    FROM legislative_matters 
    WHERE tenant_id = ? 
    GROUP BY type
");
$stmtTypes->execute([$tenantId]);
$mattersByType = $stmtTypes->fetchAll();
$chartTypesLabels = array_column($mattersByType, 'type');
$chartTypesValues = array_map('intval', array_column($mattersByType, 'qty'));

// 3. Proposições Recentes
$stmtRecentMatters = $db->prepare("
    SELECT * FROM legislative_matters 
    WHERE tenant_id = ? 
    ORDER BY id DESC 
    LIMIT 5
");
$stmtRecentMatters->execute([$tenantId]);
$recentMatters = $stmtRecentMatters->fetchAll();

// 4. Sessões Plenárias Próximas / Recentes
$stmtSessions = $db->prepare("
    SELECT * FROM sessions 
    WHERE tenant_id = ? 
    ORDER BY session_date DESC, start_time DESC 
    LIMIT 3
");
$stmtSessions->execute([$tenantId]);
$recentSessions = $stmtSessions->fetchAll();

// 5. Últimos Registros de Auditoria
$stmtAudit = $db->prepare("
    SELECT * FROM audit_logs 
    WHERE tenant_id = ? 
    ORDER BY id DESC 
    LIMIT 6
");
$stmtAudit->execute([$tenantId]);
$recentAudits = $stmtAudit->fetchAll();

require_once __DIR__ . '/includes/Header.php';
require_once __DIR__ . '/includes/Sidebar.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-sky-950 to-indigo-950 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none text-white text-9xl">
                <i class="fa-solid fa-landmark"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-white/10 text-sky-200 text-xs font-semibold mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span><?= htmlspecialchars($currentTenant['name']) ?></span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white font-heading">
                        Bem-vindo(a), <?= htmlspecialchars($currentUser['name']) ?>!
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl">
                        Painel Integrado de Governança Legislativa e Administrativa. Acompanhe tramitações, estoque, licitações, folha e inteligência artificial em tempo real.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?= url('modulos/ia_legislativa.php') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs rounded-xl shadow-lg shadow-purple-600/30 transition-all">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>Abrir IA Legislativa</span>
                    </a>
                    <a href="<?= url('modulos/legislativo.php') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 transition-all">
                        <i class="fa-solid fa-scale-balanced"></i>
                        <span>Processo Legislativo</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards (4 Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Processo Legislativo -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm card-hover transition-all-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Matérias Legislativas</span>
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900"><?= $stats['matters'] ?></span>
                    <span class="text-xs text-slate-500">em tramitação</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-[11px] pt-3 border-t border-slate-100">
                    <span class="text-slate-600"><?= $stats['laws'] ?> leis promulgadas</span>
                    <a href="<?= url('modulos/legislativo.php') ?>" class="text-sky-600 font-semibold hover:underline">Acessar &rarr;</a>
                </div>
            </div>

            <!-- Card 2: Contratos & Compras -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm card-hover transition-all-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contratos Vigentes</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900"><?= $stats['contracts'] ?></span>
                    <span class="text-xs text-slate-500"><?= formatCurrency($stats['contracts_val']) ?></span>
                </div>
                <div class="mt-3 flex items-center justify-between text-[11px] pt-3 border-t border-slate-100">
                    <span class="text-slate-600">Lei 14.133 / PNCP</span>
                    <a href="<?= url('modulos/compras.php') ?>" class="text-emerald-600 font-semibold hover:underline">Ver Editais &rarr;</a>
                </div>
            </div>

            <!-- Card 3: Almoxarifado / Alertas -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm card-hover transition-all-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Almoxarifado & Estoque</span>
                    <div class="w-10 h-10 rounded-xl <?= $stats['stock_alerts'] > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-600' ?> flex items-center justify-center text-base">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold <?= $stats['stock_alerts'] > 0 ? 'text-amber-600' : 'text-slate-900' ?>">
                        <?= $stats['stock_alerts'] ?>
                    </span>
                    <span class="text-xs text-slate-500">itens em estoque crítico</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-[11px] pt-3 border-t border-slate-100">
                    <span class="text-slate-600">Saldo mínimo auditado</span>
                    <a href="<?= url('modulos/almoxarifado.php') ?>" class="text-amber-600 font-semibold hover:underline">Repor Itens &rarr;</a>
                </div>
            </div>

            <!-- Card 4: Ouvidoria e SIC -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm card-hover transition-all-200">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ouvidoria & SIC</span>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900"><?= $stats['citizen_pending'] ?></span>
                    <span class="text-xs text-slate-500">manifestações abertas</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-[11px] pt-3 border-t border-slate-100">
                    <span class="text-slate-600">Prazo LAI: 20 dias</span>
                    <a href="<?= url('modulos/ouvidoria.php') ?>" class="text-purple-600 font-semibold hover:underline">Responder &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Charts Section (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 1: Proposições por Tipo (Donut) -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Composição Legislativa</h3>
                    <span class="text-[11px] text-slate-500">11ª Legislatura</span>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="mattersChart"></canvas>
                </div>
                <div class="mt-3 text-center text-[11px] text-slate-500">
                    Distribuição proporcional das proposições cadastradas no sistema
                </div>
            </div>

            <!-- Chart 2: Execução Orçamentária (Bar) -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Execução Orçamentária Anual (2026)</h3>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-sky-500"></span> Dotação</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-indigo-500"></span> Empenhado</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500"></span> Pago</span>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="budgetChart"></canvas>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100">
                    <span>Total Previsto: <strong><?= formatCurrency($stats['budget_initial']) ?></strong></span>
                    <span>Disponível: <strong class="text-emerald-600"><?= formatCurrency($stats['budget_initial'] - $stats['budget_committed']) ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Proposições e Sessões (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Coluna 1: Proposições Recentes -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-file-pen text-sky-600 text-sm"></i>
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800">Últimas Proposições Cadastradas</h3>
                    </div>
                    <a href="<?= url('modulos/legislativo.php') ?>" class="text-xs text-sky-600 font-semibold hover:underline">Ver todas</a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($recentMatters as $m): ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100"><?= htmlspecialchars($m['code']) ?></span>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full <?= $m['status'] === 'Aprovado' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                    <?= htmlspecialchars($m['status']) ?>
                                </span>
                            </div>
                            <h4 class="font-bold text-xs text-slate-900 mt-1"><?= htmlspecialchars($m['title']) ?></h4>
                            <p class="text-slate-500 text-[11px] mt-0.5 line-clamp-1"><?= htmlspecialchars($m['summary']) ?></p>
                            <div class="flex items-center justify-between mt-2 text-[10px] text-slate-400">
                                <span><i class="fa-solid fa-user-pen mr-1"></i> <?= htmlspecialchars($m['author_name']) ?></span>
                                <span><i class="fa-regular fa-calendar mr-1"></i> <?= formatDate($m['presentation_date']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Coluna 2: Sessões Plenárias e Pauta -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-gavel text-amber-600 text-sm"></i>
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800">Sessões Plenárias</h3>
                    </div>
                    <a href="<?= url('modulos/legislativo.php#sessions') ?>" class="text-xs text-sky-600 font-semibold hover:underline">Ver Pautas</a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($recentSessions as $s): ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">
                                    <?= $s['session_number'] ?>ª Sessão <?= htmlspecialchars($s['session_type']) ?>
                                </span>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full <?= $s['status'] === 'Encerrada' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                    <?= htmlspecialchars($s['status']) ?>
                                </span>
                            </div>
                            <p class="text-slate-600 text-xs mt-1"><?= htmlspecialchars($s['summary'] ?: 'Pauta da Ordem do Dia.') ?></p>
                            <div class="flex items-center justify-between mt-3 text-[11px] text-slate-500 pt-2 border-t border-slate-50">
                                <span><i class="fa-regular fa-clock mr-1 text-sky-600"></i> <?= formatDate($s['session_date']) ?> às <?= substr($s['start_time'], 0, 5) ?></span>
                                <?php if (!empty($s['minutes_text'])): ?>
                                    <span class="text-emerald-600 font-medium"><i class="fa-solid fa-check-double mr-1"></i> Ata Publicada</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Trilha de Auditoria Recente -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-emerald-600 text-sm"></i>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800">Trilha de Auditoria do Sistema (Audit Log)</h3>
                </div>
                <span class="text-[11px] text-slate-500">Conformidade e Rastreabilidade Integral</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Data/Hora</th>
                            <th class="p-3">Usuário</th>
                            <th class="p-3">Ação</th>
                            <th class="p-3">Entidade</th>
                            <th class="p-3">Detalhes</th>
                            <th class="p-3">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($recentAudits as $a): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono text-[11px] text-slate-500"><?= formatDate($a['created_at'], true) ?></td>
                                <td class="p-3 font-semibold text-slate-800"><?= htmlspecialchars($a['user_name']) ?></td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                        <?= htmlspecialchars($a['action']) ?>
                                    </span>
                                </td>
                                <td class="p-3 font-mono text-slate-500"><?= htmlspecialchars($a['entity']) ?></td>
                                <td class="p-3 text-slate-700"><?= htmlspecialchars($a['details']) ?></td>
                                <td class="p-3 font-mono text-slate-400 text-[11px]"><?= htmlspecialchars($a['ip_address']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Chart.js Scripts Initialization -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Matérias Chart (Donut)
    const ctxMatters = document.getElementById('mattersChart');
    if (ctxMatters) {
        new Chart(ctxMatters, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chartTypesLabels) ?>,
                datasets: [{
                    data: <?= json_encode($chartTypesValues) ?>,
                    backgroundColor: ['#0284c7', '#6366f1', '#10b981', '#f59e0b', '#ec4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                }
            }
        });
    }

    // 2. Budget Chart (Bar)
    const ctxBudget = document.getElementById('budgetChart');
    if (ctxBudget) {
        new Chart(ctxBudget, {
            type: 'bar',
            data: {
                labels: ['Manutenção Geral', 'Capacitação', 'Modernização TI'],
                datasets: [
                    {
                        label: 'Dotação Inicial',
                        data: [12500000, 350000, 980000],
                        backgroundColor: '#0284c7',
                        borderRadius: 6
                    },
                    {
                        label: 'Empenhado',
                        data: [4200000, 85000, 320000],
                        backgroundColor: '#6366f1',
                        borderRadius: 6
                    },
                    {
                        label: 'Liquidado / Pago',
                        data: [3100000, 62000, 240000],
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + (value / 1000000) + 'M';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/Footer.php'; ?>

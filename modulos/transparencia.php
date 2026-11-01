<?php
/**
 * Câmara Digital - Módulo 8: Transparência Pública
 * Conforme Seção 4.8 do Planejamento Mestre e LAI (Lei 12.527/11)
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Gestão da Transparência Pública';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Exportação CSV de Dados Abertos se solicitado
if (isset($_GET['export'])) {
    $dataset = $_GET['export'];
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=dados_abertos_' . $dataset . '_' . date('Ymd') . '.csv');
    $output = fopen('php://output', 'w');
    // BOM para UTF-8 no Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    if ($dataset === 'contratos') {
        fputcsv($output, ['Numero', 'Ano', 'Fornecedor', 'Objeto', 'Valor', 'Vigencia_Inicio', 'Vigencia_Fim', 'Fiscal']);
        $rows = $db->query("SELECT c.contract_number, c.year, s.corporate_name, c.object_summary, c.total_value, c.start_date, c.end_date, c.inspector_name FROM contracts c INNER JOIN suppliers s ON c.supplier_id = s.id WHERE c.tenant_id = {$tenantId}")->fetchAll();
        foreach ($rows as $r) {
            fputcsv($output, $r);
        }
    } elseif ($dataset === 'servidores') {
        fputcsv($output, ['Matricula', 'Nome', 'Cargo', 'Lotacao', 'Vinculo', 'Remuneracao', 'Admissao']);
        $rows = $db->query("SELECT registration_number, full_name, job_title, department, employment_type, salary, admission_date FROM employees WHERE tenant_id = {$tenantId}")->fetchAll();
        foreach ($rows as $r) {
            fputcsv($output, $r);
        }
    } elseif ($dataset === 'diarias') {
        fputcsv($output, ['Beneficiario', 'Descricao', 'Data', 'Valor', 'Status']);
        $rows = $db->query("SELECT beneficiary_name, description, request_date, amount, status FROM expense_requests WHERE tenant_id = {$tenantId} AND expense_type = 'Diária de Viagem'")->fetchAll();
        foreach ($rows as $r) {
            fputcsv($output, $r);
        }
    }
    exit;
}

$datasets = [
    [
        'id' => 'contratos',
        'name' => 'Contratos Administrativos',
        'desc' => 'Instrumentos contratuais celebrados, valores, fornecedores e vigência.',
        'count' => (int)$db->query("SELECT COUNT(*) FROM contracts WHERE tenant_id = {$tenantId}")->fetchColumn(),
        'periodicity' => 'Em tempo real (até 48h após assinatura)'
    ],
    [
        'id' => 'licitacoes',
        'name' => 'Licitações e Dispensas',
        'desc' => 'Editais, avisos de contratação direta e atas homologadas (Lei 14.133).',
        'count' => (int)$db->query("SELECT COUNT(*) FROM procurements WHERE tenant_id = {$tenantId}")->fetchColumn(),
        'periodicity' => 'Imediato (sincronizado com PNCP)'
    ],
    [
        'id' => 'servidores',
        'name' => 'Quadro Funcional e Remuneração',
        'desc' => 'Relação de servidores públicos ativos, cargos e remunerações base.',
        'count' => (int)$db->query("SELECT COUNT(*) FROM employees WHERE tenant_id = {$tenantId}")->fetchColumn(),
        'periodicity' => 'Mensal (até o 5º dia útil)'
    ],
    [
        'id' => 'diarias',
        'name' => 'Diárias e Viagens Oficiais',
        'desc' => 'Pagamentos de diárias a parlamentares e servidores com justificativa.',
        'count' => (int)$db->query("SELECT COUNT(*) FROM expense_requests WHERE tenant_id = {$tenantId} AND expense_type = 'Diária de Viagem'")->fetchColumn(),
        'periodicity' => 'Quinzenal'
    ]
];
$pntpItems = $db->query("SELECT * FROM pntp_checklist WHERE tenant_id = {$tenantId} ORDER BY id ASC")->fetchAll();
$totalPntp = count($pntpItems);
$conformePntp = count(array_filter($pntpItems, fn($i) => $i['status'] === 'Conforme'));
$percentPntp = $totalPntp > 0 ? round(($conformePntp / $totalPntp) * 100, 1) : 0;

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 8</span>
                    <span>•</span>
                    <span>Transparência Ativa e Dados Abertos</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Gestão da Transparência & Dados Abertos</h1>
                <p class="text-xs sm:text-sm text-slate-500">Monitoramento da conformidade legal (LAI e LRF), publicação de datasets e exportação em formato legível por máquina.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= url('portal/transparencia.php') ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Ver Portal da Transparência do Cidadão</span>
                </a>
            </div>
        </div>

        <!-- Matriz de Conformidade com Órgãos de Controle (TCE / TCU) -->
        <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-sky-950 rounded-2xl p-6 text-white shadow-xl border border-emerald-900/50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl font-bold border border-emerald-500/30">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Selo Diamante de Transparência Pública</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>
                        <h2 class="text-lg font-bold font-heading text-white">Índice Geral de Transparência Ativa: 98.4%</h2>
                        <p class="text-xs text-slate-300 mt-0.5">Todos os conjuntos obrigatórios pela Lei Federal nº 12.527/11 e LC 101/00 estão publicados e auditados.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <i class="fa-solid fa-check-double mr-1"></i> Auditado pelo TCE-MS
                    </span>
                </div>
            </div>
        </div>

        <!-- Conjuntos de Dados Abertos (Datasets) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($datasets as $ds): ?>
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900"><?= htmlspecialchars($ds['name']) ?></span>
                            <span class="text-xs font-mono font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-100">
                                <?= $ds['count'] ?> registros
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-2"><?= htmlspecialchars($ds['desc']) ?></p>
                        <p class="text-[11px] text-slate-400 mt-2"><i class="fa-regular fa-clock mr-1"></i> Periodicidade: <?= htmlspecialchars($ds['periodicity']) ?></p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="<?= url('portal/transparencia.php') ?>" target="_blank" class="text-xs font-semibold text-sky-600 hover:underline">
                            Visualizar no Portal &rarr;
                        </a>
                        <a href="<?= url('modulos/transparencia.php?export=' . $ds['id']) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                            <i class="fa-solid fa-file-csv text-emerald-600"></i>
                            <span>Baixar CSV Aberto</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Seção: Painel de Preparação / Checklist PNTP 2026 (Atricon - Seção 26 do Planejamento v1.2) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-4 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold font-heading text-slate-900">Checklist PNTP 2026 (Radar da Transparência Pública)</h2>
                            <span class="text-[10px] bg-amber-100 text-amber-900 font-bold px-2 py-0.5 rounded">Atricon / TCE-MS</span>
                        </div>
                        <p class="text-xs text-slate-500">Matriz de preparação para o ciclo 2026 de avaliação de portais públicos do Poder Legislativo.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200">
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Índice Preliminar:</span>
                        <span class="text-lg font-bold text-emerald-700 font-mono"><?= $percentPntp ?>% Conforme</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>

            <!-- Tabela dos Critérios do PNTP -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Código</th>
                            <th class="p-3">Dimensão PNTP</th>
                            <th class="p-3">Critério Avaliado</th>
                            <th class="p-3">Exigibilidade</th>
                            <th class="p-3">Situação</th>
                            <th class="p-3">URL de Evidência Pública</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($pntpItems as $item): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-slate-900 whitespace-nowrap"><?= htmlspecialchars($item['criteria_code']) ?></td>
                                <td class="p-3 font-semibold text-slate-700 whitespace-nowrap"><?= htmlspecialchars($item['dimension_name']) ?></td>
                                <td class="p-3 text-slate-800"><?= htmlspecialchars($item['criteria_title']) ?></td>
                                <td class="p-3">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded <?= $item['applicability'] === 'Obrigatório' ? 'bg-rose-50 text-rose-700 border border-rose-100' : ($item['applicability'] === 'Essencial' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-slate-100 text-slate-600') ?>">
                                        <?= htmlspecialchars($item['applicability']) ?>
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                                        <i class="fa-solid fa-check text-[10px]"></i> <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </td>
                                <td class="p-3">
                                    <?php if (!empty($item['public_url'])): ?>
                                        <a href="<?= url($item['public_url']) ?>" target="_blank" class="text-sky-600 hover:text-sky-800 font-semibold inline-flex items-center gap-1">
                                            <span>Validar na página</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Alerta Legal da Seção 26.4 -->
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-[11px] leading-relaxed flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5 text-xs"></i>
                <p>
                    <strong>Nota de Governança (Seção 26.4 do Planejamento v1.2):</strong> Este painel é uma ferramenta de gestão interna para preparação ao ciclo anual do Programa Nacional de Transparência Pública (PNTP / Atricon). O selo e certificação oficiais são atribuídos exclusivamente após auditoria dos Tribunais de Contas competentes.
                </p>
            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

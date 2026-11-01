<?php
/**
 * Câmara Digital - Módulo 11: Controle Interno, Riscos e Conformidade
 * Conforme Seção 4.11 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Controle Interno e Conformidade';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Processa Novo Achado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_finding'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $title = trim($_POST['title'] ?? '');
        $cat = trim($_POST['category'] ?? 'Operacional');
        $level = trim($_POST['risk_level'] ?? 'Médio');
        $desc = trim($_POST['finding_description'] ?? '');
        $rec = trim($_POST['recommendation'] ?? '');
        $action = trim($_POST['action_plan'] ?? '');
        $resp = trim($_POST['responsible_name'] ?? '');
        $deadline = $_POST['deadline'] ?? date('Y-m-d', strtotime('+30 days'));

        if (!empty($title) && !empty($desc)) {
            $stmt = $db->prepare("
                INSERT INTO internal_controls (tenant_id, title, category, risk_level, finding_description, recommendation, action_plan, responsible_name, deadline, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Em Andamento', NOW())
            ");
            $stmt->execute([$tenantId, $title, $cat, $level, $desc, $rec, $action, $resp, $deadline]);
            Audit::log('CRIAR_ACHADO_AUDITORIA', 'internal_controls', (int)$db->lastInsertId(), "Achado de controle interno cadastrado: {$title}");
            setFlash('success', "Achado e Plano de Ação cadastrados com sucesso!");
            header("Location: " . url('modulos/controle_interno.php'));
            exit;
        }
    }
}

$controls = $db->query("SELECT * FROM internal_controls WHERE tenant_id = {$tenantId} ORDER BY deadline ASC")->fetchAll();
$audits = $db->query("SELECT * FROM audit_logs WHERE tenant_id = {$tenantId} ORDER BY id DESC LIMIT 50")->fetchAll();

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 11</span>
                    <span>•</span>
                    <span>Governança e Integridade</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Controle Interno, Matriz de Riscos & Auditoria</h1>
                <p class="text-xs sm:text-sm text-slate-500">Mapeamento de riscos, achados de fiscalização, planos de ação corretiva e checklist de conformidade com os Tribunais de Contas.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('new-finding-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Cadastrar Apontamento / Achado</span>
                </button>
            </div>
        </div>

        <!-- Seção 1: Checklist de Conformidade Regulatória Obrigatória -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2 mb-4">
                <i class="fa-solid fa-list-check text-indigo-600"></i>
                <span>Checklist de Obrigações Legais e Remessas Oficiais</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-emerald-900">PNCP (Lei 14.133/21)</span>
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    </div>
                    <p class="text-[11px] text-emerald-800 mt-1">Editais e contratos publicados e sincronizados no Portal Nacional.</p>
                    <span class="mt-2 block text-[10px] text-emerald-700 font-semibold">100% de Conformidade</span>
                </div>

                <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-emerald-900">LAI (Lei 12.527/11)</span>
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    </div>
                    <p class="text-[11px] text-emerald-800 mt-1">Ouvidoria e SIC operando dentro do prazo legal máximo de 20 dias.</p>
                    <span class="mt-2 block text-[10px] text-emerald-700 font-semibold">Sem pedidos em atraso</span>
                </div>

                <div class="p-4 rounded-xl border border-sky-200 bg-sky-50/50">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sky-900">TCE-MS / e-Sfinge</span>
                        <i class="fa-solid fa-circle-notch text-sky-600"></i>
                    </div>
                    <p class="text-[11px] text-sky-800 mt-1">Remessa mensal de balancetes e atos de pessoal em conformidade.</p>
                    <span class="mt-2 block text-[10px] text-sky-700 font-semibold">Exercício 2026 Vigente</span>
                </div>
            </div>
        </div>

        <!-- Seção 2: Matriz de Riscos e Achados -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-amber-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Achados de Auditoria Interna e Planos de Ação</h2>
                </div>
                <span class="text-xs text-slate-500"><?= count($controls) ?> registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Categoria</th>
                            <th class="p-3">Nível de Risco</th>
                            <th class="p-3">Apontamento / Título</th>
                            <th class="p-3">Recomendação do Controle Interno</th>
                            <th class="p-3">Plano de Ação Corretiva</th>
                            <th class="p-3">Responsável</th>
                            <th class="p-3">Prazo</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($controls as $c): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-semibold text-slate-700 whitespace-nowrap"><?= htmlspecialchars($c['category']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $c['risk_level'] === 'Alto' ? 'bg-rose-100 text-rose-800' : ($c['risk_level'] === 'Médio' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') ?>">
                                        <?= htmlspecialchars($c['risk_level']) ?>
                                    </span>
                                </td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($c['title']) ?></p>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5"><?= htmlspecialchars($c['finding_description']) ?></p>
                                </td>
                                <td class="p-3 text-slate-700 line-clamp-2"><?= htmlspecialchars($c['recommendation']) ?></td>
                                <td class="p-3 text-indigo-900 font-medium line-clamp-2"><?= htmlspecialchars($c['action_plan']) ?></td>
                                <td class="p-3 text-slate-600 whitespace-nowrap"><?= htmlspecialchars($c['responsible_name']) ?></td>
                                <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= formatDate($c['deadline']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $c['status'] === 'Concluído' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' ?>">
                                        <?= htmlspecialchars($c['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção 3: Trilha de Auditoria Global (Audit Trail) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-slate-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Trilha Global de Auditoria de Acessos e Operações</h2>
                </div>
                <span class="text-xs text-slate-500">Últimos 50 eventos registrados</span>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200 sticky top-0">
                        <tr>
                            <th class="p-3">Data/Hora</th>
                            <th class="p-3">Usuário</th>
                            <th class="p-3">Operação</th>
                            <th class="p-3">Entidade</th>
                            <th class="p-3">Detalhes</th>
                            <th class="p-3">IP de Origem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($audits as $a): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono text-[11px] text-slate-500 whitespace-nowrap"><?= formatDate($a['created_at'], true) ?></td>
                                <td class="p-3 font-semibold text-slate-800 whitespace-nowrap"><?= htmlspecialchars($a['user_name']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                        <?= htmlspecialchars($a['action']) ?>
                                    </span>
                                </td>
                                <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= htmlspecialchars($a['entity']) ?></td>
                                <td class="p-3 text-slate-700"><?= htmlspecialchars($a['details']) ?></td>
                                <td class="p-3 font-mono text-slate-400 text-[11px] whitespace-nowrap"><?= htmlspecialchars($a['ip_address']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Novo Achado -->
<div id="new-finding-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Registrar Apontamento de Auditoria Interna</h3>
            <button onclick="closeModal('new-finding-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/controle_interno.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_finding" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Categoria do Processo</label>
                    <select name="category" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Legal/Regulamentar">Legal / Regulamentar</option>
                        <option value="Operacional">Operacional</option>
                        <option value="Financeiro / Orçamentário">Financeiro / Orçamentário</option>
                        <option value="TI e Segurança da Informação">TI e Segurança da Informação</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Nível de Risco Identificado</label>
                    <select name="risk_level" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Baixo">Baixo</option>
                        <option value="Médio" selected>Médio</option>
                        <option value="Alto">Alto</option>
                        <option value="Crítico">Crítico</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Título do Achado / Não Conformidade</label>
                <input type="text" name="title" required placeholder="Ex: Regularização dos Prazos de Publicação no Diário"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Descrição do Fato Auditado</label>
                <textarea name="finding_description" rows="2" required placeholder="Constatou-se durante a verificação amostral..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Recomendação do Controle Interno</label>
                <textarea name="recommendation" rows="2" required placeholder="Recomenda-se adotar rotina de checklist prévio..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Plano de Ação Corretiva</label>
                <textarea name="action_plan" rows="2" required placeholder="Implementar validação antes da assinatura da despesa..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Responsável pelo Cumprimento</label>
                    <input type="text" name="responsible_name" required placeholder="Nome do Responsável / Setor"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Prazo Limite para Regularização</label>
                    <input type="date" name="deadline" required value="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-finding-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Apontamento
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

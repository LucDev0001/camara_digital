<?php
/**
 * Câmara Digital - Módulo 5: Compras, Licitações e Contratos
 * Conforme Seção 4.5 do Planejamento Mestre e Lei Federal nº 14.133/2021
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Compras, Licitações e Contratos';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Processa Novo Processo Licitatório
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_procurement'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $processNum = trim($_POST['process_number'] ?? '');
        $modality = trim($_POST['modality'] ?? 'Pregão Eletrônico (Lei 14.133)');
        $object = trim($_POST['object_description'] ?? '');
        $estVal = (float)str_replace(['.', ','], ['', '.'], $_POST['estimated_value'] ?? '0');
        $openDate = $_POST['opening_date'] ?? date('Y-m-d');
        $year = (int)date('Y');

        if (!empty($processNum) && !empty($object)) {
            $stmt = $db->prepare("
                INSERT INTO procurements (tenant_id, process_number, year, modality, object_description, estimated_value, status, opening_date, pncp_published, pncp_link, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'Em Andamento', ?, 1, 'https://pncp.gov.br', NOW())
            ");
            $stmt->execute([$tenantId, $processNum, $year, $modality, $object, $estVal, $openDate]);
            Audit::log('CRIAR_LICITACAO', 'procurements', (int)$db->lastInsertId(), "Processo licitatório {$processNum} cadastrado na modalidade {$modality}");
            setFlash('success', "Processo licitatório {$processNum} cadastrado com sucesso!");
            header("Location: " . url('modulos/compras.php'));
            exit;
        }
    }
}

// Processa Novo Contrato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_contract'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $contractNum = trim($_POST['contract_number'] ?? '');
        $supplierId = (int)($_POST['supplier_id'] ?? 0);
        $summary = trim($_POST['object_summary'] ?? '');
        $totalVal = (float)str_replace(['.', ','], ['', '.'], $_POST['total_value'] ?? '0');
        $startDate = $_POST['start_date'] ?? date('Y-m-d');
        $endDate = $_POST['end_date'] ?? date('Y-m-d', strtotime('+1 year'));
        $manager = trim($_POST['manager_name'] ?? '');
        $inspector = trim($_POST['inspector_name'] ?? '');
        $year = (int)date('Y');

        if (!empty($contractNum) && $supplierId) {
            $stmt = $db->prepare("
                INSERT INTO contracts (tenant_id, supplier_id, contract_number, year, object_summary, total_value, start_date, end_date, manager_name, inspector_name, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Vigente', NOW())
            ");
            $stmt->execute([$tenantId, $supplierId, $contractNum, $year, $summary, $totalVal, $startDate, $endDate, $manager, $inspector]);
            Audit::log('CRIAR_CONTRATO', 'contracts', (int)$db->lastInsertId(), "Contrato {$contractNum}/{$year} cadastrado no valor de R$ {$totalVal}");
            setFlash('success', "Contrato administrativo nº {$contractNum}/{$year} cadastrado com sucesso!");
            header("Location: " . url('modulos/compras.php#contracts'));
            exit;
        }
    }
}

$procurements = $db->query("SELECT * FROM procurements WHERE tenant_id = {$tenantId} ORDER BY year DESC, id DESC")->fetchAll();
$contracts = $db->query("
    SELECT c.*, s.corporate_name, s.cnpj_cpf 
    FROM contracts c 
    INNER JOIN suppliers s ON c.supplier_id = s.id 
    WHERE c.tenant_id = {$tenantId} 
    ORDER BY c.end_date ASC
")->fetchAll();
$suppliers = $db->query("SELECT * FROM suppliers WHERE tenant_id = {$tenantId} ORDER BY corporate_name ASC")->fetchAll();

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 5</span>
                    <span>•</span>
                    <span>Contratações Públicas</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Compras, Licitações & Gestão de Contratos</h1>
                <p class="text-xs sm:text-sm text-slate-500">Fluxos preparatórios, editais da Lei 14.133/21, sincronização PNCP e fiscalização contratual.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('new-procurement-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-file-circle-plus"></i>
                    <span>Nova Licitação / Dispensa</span>
                </button>
                <button onclick="openModal('new-contract-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-signature"></i>
                    <span>Cadastrar Contrato</span>
                </button>
            </div>
        </div>

        <!-- Seção 1: Processos Licitatórios -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-scale-unbalanced-flip text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Processos de Contratação e Licitações (Lei 14.133)</h2>
                </div>
                <span class="text-xs text-slate-500"><?= count($procurements) ?> processos cadastrados</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Processo</th>
                            <th class="p-3">Modalidade</th>
                            <th class="p-3">Objeto da Contratação</th>
                            <th class="p-3">Valor Estimado</th>
                            <th class="p-3">Abertura</th>
                            <th class="p-3">PNCP</th>
                            <th class="p-3">Situação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($procurements as $p): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700 whitespace-nowrap"><?= htmlspecialchars($p['process_number']) ?></td>
                                <td class="p-3 font-medium text-slate-700 whitespace-nowrap"><?= htmlspecialchars($p['modality']) ?></td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-900 line-clamp-1"><?= htmlspecialchars($p['object_description']) ?></p>
                                </td>
                                <td class="p-3 font-bold font-mono text-slate-900 whitespace-nowrap"><?= formatCurrency($p['estimated_value']) ?></td>
                                <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= formatDate($p['opening_date']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($p['pncp_link']) ?>" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-sky-600 hover:underline">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> Publicado
                                    </a>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $p['status'] === 'Homologado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= htmlspecialchars($p['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção 2: Gestão de Contratos e Alertas de Vigência -->
        <div id="contracts" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-emerald-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Contratos Administrativos Vigentes & Fiscais</h2>
                </div>
                <span class="text-xs text-slate-500"><?= count($contracts) ?> contratos</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Contrato nº</th>
                            <th class="p-3">Fornecedor Contratado</th>
                            <th class="p-3">Objeto Contratual</th>
                            <th class="p-3">Valor Global</th>
                            <th class="p-3">Vigência</th>
                            <th class="p-3">Fiscal do Contrato</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($contracts as $c): ?>
                            <?php 
                                $daysLeft = (strtotime($c['end_date']) - time()) / 86400;
                                $isExpiring = $daysLeft <= 60 && $daysLeft > 0;
                            ?>
                            <tr class="hover:bg-slate-50 <?= $isExpiring ? 'bg-amber-50/50' : '' ?>">
                                <td class="p-3 font-mono font-bold text-sky-700 whitespace-nowrap"><?= htmlspecialchars($c['contract_number']) ?>/<?= $c['year'] ?></td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($c['corporate_name']) ?></p>
                                    <p class="text-[11px] text-slate-400 font-mono">CNPJ: <?= formatCnpj($c['cnpj_cpf']) ?></p>
                                </td>
                                <td class="p-3">
                                    <p class="text-slate-800 line-clamp-1"><?= htmlspecialchars($c['object_summary']) ?></p>
                                </td>
                                <td class="p-3 font-bold font-mono text-emerald-700 whitespace-nowrap"><?= formatCurrency($c['total_value']) ?></td>
                                <td class="p-3 font-mono text-slate-600 whitespace-nowrap">
                                    <?= formatDate($c['start_date']) ?> até <strong><?= formatDate($c['end_date']) ?></strong>
                                    <?php if ($isExpiring): ?>
                                        <span class="block text-[10px] font-bold text-amber-700"><i class="fa-solid fa-clock mr-0.5"></i> Vence em <?= ceil($daysLeft) ?> dias!</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-slate-700 whitespace-nowrap">
                                    <p class="font-semibold"><?= htmlspecialchars($c['inspector_name']) ?></p>
                                    <p class="text-[10px] text-slate-400">Gestor: <?= htmlspecialchars($c['manager_name']) ?></p>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <?= htmlspecialchars($c['status']) ?>
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

<!-- Modal: Novo Processo -->
<div id="new-procurement-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Autuar Novo Processo Licitatório</h3>
            <button onclick="closeModal('new-procurement-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/compras.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_procurement" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Número do Processo</label>
                    <input type="text" name="process_number" required placeholder="Ex: PE-015/2026"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Modalidade Legal</label>
                    <select name="modality" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Pregão Eletrônico (Lei 14.133)">Pregão Eletrônico (Lei 14.133)</option>
                        <option value="Dispensa de Licitação (Art. 75, II)">Dispensa de Licitação (Art. 75, II)</option>
                        <option value="Inexigibilidade de Licitação (Art. 74)">Inexigibilidade de Licitação (Art. 74)</option>
                        <option value="Concorrência">Concorrência Pública</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Objeto da Contratação / Descrição</label>
                <textarea name="object_description" rows="3" required placeholder="Contratação de empresa para..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Valor Estimado Global (R$)</label>
                    <input type="text" name="estimated_value" required placeholder="75.000,00"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Data de Abertura / Sessão</label>
                    <input type="date" name="opening_date" required value="<?= date('Y-m-d', strtotime('+15 days')) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-procurement-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg shadow-sm">
                    Autuar Processo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Novo Contrato -->
<div id="new-contract-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Cadastrar Contrato Administrativo</h3>
            <button onclick="closeModal('new-contract-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/compras.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_contract" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Número do Contrato</label>
                    <input type="text" name="contract_number" required placeholder="Ex: CONT-015"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Fornecedor Contratado</label>
                    <select name="supplier_id" required class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['corporate_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Objeto do Contrato</label>
                <textarea name="object_summary" rows="2" required placeholder="Prestação de serviços de..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-medium text-slate-700">Valor Global (R$)</label>
                    <input type="text" name="total_value" required placeholder="120.000,00"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Início da Vigência</label>
                    <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Término da Vigência</label>
                    <input type="date" name="end_date" required value="<?= date('Y-m-d', strtotime('+1 year')) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Gestor do Contrato</label>
                    <input type="text" name="manager_name" required placeholder="Nome do Gestor"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Fiscal do Contrato</label>
                    <input type="text" name="inspector_name" required placeholder="Nome do Fiscal Designado"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-contract-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Contrato
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

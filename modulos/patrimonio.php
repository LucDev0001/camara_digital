<?php
/**
 * Câmara Digital - Módulo 6: Patrimônio, Frota e Manutenção
 * Conforme Seção 4.6 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Patrimônio e Frota Oficial';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Processa Novo Bem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_asset'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $tag = trim($_POST['asset_tag'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $cat = trim($_POST['category'] ?? 'Informática e Servidores');
        $brand = trim($_POST['brand'] ?? '');
        $serial = trim($_POST['serial_number'] ?? '');
        $acqDate = $_POST['acquisition_date'] ?? date('Y-m-d');
        $acqVal = (float)str_replace(['.', ','], ['', '.'], $_POST['acquisition_value'] ?? '0');
        $curVal = (float)str_replace(['.', ','], ['', '.'], $_POST['current_value'] ?? $_POST['acquisition_value'] ?? '0');
        $location = trim($_POST['location'] ?? 'Sede da Câmara');
        $responsible = trim($_POST['responsible_name'] ?? '');
        $condition = trim($_POST['condition_status'] ?? 'Bom');

        if (!empty($tag) && !empty($desc)) {
            $stmt = $db->prepare("
                INSERT INTO assets (tenant_id, asset_tag, description, category, brand, serial_number, acquisition_date, acquisition_value, current_value, location, responsible_name, condition_status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$tenantId, $tag, $desc, $cat, $brand, $serial, $acqDate, $acqVal, $curVal, $location, $responsible, $condition]);
            Audit::log('TOMBAMENTO_BEM', 'assets', (int)$db->lastInsertId(), "Bem tombado sob o registro patrimonial {$tag}: {$desc}");
            setFlash('success', "Bem tombado com sucesso sob o registro {$tag}!");
            header("Location: " . url('modulos/patrimonio.php'));
            exit;
        }
    }
}

// Consultas
$assets = $db->query("SELECT * FROM assets WHERE tenant_id = {$tenantId} ORDER BY asset_tag ASC")->fetchAll();
$vehicles = $db->query("
    SELECT v.*, a.asset_tag, a.description as asset_desc 
    FROM vehicles v 
    LEFT JOIN assets a ON v.asset_id = a.id 
    WHERE v.tenant_id = {$tenantId} 
    ORDER BY v.plate ASC
")->fetchAll();

$totalAssetValue = array_sum(array_column($assets, 'current_value'));

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 6</span>
                    <span>•</span>
                    <span>Gestão Patrimonial</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Patrimônio, Bens Tombados & Frota</h1>
                <p class="text-xs sm:text-sm text-slate-500">Tombamento de bens permanentes, termos de responsabilidade e guarda, veículos e ordens de serviço.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('new-asset-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-barcode"></i>
                    <span>Tombar Novo Bem</span>
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Acervo Tombado</span>
                <p class="text-2xl font-bold text-slate-900 mt-2"><?= count($assets) ?> bens</p>
                <span class="text-[11px] text-slate-500 mt-1 block">Inventário ativo regular</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Valor Contábil Consolidado</span>
                <p class="text-2xl font-bold text-emerald-600 font-mono mt-2"><?= formatCurrency($totalAssetValue) ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Depreciação auditada</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Frota Oficial</span>
                <p class="text-2xl font-bold text-sky-600 mt-2"><?= count($vehicles) ?> veículos</p>
                <span class="text-[11px] text-slate-500 mt-1 block">Licenciamento e manutenção em dia</span>
            </div>
        </div>

        <!-- Seção 1: Inventário de Bens Tombados -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-tag text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Bens Patrimoniais Permanentes</h2>
                </div>
                <div class="w-full sm:w-64">
                    <input type="text" id="asset-filter" placeholder="Buscar por tombamento, descrição ou local..."
                           onkeyup="filterTable('asset-filter', 'asset-table')"
                           class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600" id="asset-table">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Tombamento</th>
                            <th class="p-3">Descrição do Bem</th>
                            <th class="p-3">Categoria</th>
                            <th class="p-3">Localização</th>
                            <th class="p-3">Responsável</th>
                            <th class="p-3">Valor Contábil</th>
                            <th class="p-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($assets as $a): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700 whitespace-nowrap"><?= htmlspecialchars($a['asset_tag']) ?></td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($a['description']) ?></p>
                                    <p class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($a['brand'] ?: '') ?> <?= $a['serial_number'] ? '• S/N: ' . htmlspecialchars($a['serial_number']) : '' ?></p>
                                </td>
                                <td class="p-3 text-slate-600"><?= htmlspecialchars($a['category']) ?></td>
                                <td class="p-3 text-slate-700 font-medium"><?= htmlspecialchars($a['location']) ?></td>
                                <td class="p-3 text-slate-600"><?= htmlspecialchars($a['responsible_name'] ?: '-') ?></td>
                                <td class="p-3 font-mono font-bold text-slate-900"><?= formatCurrency($a['current_value']) ?></td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                        <?= htmlspecialchars($a['condition_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção 2: Frota Oficial -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2 mb-4">
                <i class="fa-solid fa-car-side text-sky-600"></i>
                <span>Controle da Frota Oficial de Veículos</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($vehicles as $v): ?>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-bold bg-slate-900 text-white px-2 py-0.5 rounded"><?= htmlspecialchars($v['plate']) ?></span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800"><?= htmlspecialchars($v['status']) ?></span>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 mt-1"><?= htmlspecialchars($v['model']) ?> (Ano <?= $v['year'] ?>)</h3>
                            <div class="mt-2 text-[11px] text-slate-600 space-y-1">
                                <p><span class="text-slate-400">Condutor Designado:</span> <strong><?= htmlspecialchars($v['driver_name']) ?></strong></p>
                                <p><span class="text-slate-400">Odômetro:</span> <span class="font-mono font-bold"><?= number_format($v['mileage'], 0, ',', '.') ?> km</span></p>
                                <p><span class="text-slate-400">Última Revisão:</span> <?= formatDate($v['last_maintenance']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Novo Bem -->
<div id="new-asset-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Tombar Novo Bem Permanente</h3>
            <button onclick="closeModal('new-asset-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/patrimonio.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_asset" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Plaqueta / Número Patrimonial</label>
                    <input type="text" name="asset_tag" required placeholder="Ex: PAT-2026-050"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Categoria</label>
                    <select name="category" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Informática e Servidores">Informática e Servidores</option>
                        <option value="Móveis e Utensílios">Móveis e Utensílios</option>
                        <option value="Equipamentos Audiovisuais">Equipamentos Audiovisuais</option>
                        <option value="Veículos e Frota">Veículos e Frota</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Descrição do Bem</label>
                <input type="text" name="description" required placeholder="Ex: Notebook Dell Latitude 16GB"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Marca / Fabricante</label>
                    <input type="text" name="brand" placeholder="Ex: Dell"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Número de Série</label>
                    <input type="text" name="serial_number" placeholder="Ex: 8F2A900"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Localização Física</label>
                    <input type="text" name="location" required placeholder="Ex: Plenário / Sala das Comissões"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Servidor Responsável (Guarda)</label>
                    <input type="text" name="responsible_name" required placeholder="Nome do Responsável"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Valor de Aquisição (R$)</label>
                    <input type="text" name="acquisition_value" required placeholder="5.400,00"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Data de Aquisição</label>
                    <input type="date" name="acquisition_date" required value="<?= date('Y-m-d') ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-asset-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Tombamento
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

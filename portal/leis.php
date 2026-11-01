<?php
/**
 * Câmara Digital - Consulta Pública do Acervo de Leis e Normas Municipais
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Leis e Legislação Municipal';
$tenantId = Tenant::id();
$db = Database::getConnection();

$q = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$year = (int)($_GET['year'] ?? 0);
$selectedId = (int)($_GET['id'] ?? 0);

$querySql = "SELECT * FROM legal_norms WHERE tenant_id = ?";
$params = [$tenantId];

if (!empty($q)) {
    $querySql .= " AND (title LIKE ? OR summary LIKE ? OR full_text LIKE ?)";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
}
if (!empty($type)) {
    $querySql .= " AND norm_type = ?";
    $params[] = $type;
}
if ($year > 0) {
    $querySql .= " AND year = ?";
    $params[] = $year;
}

$querySql .= " ORDER BY year DESC, number DESC";
$stmt = $db->prepare($querySql);
$stmt->execute($params);
$norms = $stmt->fetchAll();

// Se um ID específico foi selecionado
$selectedNorm = null;
if ($selectedId) {
    $stmtSel = $db->prepare("SELECT * FROM legal_norms WHERE id = ? AND tenant_id = ?");
    $stmtSel->execute([$selectedId, $tenantId]);
    $selectedNorm = $stmtSel->fetch();
}

$types = $db->query("SELECT DISTINCT norm_type FROM legal_norms WHERE tenant_id = {$tenantId} ORDER BY norm_type ASC")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-slate-900">Acervo de Legislação Municipal</h1>
            <p class="text-xs sm:text-sm text-slate-500">Consulte leis ordinárias, leis complementares, decretos legislativos e resoluções municipais.</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 bg-sky-50 text-sky-700 rounded-lg border border-sky-200">
            <?= count($norms) ?> normas encontradas
        </span>
    </div>

    <!-- Filtros de Pesquisa -->
    <form action="<?= url('portal/leis.php') ?>" method="GET" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
        <div class="sm:col-span-2">
            <label class="block font-medium text-slate-700">Palavra-chave ou assunto</label>
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Ex: calçadas, IPTU, energia solar..."
                   class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block font-medium text-slate-700">Tipo de Norma</label>
            <select name="type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">Todos os tipos</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>" <?= $type === $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="w-full py-2 px-4 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm transition-colors">
                Filtrar Acervo
            </button>
            <?php if ($q || $type || $year): ?>
                <a href="<?= url('portal/leis.php') ?>" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-lg transition-colors">
                    Limpar
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Visualização de Norma Selecionada (se houver ID) -->
    <?php if ($selectedNorm): ?>
        <div class="bg-white rounded-2xl border border-sky-300 p-6 shadow-md space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-sky-700 uppercase tracking-wider bg-sky-50 px-2.5 py-1 rounded border border-sky-200">
                        <?= htmlspecialchars($selectedNorm['norm_type']) ?> nº <?= $selectedNorm['number'] ?>/<?= $selectedNorm['year'] ?>
                    </span>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-800">
                        <?= htmlspecialchars($selectedNorm['status']) ?>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Abas de Leitura: Texto Oficial vs Linguagem Simples (Lei 15.263/2025) -->
                    <div class="inline-flex rounded-lg bg-slate-100 p-1 text-xs font-semibold">
                        <button type="button" onclick="switchLawView('official')" id="btn-tab-official" class="px-3 py-1 rounded-md bg-white text-slate-800 shadow-sm transition-all">
                            <i class="fa-solid fa-scroll mr-1"></i> Texto Oficial
                        </button>
                        <button type="button" onclick="switchLawView('plain')" id="btn-tab-plain" class="px-3 py-1 rounded-md text-slate-600 hover:text-sky-700 transition-all flex items-center gap-1">
                            <i class="fa-solid fa-feather text-amber-500"></i> Linguagem Simples <span class="text-[9px] bg-amber-100 text-amber-800 px-1 rounded font-bold">Lei 15.263</span>
                        </button>
                    </div>
                </div>
            </div>

            <h2 class="text-lg font-bold font-heading text-slate-900"><?= htmlspecialchars($selectedNorm['title']) ?></h2>
            
            <div class="p-3 bg-slate-50 border-l-4 border-sky-500 text-xs text-slate-700">
                <strong>Ementa Oficial:</strong> <?= htmlspecialchars($selectedNorm['summary']) ?>
            </div>

            <!-- View 1: Texto Jurídico Oficial -->
            <div id="view-official-text" class="text-xs text-slate-800 whitespace-pre-wrap font-serif leading-relaxed p-4 bg-slate-50/50 rounded-xl border border-slate-200">
<?= htmlspecialchars($selectedNorm['full_text']) ?>
            </div>

            <!-- View 2: Linguagem Simples Cidadã (Lei nº 15.263/2025) -->
            <div id="view-plain-text" class="hidden space-y-3">
                <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-950 text-xs space-y-2">
                    <div class="flex items-center gap-2 font-bold text-amber-900">
                        <i class="fa-solid fa-sparkles text-amber-600"></i>
                        <span>Entenda esta Norma em Linguagem Simples (Conforme a Lei Federal nº 15.263/2025)</span>
                    </div>
                    <div class="text-xs text-slate-800 whitespace-pre-wrap leading-relaxed font-sans bg-white p-4 rounded-lg border border-amber-200">
<?= htmlspecialchars($selectedNorm['plain_language_summary'] ?? 'Resumo explicativo em preparação pela assessoria legislativa.') ?>
                    </div>
                    <p class="text-[10px] text-amber-800 italic pt-1 border-t border-amber-200/60">
                        * Nota de transparência: Esta explicação em linguagem direta é um recurso auxiliar de acessibilidade comunicacional. O texto jurídico oficial acima é a única fonte com eficácia normativa.
                    </p>
                </div>
            </div>

            <!-- Comprovação de Publicação Oficial (Seção 22.2 & 27.5 do Planejamento v1.2) -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-0.5">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Veículo Oficial de Publicação:</span>
                    <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-newspaper text-slate-400"></i>
                        <?= htmlspecialchars($selectedNorm['official_publication_vehicle'] ?? 'Diário Oficial dos Municípios (DOM)') ?>
                        <span class="text-slate-400 font-normal">• <?= htmlspecialchars($selectedNorm['official_publication_edition'] ?? 'Edição Ordinária') ?></span>
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <?php if (!empty($selectedNorm['official_publication_url'])): ?>
                    <a href="<?= htmlspecialchars($selectedNorm['official_publication_url']) ?>" target="_blank" class="text-sky-700 hover:text-sky-900 font-bold text-[11px] inline-flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Ver no Diário Oficial
                    </a>
                    <?php endif; ?>
                    <button onclick="window.print()" class="text-slate-600 hover:text-slate-900 font-semibold text-[11px] flex items-center gap-1">
                        <i class="fa-solid fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>

        <script>
        function switchLawView(view) {
            const officialView = document.getElementById('view-official-text');
            const plainView = document.getElementById('view-plain-text');
            const btnOfficial = document.getElementById('btn-tab-official');
            const btnPlain = document.getElementById('btn-tab-plain');

            if (view === 'plain') {
                officialView.classList.add('hidden');
                plainView.classList.remove('hidden');
                btnPlain.classList.add('bg-white', 'text-amber-800', 'shadow-sm');
                btnPlain.classList.remove('text-slate-600');
                btnOfficial.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
                btnOfficial.classList.add('text-slate-600');
            } else {
                plainView.classList.add('hidden');
                officialView.classList.remove('hidden');
                btnOfficial.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
                btnOfficial.classList.remove('text-slate-600');
                btnPlain.classList.remove('bg-white', 'text-amber-800', 'shadow-sm');
                btnPlain.classList.add('text-slate-600');
            }
        }
        </script>
    <?php endif; ?>

    <!-- Lista de Normas -->
    <div class="space-y-3">
        <?php foreach ($norms as $norm): ?>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-sky-300 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs text-sky-800 font-mono bg-sky-50 px-2 py-0.5 rounded border border-sky-100">
                            <?= htmlspecialchars($norm['norm_type']) ?> nº <?= $norm['number'] ?>/<?= $norm['year'] ?>
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                            <?= htmlspecialchars($norm['status']) ?>
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-400">Promulgada em: <?= formatDate($norm['promulgation_date']) ?></span>
                </div>
                <h3 class="font-bold text-sm text-slate-900 mt-2"><?= htmlspecialchars($norm['title']) ?></h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= htmlspecialchars($norm['summary']) ?></p>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px]">Ano de Vigência: <?= $norm['year'] ?></span>
                    <a href="<?= url('portal/leis.php?id=' . $norm['id']) ?>" class="text-sky-600 font-semibold hover:underline flex items-center gap-1">
                        <span>Acessar Texto Integral</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

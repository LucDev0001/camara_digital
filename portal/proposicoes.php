<?php
/**
 * Câmara Digital - Acompanhamento Público de Proposições e Tramitações
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Acompanhamento de Proposições';
$tenantId = Tenant::id();
$db = Database::getConnection();

$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$selectedId = (int)($_GET['id'] ?? 0);

$sql = "SELECT * FROM legislative_matters WHERE tenant_id = ?";
$params = [$tenantId];

if (!empty($q)) {
    $sql .= " AND (title LIKE ? OR summary LIKE ? OR code LIKE ? OR author_name LIKE ?)";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
}
if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY year DESC, number DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$matters = $stmt->fetchAll();

$selectedMatter = null;
$movements = [];
if ($selectedId) {
    $stmtSel = $db->prepare("SELECT * FROM legislative_matters WHERE id = ? AND tenant_id = ?");
    $stmtSel->execute([$selectedId, $tenantId]);
    $selectedMatter = $stmtSel->fetch();

    if ($selectedMatter) {
        $stmtMov = $db->prepare("SELECT * FROM matter_movements WHERE matter_id = ? ORDER BY movement_date ASC");
        $stmtMov->execute([$selectedId]);
        $movements = $stmtMov->fetchAll();
    }
}

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-slate-900">Acompanhamento Legislativo</h1>
            <p class="text-xs sm:text-sm text-slate-500">Consulte o histórico de tramitação dos projetos de lei, requerimentos e indicações apresentados pelos vereadores.</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 bg-sky-50 text-sky-700 rounded-lg border border-sky-200">
            <?= count($matters) ?> matérias em andamento
        </span>
    </div>

    <!-- Filtros -->
    <form action="<?= url('portal/proposicoes.php') ?>" method="GET" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div class="sm:col-span-2">
            <label class="block font-medium text-slate-700">Buscar por código, autor ou assunto</label>
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Ex: PL-104/2026, energia solar, Dr. Carlos..."
                   class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="w-full py-2 px-4 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm transition-colors">
                Buscar Proposições
            </button>
            <?php if ($q || $status): ?>
                <a href="<?= url('portal/proposicoes.php') ?>" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-lg transition-colors">
                    Limpar
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Visualização Detalhada da Proposição & Linha do Tempo de Tramitação -->
    <?php if ($selectedMatter): ?>
        <div class="bg-white rounded-2xl border border-sky-300 p-6 shadow-md space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-sky-800 font-mono bg-sky-50 px-2.5 py-1 rounded border border-sky-100">
                        <?= htmlspecialchars($selectedMatter['code']) ?>
                    </span>
                    <span class="text-xs font-semibold text-slate-600"><?= htmlspecialchars($selectedMatter['type']) ?></span>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full <?= $selectedMatter['status'] === 'Aprovado' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' ?>">
                    Situação: <?= htmlspecialchars($selectedMatter['status']) ?>
                </span>
            </div>

            <div>
                <h2 class="text-lg font-bold font-heading text-slate-900"><?= htmlspecialchars($selectedMatter['title']) ?></h2>
                <p class="text-xs text-slate-600 mt-1"><?= htmlspecialchars($selectedMatter['summary']) ?></p>
                <div class="mt-3 flex flex-wrap gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
                    <span><i class="fa-solid fa-user-pen mr-1 text-sky-600"></i> Autor: <strong><?= htmlspecialchars($selectedMatter['author_name']) ?></strong></span>
                    <span><i class="fa-regular fa-calendar mr-1 text-sky-600"></i> Apresentado em: <strong><?= formatDate($selectedMatter['presentation_date']) ?></strong></span>
                    <span><i class="fa-solid fa-bolt mr-1 text-amber-600"></i> Regime: <strong><?= htmlspecialchars($selectedMatter['urgency']) ?></strong></span>
                </div>
            </div>

            <!-- Linguagem Simples (Lei Federal nº 15.263/2025 - Seção 28.4 do Planejamento v1.2) -->
            <?php if (!empty($selectedMatter['plain_language_summary'])): ?>
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-950 text-xs space-y-2">
                <div class="flex items-center gap-2 font-bold text-amber-900">
                    <i class="fa-solid fa-feather text-amber-600"></i>
                    <span>Entenda em Linguagem Simples (Política Nacional - Lei Federal nº 15.263/2025)</span>
                </div>
                <div class="text-xs text-slate-800 whitespace-pre-wrap leading-relaxed font-sans bg-white p-3.5 rounded-lg border border-amber-200">
<?= htmlspecialchars($selectedMatter['plain_language_summary']) ?>
                </div>
                <p class="text-[10px] text-amber-800 italic">
                    * Nota: Este resumo é um recurso auxiliar de acessibilidade e transparência. O texto original protocolado da proposição é a fonte oficial com validade jurídica.
                </p>
            </div>
            <?php endif; ?>

            <!-- Linha do Tempo da Tramitação -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-route text-sky-600"></i>
                    <span>Histórico de Tramitação Oficial (Fé Pública)</span>
                </h3>

                <div class="space-y-4 relative before:absolute before:inset-0 before:left-3.5 before:w-0.5 before:bg-slate-200">
                    <?php if (empty($movements)): ?>
                        <p class="text-xs text-slate-400 italic pl-8">Aguardando primeiras movimentações regimentais.</p>
                    <?php else: ?>
                        <?php foreach ($movements as $idx => $mov): ?>
                            <div class="relative flex items-start gap-3 pl-8">
                                <div class="absolute left-1.5 mt-1 w-4 h-4 rounded-full bg-sky-600 border-2 border-white shadow-sm"></div>
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex-1 space-y-1">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-sky-800"><?= htmlspecialchars($mov['to_dept']) ?></span>
                                        <span class="font-mono text-slate-400"><?= formatDate($mov['movement_date'], true) ?></span>
                                    </div>
                                    <p class="text-xs text-slate-700"><?= htmlspecialchars($mov['dispatch_text']) ?></p>
                                    <p class="text-[10px] text-slate-400">Despachado por: <?= htmlspecialchars($mov['user_name']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lista de Proposições -->
    <div class="space-y-3">
        <?php foreach ($matters as $m): ?>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-sky-300 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs text-sky-800 font-mono bg-sky-50 px-2 py-0.5 rounded border border-sky-100">
                            <?= htmlspecialchars($m['code']) ?>
                        </span>
                        <span class="text-xs text-slate-500"><?= htmlspecialchars($m['type']) ?></span>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded <?= $m['status'] === 'Aprovado' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' ?>">
                        <?= htmlspecialchars($m['status']) ?>
                    </span>
                </div>
                <h3 class="font-bold text-sm text-slate-900 mt-2"><?= htmlspecialchars($m['title']) ?></h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?= htmlspecialchars($m['summary']) ?></p>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500 text-[11px]">Autor: <strong><?= htmlspecialchars($m['author_name']) ?></strong></span>
                    <a href="<?= url('portal/proposicoes.php?id=' . $m['id']) ?>" class="text-sky-600 font-semibold hover:underline flex items-center gap-1">
                        <span>Ver Tramitação Completa</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

<?php
/**
 * Câmara Digital - Sessões Plenárias e Votações
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Sessões Plenárias e Votações';
$tenantId = Tenant::id();
$db = Database::getConnection();

$sessions = $db->query("
    SELECT s.*, 
        (SELECT COUNT(*) FROM session_agenda_items WHERE session_id = s.id) as total_items 
    FROM sessions s 
    WHERE s.tenant_id = {$tenantId} 
    ORDER BY s.session_date DESC, s.start_time DESC
")->fetchAll();

$selectedId = (int)($_GET['id'] ?? ($sessions[0]['id'] ?? 0));
$selectedSession = null;
$agendaItems = [];

if ($selectedId) {
    $stmt = $db->prepare("SELECT * FROM sessions WHERE id = ? AND tenant_id = ?");
    $stmt->execute([$selectedId, $tenantId]);
    $selectedSession = $stmt->fetch();

    if ($selectedSession) {
        $stmtItems = $db->prepare("
            SELECT sai.*, lm.code as matter_code, lm.title as matter_title 
            FROM session_agenda_items sai 
            LEFT JOIN legislative_matters lm ON sai.matter_id = lm.id 
            WHERE sai.session_id = ? 
            ORDER BY sai.order_num ASC
        ");
        $stmtItems->execute([$selectedId]);
        $agendaItems = $stmtItems->fetchAll();
    }
}

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-slate-900">Sessões Plenárias do Legislativo</h1>
            <p class="text-xs sm:text-sm text-slate-500">Acompanhe as pautas deliberativas, lista de presenças dos vereadores, resultados das votações e atas oficiais.</p>
        </div>
    </div>

    <?php if ($selectedSession): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider bg-sky-100 text-sky-800 px-2.5 py-0.5 rounded">
                            <?= htmlspecialchars($selectedSession['session_type']) ?>
                        </span>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-100">
                            <?= htmlspecialchars($selectedSession['status']) ?>
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold font-heading text-slate-900">
                        <?= $selectedSession['session_number'] ?>ª Sessão Plenária
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Realizada em <?= formatDate($selectedSession['session_date']) ?> às <?= substr($selectedSession['start_time'], 0, 5) ?> • Presidência: <strong><?= htmlspecialchars($selectedSession['president_name']) ?></strong>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <?php if ($selectedSession['video_url']): ?>
                        <a href="<?= htmlspecialchars($selectedSession['video_url']) ?>" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-play text-xs"></i>
                            <span>Assistir Gravação</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resumo e Ata -->
            <?php if ($selectedSession['minutes_text']): ?>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">Ata Oficial da Sessão</h3>
                    <p class="text-xs text-slate-700 leading-relaxed font-serif whitespace-pre-wrap"><?= htmlspecialchars($selectedSession['minutes_text']) ?></p>
                </div>
            <?php endif; ?>

            <!-- Pauta e Itens da Ordem do Dia com Votos -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-check-to-slot text-sky-600"></i>
                    <span>Itens Deliberados na Ordem do Dia</span>
                </h3>

                <div class="space-y-3">
                    <?php foreach ($agendaItems as $item): ?>
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-bold text-sky-700 uppercase tracking-wider">Item <?= $item['order_num'] ?></span>
                                <h4 class="font-bold text-sm text-slate-900 mt-0.5"><?= htmlspecialchars($item['title']) ?></h4>
                                <?php if ($item['matter_code']): ?>
                                    <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($item['matter_code']) ?>: <?= htmlspecialchars($item['matter_title']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-4 text-xs font-mono">
                                <div class="flex items-center gap-2 text-center">
                                    <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 font-bold"><?= $item['votes_yes'] ?> Sim</span>
                                    <span class="px-2 py-1 rounded bg-rose-100 text-rose-800 font-bold"><?= $item['votes_no'] ?> Não</span>
                                    <span class="px-2 py-1 rounded bg-amber-100 text-amber-800 font-bold"><?= $item['votes_abstention'] ?> Abs</span>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap <?= $item['result'] === 'Aprovado' || str_contains($item['result'], 'Aprovado') ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-800' ?>">
                                    <?= htmlspecialchars($item['result'] ?: $item['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Seletor de Sessões Anteriores -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-4">Histórico de Sessões Plenárias</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            <?php foreach ($sessions as $s): ?>
                <a href="<?= url('portal/sessoes.php?id=' . $s['id']) ?>" class="p-3.5 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors <?= $s['id'] == $selectedId ? 'bg-sky-50 border-sky-400' : '' ?>">
                    <span class="text-[10px] font-bold text-sky-700"><?= $s['session_number'] ?>ª Sessão <?= htmlspecialchars($s['session_type']) ?></span>
                    <h4 class="font-bold text-xs text-slate-900 mt-1"><?= formatDate($s['session_date']) ?></h4>
                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1"><?= htmlspecialchars($s['summary'] ?: 'Pauta da Ordem do Dia.') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

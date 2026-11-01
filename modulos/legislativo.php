<?php
/**
 * Câmara Digital - Módulo 2: Processo Legislativo
 * Conforme Seção 4.2 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Processo Legislativo e Sessões';
$tenantId = Tenant::id();
$db = Database::getConnection();
$user = Auth::user();

// Processa Nova Proposição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_matter'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $type = trim($_POST['type'] ?? 'Projeto de Lei Ordinária');
        $title = trim($_POST['title'] ?? '');
        $summary = trim($_POST['summary'] ?? '');
        $fullText = trim($_POST['full_text'] ?? '');
        $urgency = trim($_POST['urgency'] ?? 'Normal');
        $signatureLevel = trim($_POST['signature_level'] ?? 'Simples');
        $authorCouncilorId = !empty($_POST['author_councilor_id']) ? (int)$_POST['author_councilor_id'] : null;

        // Recupera nome do autor
        $authorName = $user['name'];
        if ($authorCouncilorId) {
            $stmtC = $db->prepare("SELECT full_name FROM councilors WHERE id = ?");
            $stmtC->execute([$authorCouncilorId]);
            $c = $stmtC->fetch();
            if ($c) $authorName = $c['full_name'];
        }

        $year = (int)date('Y');
        // Numeração sequencial
        $stmtSeq = $db->prepare("SELECT MAX(number) FROM legislative_matters WHERE tenant_id = ? AND year = ? AND type = ?");
        $stmtSeq->execute([$tenantId, $year, $type]);
        $nextNum = ((int)$stmtSeq->fetchColumn()) + 1;

        $typePrefixes = [
            'Projeto de Lei Ordinária' => 'PL',
            'Projeto de Lei Complementar' => 'PLC',
            'Projeto de Decreto Legislativo' => 'PDL',
            'Requerimento' => 'REQ',
            'Indicação' => 'IND',
            'Moção' => 'MOC'
        ];
        $prefix = $typePrefixes[$type] ?? 'MAT';
        $code = "{$prefix}-{$nextNum}/{$year}";

        $stmt = $db->prepare("
            INSERT INTO legislative_matters (tenant_id, code, number, year, type, author_councilor_id, author_name, title, summary, full_text, status, urgency, signature_level, presentation_date, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Em Tramitação', ?, ?, CURDATE(), NOW())
        ");
        $stmt->execute([$tenantId, $code, $nextNum, $year, $type, $authorCouncilorId, $authorName, $title, $summary, $fullText, $urgency, $signatureLevel]);
        $matterId = (int)$db->lastInsertId();

        // Tramitação inicial automática
        $stmtMov = $db->prepare("
            INSERT INTO matter_movements (matter_id, from_dept, to_dept, dispatch_text, status_after, movement_date, user_name, created_at)
            VALUES (?, 'Gabinete do Autor', 'Secretaria Legislativa', 'Protocolo e leitura inicial da proposição.', 'Em Tramitação', NOW(), ?, NOW())
        ");
        $stmtMov->execute([$matterId, $user['name']]);

        Audit::log('CRIAR_MATERIA', 'legislative_matters', $matterId, "Proposição {$code} cadastrada pelo autor: {$authorName}");
        setFlash('success', "Proposição {$code} protocolada com sucesso!");
        header("Location: " . url('modulos/legislativo.php'));
        exit;
    }
}

// Processa Nova Tramitação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_movement'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $matterId = (int)($_POST['matter_id'] ?? 0);
        $toDept = trim($_POST['to_dept'] ?? 'Plenário');
        $dispatchText = trim($_POST['dispatch_text'] ?? '');
        $statusAfter = trim($_POST['status_after'] ?? 'Em Tramitação');

        if ($matterId && !empty($dispatchText)) {
            $stmtMov = $db->prepare("
                INSERT INTO matter_movements (matter_id, from_dept, to_dept, dispatch_text, status_after, movement_date, user_name, created_at)
                VALUES (?, 'Secretaria Legislativa', ?, ?, ?, NOW(), ?, NOW())
            ");
            $stmtMov->execute([$matterId, $toDept, $dispatchText, $statusAfter, $user['name']]);

            $db->prepare("UPDATE legislative_matters SET status = ? WHERE id = ?")->execute([$statusAfter, $matterId]);

            Audit::log('TRAMITACAO', 'legislative_matters', $matterId, "Tramitado para {$toDept} com novo status: {$statusAfter}");
            setFlash('success', "Despacho de tramitação registrado com sucesso.");
            header("Location: " . url('modulos/legislativo.php#matter-' . $matterId));
            exit;
        }
    }
}

// Consultas
$matters = $db->query("
    SELECT * FROM legislative_matters 
    WHERE tenant_id = {$tenantId} 
    ORDER BY year DESC, number DESC
")->fetchAll();

$sessions = $db->query("
    SELECT s.*, 
        (SELECT COUNT(*) FROM session_agenda_items WHERE session_id = s.id) as total_items,
        (SELECT COUNT(*) FROM session_agenda_items WHERE session_id = s.id AND status = 'Votado') as voted_items
    FROM sessions s 
    WHERE s.tenant_id = {$tenantId} 
    ORDER BY s.session_date DESC, s.start_time DESC
")->fetchAll();

$councilors = $db->query("SELECT * FROM councilors WHERE tenant_id = {$tenantId} AND is_active = 1 ORDER BY board_role DESC, full_name ASC")->fetchAll();
$norms = $db->query("SELECT * FROM legal_norms WHERE tenant_id = {$tenantId} ORDER BY year DESC, number DESC LIMIT 10")->fetchAll();

// Pega a sessão ativa/recente para o painel de votação
$activeSession = $sessions[0] ?? null;
$agendaItems = [];
if ($activeSession) {
    $stmtItems = $db->prepare("
        SELECT sai.*, lm.code as matter_code, lm.title as matter_title 
        FROM session_agenda_items sai 
        LEFT JOIN legislative_matters lm ON sai.matter_id = lm.id 
        WHERE sai.session_id = ? 
        ORDER BY sai.order_num ASC
    ");
    $stmtItems->execute([$activeSession['id']]);
    $agendaItems = $stmtItems->fetchAll();
}

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 2</span>
                    <span>•</span>
                    <span>Processo Legislativo</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Gestão de Proposições, Pautas & Votação</h1>
                <p class="text-xs sm:text-sm text-slate-500">Acompanhamento do ciclo de vida das matérias, tramitação, pareceres e votação nominal em plenário.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('new-matter-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-file-circle-plus"></i>
                    <span>Apresentar Nova Proposição</span>
                </button>
            </div>
        </div>

        <!-- Tabs / Sub-navegação interna -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-semibold">
            <a href="#matters-list" class="px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 border border-sky-200">
                <i class="fa-solid fa-list mr-1"></i> Proposições em Tramitação
            </a>
            <a href="#plenary-panel" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100">
                <i class="fa-solid fa-check-to-slot mr-1 text-emerald-600"></i> Painel Eletrônico de Votação
            </a>
            <a href="#laws-catalog" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100">
                <i class="fa-solid fa-book-bookmark mr-1 text-indigo-600"></i> Acervo de Leis e Normas
            </a>
        </div>

        <!-- Seção 1: Painel Eletrônico da Ordem do Dia (Interativo) -->
        <div id="plenary-panel" class="bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 rounded-2xl p-6 text-white shadow-xl border border-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-800 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/30">
                        <i class="fa-solid fa-gavel"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs uppercase font-bold tracking-wider text-emerald-400">Plenário Virtual em Tempo Real</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-white font-heading">
                            <?= $activeSession ? "{$activeSession['session_number']}ª Sessão {$activeSession['session_type']} — " . formatDate($activeSession['session_date']) : "Nenhuma Sessão Ativa" ?>
                        </h2>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400">Presidente dos Trabalhos:</span>
                    <p class="font-bold text-xs text-slate-200"><?= htmlspecialchars($activeSession['president_name'] ?? 'Mesa Diretora') ?></p>
                </div>
            </div>

            <!-- Itens da Ordem do Dia e Votação -->
            <div class="mt-6 space-y-4">
                <?php if (empty($agendaItems)): ?>
                    <p class="text-center py-6 text-slate-400 text-xs">Nenhum item em pauta para votação nesta sessão.</p>
                <?php else: ?>
                    <?php foreach ($agendaItems as $item): ?>
                        <div class="bg-slate-900/90 rounded-xl p-4 border border-slate-800 space-y-3" id="agenda-card-<?= $item['id'] ?>">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        Item <?= $item['order_num'] ?> • Votação <?= htmlspecialchars($item['voting_type']) ?>
                                    </span>
                                    <h3 class="font-bold text-sm text-white mt-1"><?= htmlspecialchars($item['title']) ?></h3>
                                    <?php if ($item['matter_code']): ?>
                                        <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($item['matter_code']) ?>: <?= htmlspecialchars($item['matter_title']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= $item['status'] === 'Votado' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' ?>">
                                        <?= htmlspecialchars($item['result'] ?: $item['status']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Placar de Votos -->
                            <div class="grid grid-cols-3 gap-2 bg-slate-950/60 p-2.5 rounded-lg border border-slate-800/80 text-center text-xs">
                                <div>
                                    <span class="text-[10px] text-emerald-400 uppercase font-bold">Sim</span>
                                    <p class="text-lg font-bold text-emerald-400" id="votes-yes-<?= $item['id'] ?>"><?= (int)$item['votes_yes'] ?></p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-rose-400 uppercase font-bold">Não</span>
                                    <p class="text-lg font-bold text-rose-400" id="votes-no-<?= $item['id'] ?>"><?= (int)$item['votes_no'] ?></p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-amber-400 uppercase font-bold">Abstenção</span>
                                    <p class="text-lg font-bold text-amber-400" id="votes-abs-<?= $item['id'] ?>"><?= (int)$item['votes_abstention'] ?></p>
                                </div>
                            </div>

                            <!-- Votação Rápida dos Vereadores (Simulação em Plenário) -->
                            <div class="pt-2 border-t border-slate-800">
                                <p class="text-[11px] text-slate-400 font-semibold mb-2 flex items-center justify-between">
                                    <span>Registrar Voto de Parlamentar:</span>
                                    <span class="text-[10px] text-slate-500">Clique para emitir o voto</span>
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($councilors as $c): ?>
                                        <div class="inline-flex items-center gap-1.5 bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-700 text-xs">
                                            <span class="font-medium text-slate-200"><?= htmlspecialchars($c['full_name']) ?>:</span>
                                            <button onclick="castPlenaryVote(<?= $item['id'] ?>, <?= $c['id'] ?>, 'Sim')" 
                                                    class="px-1.5 py-0.5 rounded bg-emerald-600/30 hover:bg-emerald-600 text-emerald-300 hover:text-white font-bold transition-all text-[10px]">
                                                SIM
                                            </button>
                                            <button onclick="castPlenaryVote(<?= $item['id'] ?>, <?= $c['id'] ?>, 'Não')" 
                                                    class="px-1.5 py-0.5 rounded bg-rose-600/30 hover:bg-rose-600 text-rose-300 hover:text-white font-bold transition-all text-[10px]">
                                                NÃO
                                            </button>
                                            <button onclick="castPlenaryVote(<?= $item['id'] ?>, <?= $c['id'] ?>, 'Abstenção')" 
                                                    class="px-1.5 py-0.5 rounded bg-amber-600/30 hover:bg-amber-600 text-amber-300 hover:text-white font-bold transition-all text-[10px]">
                                                ABS
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seção 2: Tabela de Proposições e Tramitação -->
        <div id="matters-list" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Proposições Cadastradas</h2>
                    <span class="text-[11px] text-slate-500">(<?= count($matters) ?> itens)</span>
                </div>
                <div class="w-full sm:w-64">
                    <input type="text" id="matters-filter" placeholder="Filtrar por código, ementa ou autor..."
                           onkeyup="filterTable('matters-filter', 'matters-table')"
                           class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600" id="matters-table">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Código</th>
                            <th class="p-3">Tipo</th>
                            <th class="p-3">Ementa / Objeto</th>
                            <th class="p-3">Autor</th>
                            <th class="p-3">Data</th>
                            <th class="p-3">Situação</th>
                            <th class="p-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($matters as $m): ?>
                            <tr class="hover:bg-slate-50" id="matter-<?= $m['id'] ?>">
                                <td class="p-3 font-mono font-bold text-sky-700 whitespace-nowrap">
                                    <?= htmlspecialchars($m['code']) ?>
                                </td>
                                <td class="p-3 text-slate-500 whitespace-nowrap"><?= htmlspecialchars($m['type']) ?></td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($m['title']) ?></p>
                                    <p class="text-slate-500 text-[11px] mt-0.5 line-clamp-1"><?= htmlspecialchars($m['summary']) ?></p>
                                </td>
                                <td class="p-3 text-slate-700 whitespace-nowrap"><?= htmlspecialchars($m['author_name']) ?></td>
                                <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= formatDate($m['presentation_date']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $m['status'] === 'Aprovado' ? 'bg-emerald-100 text-emerald-800' : ($m['status'] === 'Rejeitado' ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800') ?>">
                                        <?= htmlspecialchars($m['status']) ?>
                                    </span>
                                </td>
                                <td class="p-3 text-right whitespace-nowrap space-x-1">
                                    <button onclick="openMovementModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['code'])) ?>')" 
                                            class="p-1.5 text-slate-600 hover:text-sky-600 hover:bg-sky-50 rounded" title="Despachar / Tramitar">
                                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                    </button>
                                    <button onclick="summarizeWithAI(<?= $m['id'] ?>)" 
                                            class="p-1.5 text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded" title="Resumir com IA">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção 3: Acervo de Leis e Normas Municipais -->
        <div id="laws-catalog" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-book-bookmark text-indigo-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Acervo Consolidado de Leis e Normas</h2>
                </div>
                <a href="<?= url('portal/leis.php') ?>" target="_blank" class="text-xs text-sky-600 hover:underline font-semibold">
                    Ver Acervo Completo no Portal &rarr;
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($norms as $norm): ?>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-indigo-900"><?= htmlspecialchars($norm['norm_type']) ?> nº <?= $norm['number'] ?>/<?= $norm['year'] ?></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800"><?= htmlspecialchars($norm['status']) ?></span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-800 mt-1"><?= htmlspecialchars($norm['title']) ?></h4>
                        <p class="text-[11px] text-slate-600 mt-1 line-clamp-2"><?= htmlspecialchars($norm['summary']) ?></p>
                        <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-200">
                            <span>Promulgada em: <?= formatDate($norm['promulgation_date']) ?></span>
                            <a href="<?= url('portal/leis.php?id=' . $norm['id']) ?>" target="_blank" class="text-sky-600 hover:underline font-semibold">Texto Integral &rarr;</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Nova Proposição -->
<div id="new-matter-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Apresentar Nova Proposição Legislativa</h3>
            <button onclick="closeModal('new-matter-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/legislativo.php') ?>" method="POST" class="p-6 space-y-4 text-xs overflow-y-auto flex-1">
            <?= csrfField() ?>
            <input type="hidden" name="new_matter" value="1">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Tipo da Proposição</label>
                    <select name="type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Projeto de Lei Ordinária">Projeto de Lei Ordinária</option>
                        <option value="Projeto de Lei Complementar">Projeto de Lei Complementar</option>
                        <option value="Projeto de Decreto Legislativo">Projeto de Decreto Legislativo</option>
                        <option value="Requerimento">Requerimento</option>
                        <option value="Indicação">Indicação</option>
                        <option value="Moção">Moção</option>
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Autor Parlamentar</label>
                    <select name="author_councilor_id" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <?php foreach ($councilors as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['political_party']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Título / Objeto Resumido</label>
                <input type="text" name="title" required placeholder="Ex: Criação do Programa Municipal de Energia Solar"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Ementa Oficial</label>
                <textarea name="summary" rows="2" required placeholder="Dispõe sobre..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label class="block font-medium text-slate-700">Texto Integral da Proposição e Justificativa</label>
                    <a href="<?= url('modulos/ia_legislativa.php') ?>" target="_blank" class="text-[11px] text-purple-600 hover:underline flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Gerar Minuta com IA
                    </a>
                </div>
                <textarea name="full_text" rows="6" placeholder="Art. 1º Fica instituído... &#10;&#10;JUSTIFICATIVA: ..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono text-[11px]"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Regime de Tramitação</label>
                    <select name="urgency" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Normal">Ordinário / Normal</option>
                        <option value="Urgência">Regime de Urgência</option>
                        <option value="Urgentíssima">Urgência Urgentíssima</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Nível de Assinatura (Lei 14.063/2020)</label>
                    <select name="signature_level" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Simples">Assinatura Simples (Login Institucional)</option>
                        <option value="Avançada">Assinatura Avançada (Hash e Autenticação)</option>
                        <option value="Qualificada ICP-Brasil">Assinatura Qualificada ICP-Brasil (A1/A3)</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-matter-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Protocolar Proposição
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Nova Tramitação -->
<div id="movement-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Despacho de Tramitação: <span id="modal-matter-code" class="text-sky-600"></span></h3>
            <button onclick="closeModal('movement-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/legislativo.php') ?>" method="POST" class="p-5 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_movement" value="1">
            <input type="hidden" name="matter_id" id="modal-matter-id" value="">

            <div>
                <label class="block font-medium text-slate-700">Encaminhar para o Setor / Comissão</label>
                <select name="to_dept" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="Comissão de Constituição e Justiça (CCJ)">Comissão de Constituição e Justiça (CCJ)</option>
                    <option value="Comissão de Finanças e Orçamento">Comissão de Finanças e Orçamento</option>
                    <option value="Comissão de Educação e Saúde">Comissão de Educação e Saúde</option>
                    <option value="Plenário / Ordem do Dia">Plenário / Ordem do Dia</option>
                    <option value="Mesa Diretora / Autógrafo">Mesa Diretora / Autógrafo</option>
                    <option value="Poder Executivo (Sanção/Veto)">Poder Executivo (Sanção/Veto)</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Nova Situação / Status</label>
                <select name="status_after" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="Em Tramitação">Em Tramitação</option>
                    <option value="Em Análise na CCJ">Em Análise na CCJ</option>
                    <option value="Pronto para Votação">Pronto para Votação em Plenário</option>
                    <option value="Aprovado">Aprovado</option>
                    <option value="Rejeitado">Rejeitado</option>
                    <option value="Sancionado">Sancionado</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Texto do Despacho / Parecer</label>
                <textarea name="dispatch_text" rows="3" required placeholder="Despacho da Secretaria: Encaminha-se à comissão..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('movement-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Tramitação
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openMovementModal(matterId, matterCode) {
    document.getElementById('modal-matter-id').value = matterId;
    document.getElementById('modal-matter-code').textContent = matterCode;
    openModal('movement-modal');
}

// Computação em tempo real do voto no Plenário via AJAX
function castPlenaryVote(agendaItemId, councilorId, vote) {
    const formData = new FormData();
    formData.append('agenda_item_id', agendaItemId);
    formData.append('councilor_id', councilorId);
    formData.append('vote', vote);

    fetch('<?= url("api/vote_plenary.php") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('votes-yes-' + agendaItemId).textContent = data.votes_yes;
            document.getElementById('votes-no-' + agendaItemId).textContent = data.votes_no;
            document.getElementById('votes-abs-' + agendaItemId).textContent = data.votes_abstention;
            showToast('success', `Voto [${vote}] computado com sucesso!`);
        } else {
            showToast('error', data.error || 'Erro ao registrar voto.');
        }
    })
    .catch(() => {
        showToast('error', 'Falha na comunicação com o painel de votação.');
    });
}

// Resumo Rápido da Matéria com IA
function summarizeWithAI(matterId) {
    showToast('info', 'Gerando resumo executivo da proposição com a IA...');
    const formData = new FormData();
    formData.append('action', 'summarize');
    formData.append('matter_id', matterId);

    fetch('<?= url("api/ai_assistant.php") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.summary) {
            alert(data.summary);
        } else {
            showToast('error', data.error || 'Erro ao resumir matéria.');
        }
    })
    .catch(() => {
        showToast('error', 'Erro ao invocar IA.');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

<?php
/**
 * Câmara Digital - Módulo 9: Ouvidoria e SIC
 * Conforme Seção 4.9 do Planejamento Mestre e Lei de Acesso à Informação
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Ouvidoria e SIC';
$tenantId = Tenant::id();
$db = Database::getConnection();

$requests = $db->query("
    SELECT * FROM citizen_requests 
    WHERE tenant_id = {$tenantId} 
    ORDER BY id DESC
")->fetchAll();

$openCount = count(array_filter($requests, fn($r) => $r['status'] !== 'Respondido'));
$answeredCount = count(array_filter($requests, fn($r) => $r['status'] === 'Respondido'));

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 9</span>
                    <span>•</span>
                    <span>Relacionamento com a Sociedade</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Ouvidoria & Serviço de Informação ao Cidadão (SIC)</h1>
                <p class="text-xs sm:text-sm text-slate-500">Gestão das manifestações e pedidos de informação protocolados pela população, com controle de prazos legais e sigilo.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= url('portal/ouvidoria.php') ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Abrir Formulário Público do Cidadão</span>
                </a>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Manifestações Aguardando Resposta</span>
                <p class="text-2xl font-bold text-amber-600 mt-2"><?= $openCount ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Prazo legal de 20 dias em andamento</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Demandas Respondidas</span>
                <p class="text-2xl font-bold text-emerald-600 mt-2"><?= $answeredCount ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Concluídas com resposta oficial fundamentada</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tempo Médio de Atendimento</span>
                <p class="text-2xl font-bold text-sky-600 mt-2">4.2 dias</p>
                <span class="text-[11px] text-slate-500 mt-1 block">Abaixo do limite máximo regulamentar</span>
            </div>
        </div>

        <!-- Seção 1: Lista de Manifestações e Pedidos -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-headset text-purple-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Protocolos Recebidos no Sistema</h2>
                </div>
                <div class="w-full sm:w-64">
                    <input type="text" id="ouv-filter" placeholder="Buscar por protocolo, assunto ou solicitante..."
                           onkeyup="filterTable('ouv-filter', 'ouv-table')"
                           class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600" id="ouv-table">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Protocolo</th>
                            <th class="p-3">Tipo</th>
                            <th class="p-3">Cidadão Solicitante</th>
                            <th class="p-3">Assunto</th>
                            <th class="p-3">Data Abertura</th>
                            <th class="p-3">Prazo Limite</th>
                            <th class="p-3">Situação</th>
                            <th class="p-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($requests as $r): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700 whitespace-nowrap"><?= htmlspecialchars($r['protocol_code']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= str_contains($r['request_type'], 'SIC') ? 'bg-indigo-100 text-indigo-800' : ($r['request_type'] === 'Denúncia' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700') ?>">
                                        <?= htmlspecialchars($r['request_type']) ?>
                                    </span>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($r['citizen_name']) ?></p>
                                    <p class="text-[11px] text-slate-400"><?= htmlspecialchars($r['citizen_email'] ?: 'Identidade sob sigilo') ?></p>
                                </td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($r['subject']) ?></p>
                                    <p class="text-slate-500 text-[11px] line-clamp-1 mt-0.5"><?= htmlspecialchars($r['message']) ?></p>
                                </td>
                                <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= formatDate($r['created_at']) ?></td>
                                <td class="p-3 font-mono font-bold text-amber-700 whitespace-nowrap"><?= formatDate($r['deadline_date']) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $r['status'] === 'Respondido' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </span>
                                </td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    <button onclick="openReplyModal(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['protocol_code'])) ?>', '<?= htmlspecialchars(addslashes($r['subject'])) ?>', '<?= htmlspecialchars(addslashes($r['message'])) ?>', '<?= htmlspecialchars(addslashes($r['reply_text'] ?? '')) ?>')"
                                            class="px-3 py-1 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 rounded font-semibold text-[11px] transition-colors">
                                        <?= $r['status'] === 'Respondido' ? 'Ver Resposta' : 'Responder' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Responder Manifestação -->
<div id="reply-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Atendimento ao Cidadão: <span id="reply-protocol" class="text-purple-600"></span></h3>
            <button onclick="closeModal('reply-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('api/citizen_action.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="reply_internal">
            <input type="hidden" name="request_id" id="reply-request-id" value="">

            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                <p class="font-bold text-slate-800 text-xs" id="reply-subject"></p>
                <p class="text-[11px] text-slate-600 italic" id="reply-message"></p>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Resposta Oficial da Câmara Municipal</label>
                <textarea name="reply_text" id="reply-text" rows="5" required placeholder="Prezado cidadão, em atenção ao seu pedido..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('reply-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Fechar
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar e Enviar Resposta Oficial
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReplyModal(id, protocol, subject, message, replyText) {
    document.getElementById('reply-request-id').value = id;
    document.getElementById('reply-protocol').textContent = protocol;
    document.getElementById('reply-subject').textContent = subject;
    document.getElementById('reply-message').textContent = '"' + message + '"';
    document.getElementById('reply-text').value = replyText || '';
    openModal('reply-modal');
}
</script>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

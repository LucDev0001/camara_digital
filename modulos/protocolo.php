<?php
/**
 * Câmara Digital - Módulo 10: Protocolo Geral, Documentos e Assinaturas
 * Conforme Seção 4.10 do Planejamento Mestre e integridade SHA-256
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Protocolo Geral e Documentos';
$tenantId = Tenant::id();
$db = Database::getConnection();

$protocols = $db->query("
    SELECT p.*, 
        (SELECT COUNT(*) FROM protocol_dispatches WHERE protocol_id = p.id) as dispatch_count 
    FROM protocols p 
    WHERE p.tenant_id = {$tenantId} 
    ORDER BY p.id DESC
")->fetchAll();

$departments = $db->query("SELECT name FROM departments WHERE tenant_id = {$tenantId} ORDER BY name ASC")->fetchAll();

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 10</span>
                    <span>•</span>
                    <span>Gestão Documental e Fé Pública</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Protocolo Geral & Autenticidade Digital</h1>
                <p class="text-xs sm:text-sm text-slate-500">Registro de expedientes de entrada e saída, despachos entre setores e validação de autenticidade criptográfica SHA-256.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= url('portal/validar.php') ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-qrcode"></i>
                    <span>Validador de Autenticidade</span>
                </a>
                <button onclick="openModal('new-protocol-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-file-circle-plus"></i>
                    <span>Abrir Novo Protocolo</span>
                </button>
            </div>
        </div>

        <!-- Seção: Livro de Protocolo Geral -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-folder-tree text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Livro Eletrônico de Protocolo</h2>
                </div>
                <div class="w-full sm:w-64">
                    <input type="text" id="prot-filter" placeholder="Buscar por protocolo, assunto ou remetente..."
                           onkeyup="filterTable('prot-filter', 'prot-table')"
                           class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600" id="prot-table">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Protocolo nº</th>
                            <th class="p-3">Remetente / Tipo</th>
                            <th class="p-3">Assunto</th>
                            <th class="p-3">Setor Atual</th>
                            <th class="p-3">Autenticidade (SHA-256)</th>
                            <th class="p-3">Situação</th>
                            <th class="p-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($protocols as $p): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700 whitespace-nowrap">
                                    <?= htmlspecialchars($p['protocol_number']) ?>
                                    <span class="block text-[10px] text-slate-400"><?= formatDate($p['created_at']) ?></span>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($p['sender_name']) ?></p>
                                    <span class="text-[10px] font-semibold text-slate-500"><?= htmlspecialchars($p['sender_type']) ?></span>
                                </td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($p['subject']) ?></p>
                                    <p class="text-slate-500 text-[11px] line-clamp-1 mt-0.5"><?= htmlspecialchars($p['summary']) ?></p>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="font-semibold text-slate-700"><?= htmlspecialchars($p['destination_department']) ?></span>
                                    <span class="block text-[10px] text-slate-400"><?= $p['dispatch_count'] ?> despachos</span>
                                </td>
                                <td class="p-3 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                    <span title="<?= htmlspecialchars($p['verification_hash']) ?>" class="cursor-help bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                        <?= substr($p['verification_hash'], 0, 16) ?>...
                                    </span>
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $p['status'] === 'Concluído' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' ?>">
                                        <?= htmlspecialchars($p['status']) ?>
                                    </span>
                                </td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    <button onclick="openDispatchModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['protocol_number'])) ?>')"
                                            class="px-2.5 py-1 bg-slate-100 hover:bg-sky-50 text-sky-700 font-semibold rounded text-[11px] border border-slate-200 transition-colors">
                                        Despachar
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

<!-- Modal: Novo Protocolo -->
<div id="new-protocol-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Registrar Novo Protocolo Geral</h3>
            <button onclick="closeModal('new-protocol-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('api/protocol_action.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="create_protocol">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Tipo de Remetente</label>
                    <select name="sender_type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Cidadão">Cidadão / Munícipe</option>
                        <option value="Órgão Externo">Órgão Externo (Prefeitura / Judiciário)</option>
                        <option value="Vereador">Vereador / Gabinete Parlamentar</option>
                        <option value="Servidor">Servidor Público</option>
                        <option value="Entidade de Classe">Entidade de Classe / Sindicato</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Setor de Destino Inicial</label>
                    <select name="destination_department" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= htmlspecialchars($d['name']) ?>"><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Nome do Remetente</label>
                <input type="text" name="sender_name" required placeholder="Ex: Associação de Moradores do Bairro Pioneiros"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Assunto Principal</label>
                <input type="text" name="subject" required placeholder="Ex: Solicitação de Apoio para Urbanização de Praça"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Resumo / Síntese do Expediente</label>
                <textarea name="summary" rows="3" required placeholder="Descreva sucintamente o teor do documento recebido..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-protocol-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Gerar Protocolo com Hash
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Despachar Protocolo -->
<div id="dispatch-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Despacho Eletrônico: <span id="dispatch-protocol" class="text-sky-600"></span></h3>
            <button onclick="closeModal('dispatch-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('api/protocol_action.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="dispatch">
            <input type="hidden" name="protocol_id" id="dispatch-protocol-id" value="">

            <div>
                <label class="block font-medium text-slate-700">Encaminhar para o Setor</label>
                <select name="to_department" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= htmlspecialchars($d['name']) ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Texto do Despacho</label>
                <textarea name="dispatch_text" rows="3" required placeholder="Encaminho para análise e providências cabíveis..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('dispatch-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Despacho
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDispatchModal(id, protocol) {
    document.getElementById('dispatch-protocol-id').value = id;
    document.getElementById('dispatch-protocol').textContent = protocol;
    openModal('dispatch-modal');
}
</script>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

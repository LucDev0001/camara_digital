<?php
/**
 * Câmara Digital - Portal da Ouvidoria e Serviço de Informação ao Cidadão (SIC)
 * Conforme Lei 12.527/11 (LAI) e LGPD (Lei 13.709/18)
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Ouvidoria e SIC — Canal do Cidadão';
$tenantId = Tenant::id();
$db = Database::getConnection();

$searchProtocol = trim($_GET['protocol'] ?? '');
$searchedRequest = null;

if (!empty($searchProtocol)) {
    $stmt = $db->prepare("SELECT * FROM citizen_requests WHERE protocol_code = ? AND tenant_id = ? LIMIT 1");
    $stmt->execute([$searchProtocol, $tenantId]);
    $searchedRequest = $stmt->fetch();
}

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-purple-700 uppercase tracking-wider">
                <i class="fa-solid fa-headset"></i>
                <span>Canal Oficial de Atendimento</span>
            </div>
            <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Ouvidoria & Serviço de Informação ao Cidadão (SIC)</h1>
            <p class="text-xs sm:text-sm text-slate-500">Envie suas solicitações, denúncias, elogios ou pedidos com base na Lei de Acesso à Informação e acompanhe por protocolo.</p>
        </div>
    </div>

    <!-- Seção de Consulta por Protocolo -->
    <div class="bg-gradient-to-r from-purple-900 to-indigo-950 p-6 sm:p-8 rounded-2xl text-white shadow-lg space-y-4">
        <div class="max-w-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-purple-300">Já possui um protocolo?</span>
            <h2 class="text-lg sm:text-xl font-bold font-heading text-white mt-1">Consulte o Andamento da sua Manifestação</h2>
            <p class="text-xs text-slate-300 mt-1">Digite o código gerado no momento do envio (ex: OUV-2026-10492) para verificar a resposta oficial.</p>
        </div>

        <form action="<?= url('portal/ouvidoria.php') ?>" method="GET" class="max-w-xl flex gap-2">
            <input type="text" name="protocol" value="<?= htmlspecialchars($searchProtocol) ?>" required placeholder="Ex: OUV-2026-10492"
                   class="flex-1 px-4 py-2.5 text-xs sm:text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
            <button type="submit" class="px-5 py-2.5 bg-purple-500 hover:bg-purple-400 text-white font-semibold text-xs rounded-xl shadow-md transition-colors">
                Consultar Protocolo
            </button>
        </form>

        <!-- Resultado da Consulta por Protocolo -->
        <?php if (!empty($searchProtocol)): ?>
            <div class="mt-4 pt-4 border-t border-purple-800/80">
                <?php if ($searchedRequest): ?>
                    <div class="bg-white rounded-xl p-5 text-slate-800 space-y-3 shadow-md">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="text-xs font-mono font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-100">
                                <?= htmlspecialchars($searchedRequest['protocol_code']) ?>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded <?= $searchedRequest['status'] === 'Respondido' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= htmlspecialchars($searchedRequest['status']) ?>
                            </span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900"><?= htmlspecialchars($searchedRequest['subject']) ?></h3>
                        <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100 italic">"<?= htmlspecialchars($searchedRequest['message']) ?>"</p>
                        
                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-500 pt-2">
                            <span>Tipo: <strong><?= htmlspecialchars($searchedRequest['request_type']) ?></strong></span>
                            <span>Prazo Legal de Resposta: <strong><?= formatDate($searchedRequest['deadline_date']) ?></strong></span>
                        </div>

                        <?php if ($searchedRequest['reply_text']): ?>
                            <div class="mt-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-950 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Resposta Oficial da Câmara Municipal:</span>
                                <p class="text-xs leading-relaxed font-medium"><?= htmlspecialchars($searchedRequest['reply_text']) ?></p>
                                <p class="text-[10px] text-emerald-600 pt-1">Atendido em <?= formatDate($searchedRequest['replied_at'], true) ?> por <?= htmlspecialchars($searchedRequest['replied_by']) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                                <i class="fa-regular fa-clock mr-1"></i> Sua manifestação está em análise pelo setor competente da Câmara. O prazo legal de resposta é de até 20 dias corridos.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-lg bg-rose-500/20 border border-rose-500/40 text-rose-200 text-xs">
                        Nenhuma manifestação encontrada com o protocolo "<?= htmlspecialchars($searchProtocol) ?>". Verifique a digitação e tente novamente.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Formulário de Nova Manifestação / Pedido LAI -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div>
            <h2 class="text-lg font-bold font-heading text-slate-900">Registrar Nova Manifestação ou Pedido SIC</h2>
            <p class="text-xs text-slate-500 mt-1">Preencha o formulário abaixo. Seu protocolo será gerado imediatamente após o envio.</p>
        </div>

        <form action="<?= url('api/citizen_action.php') ?>" method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="action" value="submit_public">
            <input type="hidden" name="tenant_id" value="<?= $tenantId ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Tipo de Manifestação</label>
                    <select name="request_type" required class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="SIC - Pedido LAI">SIC - Pedido de Acesso à Informação (Lei 12.527)</option>
                        <option value="Sugestão">Sugestão de Projeto / Melhoria</option>
                        <option value="Reclamação">Reclamação de Serviço Público</option>
                        <option value="Denúncia">Denúncia de Irregularidade</option>
                        <option value="Elogio">Elogio</option>
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700">
                        <input type="checkbox" name="is_anonymous" value="1" id="anon-checkbox"
                               onchange="toggleAnonymous(this.checked)"
                               class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                        <span>Desejo enviar de forma anônima (sem identificação)</span>
                    </label>
                </div>
            </div>

            <!-- Campos de Identificação -->
            <div id="ident-fields" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Seu Nome Completo</label>
                    <input type="text" name="citizen_name" id="citizen-name" placeholder="Ex: Maria das Graças"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Seu E-mail (Para receber a resposta)</label>
                    <input type="email" name="citizen_email" id="citizen-email" placeholder="maria@email.com"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Telefone / WhatsApp</label>
                    <input type="text" name="citizen_phone" placeholder="(67) 99999-0000"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Assunto Principal</label>
                <input type="text" name="subject" required placeholder="Ex: Solicitação de relatório de despesas com manutenção"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Mensagem / Detalhamento da Solicitação</label>
                <textarea name="message" rows="4" required placeholder="Descreva detalhadamente sua dúvida, pedido de acesso à informação ou manifestação..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none"></textarea>
            </div>

            <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-[11px] text-purple-900 leading-relaxed">
                <i class="fa-solid fa-shield-halved mr-1 text-purple-600"></i>
                <strong>Garantia Legal de Privacidade:</strong> Seus dados são protegidos conforme a LGPD (Lei 13.709/18). Manifestações com pedido de sigilo não terão dados pessoais exibidos publicamente.
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-xl shadow-md transition-colors text-xs">
                    Enviar Manifestação & Gerar Protocolo
                </button>
            </div>
        </form>
    </div>

</main>

<script>
function toggleAnonymous(isAnon) {
    const fields = document.getElementById('ident-fields');
    const nameInput = document.getElementById('citizen-name');
    const emailInput = document.getElementById('citizen-email');

    if (isAnon) {
        fields.classList.add('opacity-40', 'pointer-events-none');
        nameInput.required = false;
        emailInput.required = false;
    } else {
        fields.classList.remove('opacity-40', 'pointer-events-none');
        nameInput.required = true;
        emailInput.required = true;
    }
}
</script>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

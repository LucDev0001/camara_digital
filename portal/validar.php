<?php
/**
 * Câmara Digital - Validador Público de Autenticidade Documental
 * Verificação de Hash Criptográfico SHA-256 e Fé Pública
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Validador de Autenticidade Documental';
$tenantId = Tenant::id();
$db = Database::getConnection();

$codeOrHash = trim($_GET['code'] ?? '');
$verifiedProtocol = null;
$searched = false;

if (!empty($codeOrHash)) {
    $searched = true;
    $stmt = $db->prepare("
        SELECT * FROM protocols 
        WHERE tenant_id = ? AND (protocol_number = ? OR verification_hash = ?) 
        LIMIT 1
    ");
    $stmt->execute([$tenantId, $codeOrHash, $codeOrHash]);
    $verifiedProtocol = $stmt->fetch();
}

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 text-xl font-bold mb-2">
            <i class="fa-solid fa-qrcode"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">Validador de Autenticidade Documental</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto">
            Verifique a integridade e fé pública de certidões, atos, protocolos e ofícios emitidos eletronicamente pela Câmara Municipal.
        </p>
    </div>

    <!-- Campo de Busca -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <form action="<?= url('portal/validar.php') ?>" method="GET" class="space-y-3">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                Informe o Número do Protocolo ou Código Verificador (Hash SHA-256)
            </label>
            <div class="flex gap-2">
                <input type="text" name="code" value="<?= htmlspecialchars($codeOrHash) ?>" required 
                       placeholder="Ex: PROT-2026/00142 ou cole o hash de 64 caracteres..."
                       class="flex-1 px-4 py-2.5 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-md transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>Verificar</span>
                </button>
            </div>
            <p class="text-[11px] text-slate-600">
                Exemplos para testar: <code class="bg-slate-100 px-1 py-0.5 rounded text-sky-700 font-bold cursor-pointer" onclick="document.querySelector('input[name=code]').value='PROT-2026/00142'">PROT-2026/00142</code>
            </p>
        </form>
    </div>

    <!-- Resultado da Validação -->
    <?php if ($searched): ?>
        <?php if ($verifiedProtocol): ?>
            <!-- Selo Oficial de Autenticidade -->
            <div class="bg-white rounded-2xl border-2 border-emerald-500 p-6 sm:p-8 shadow-xl space-y-6 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 opacity-5 text-emerald-900 text-9xl pointer-events-none">
                    <i class="fa-solid fa-stamp"></i>
                </div>

                <div class="flex items-center gap-4 border-b border-emerald-100 pb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl font-bold border border-emerald-200">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Certidão de Autenticidade Confirmada</span>
                        <h2 class="text-lg font-bold font-heading text-slate-900">Documento Válido e Registrado nos Livros Oficiais</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Número do Protocolo:</span>
                        <strong class="font-mono text-slate-900 text-sm"><?= htmlspecialchars($verifiedProtocol['protocol_number']) ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Data/Hora de Registro Oficial:</span>
                        <strong class="font-mono text-slate-900"><?= formatDate($verifiedProtocol['created_at'], true) ?></strong>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 block">Interessado / Remetente:</span>
                        <strong class="text-slate-900"><?= htmlspecialchars($verifiedProtocol['sender_name']) ?> (<?= htmlspecialchars($verifiedProtocol['sender_type']) ?>)</strong>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 block">Assunto / Objeto do Ato:</span>
                        <p class="text-slate-800 font-medium"><?= htmlspecialchars($verifiedProtocol['subject']) ?></p>
                        <p class="text-[11px] text-slate-500 mt-1"><?= htmlspecialchars($verifiedProtocol['summary']) ?></p>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Setor Responsável Atual:</span>
                        <strong class="text-slate-900"><?= htmlspecialchars($verifiedProtocol['destination_department']) ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Situação Tramitatória:</span>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            <?= htmlspecialchars($verifiedProtocol['status']) ?>
                        </span>
                    </div>
                </div>

                <!-- Hash SHA-256 Inviolável -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Hash Criptográfico de Integridade (SHA-256):</span>
                    <p class="font-mono text-[11px] text-sky-800 break-all select-all font-bold">
                        <?= htmlspecialchars($verifiedProtocol['verification_hash']) ?>
                    </p>
                </div>

                <div class="pt-2 text-[11px] text-slate-400 text-center border-t border-slate-100">
                    Esta certidão digital substitui o carimbo físico nos termos da Medida Provisória nº 2.200-2/2001 e legislação municipal vigente.
                </div>
            </div>
        <?php else: ?>
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 text-center space-y-2">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-3xl"></i>
                <h3 class="font-bold text-sm text-rose-900">Documento ou Código Não Encontrado</h3>
                <p class="text-xs text-rose-700 max-w-md mx-auto">
                    Não foi localizado nenhum protocolo ou documento assinado correspondente ao código informado ("<?= htmlspecialchars($codeOrHash) ?>").
                </p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

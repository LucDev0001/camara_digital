<?php
/**
 * Câmara Digital - Tela de Bloqueio de Segurança / Suspensão por Inadimplência ou Cópia Não Autorizada
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/LicenseGuard.php';

$tenant = Tenant::current();

// Processa ações de desbloqueio / sincronização na própria tela de bloqueio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'apply_token') {
        $token = trim($_POST['signed_token'] ?? '');
        $res = LicenseGuard::applySignedToken($token);
        if ($res['success'] && $res['status'] === 'active') {
            setFlash('success', 'Licença validada e desbloqueada com sucesso!');
            header('Location: ' . url('dashboard.php'));
            exit;
        } else {
            setFlash('error', $res['error'] ?? 'O token informado mantém o sistema bloqueado.');
        }
    } elseif ($action === 'sync_vercel') {
        $res = LicenseGuard::syncWithVercel();
        $checkAfter = LicenseGuard::check();
        if ($checkAfter['valid']) {
            setFlash('success', 'Assinatura revalidada junto ao servidor Vercel Guard!');
            header('Location: ' . url('dashboard.php'));
            exit;
        } else {
            setFlash('warning', $res['message']);
        }
    } elseif ($action === 'dev_unlock') {
        $pin = trim($_POST['dev_pin'] ?? '');
        $res = LicenseGuard::devOverride($pin, 'active', null);
        if ($res['success']) {
            setFlash('success', 'Acesso liberado pela Chave Mestra do Desenvolvedor!');
            header('Location: ' . url('dashboard.php'));
            exit;
        } else {
            setFlash('error', $res['error']);
        }
    }
}

$check = LicenseGuard::check();
$lic = $check['license'];
$currentFp = $check['current_fingerprint'];
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Bloqueado — Controle de Licenciamento | Câmara Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="min-h-full flex items-center justify-center p-4 text-slate-100 antialiased">

    <div class="max-w-2xl w-full bg-slate-900 border border-rose-500/40 rounded-3xl shadow-2xl overflow-hidden">
        <!-- Banner Topo -->
        <div class="bg-gradient-to-r from-rose-900 via-rose-800 to-slate-900 p-6 sm:p-8 border-b border-rose-500/30 flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-500/20 border border-rose-400/40 flex items-center justify-center text-rose-300 text-2xl flex-shrink-0">
                <i class="fa-solid <?= $check['status'] === 'blocked_copy' ? 'fa-microchip' : 'fa-lock' ?>"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-rose-500/30 text-rose-200 border border-rose-400/30">
                    <?= $check['status'] === 'blocked_copy' ? 'Trava Anti-Cópia Acionada' : 'Assinatura Suspensa / Inadimplência' ?>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white mt-2">
                    <?= $check['status'] === 'blocked_copy' ? 'Cópia Não Autorizada ou Mudança de Servidor' : 'Acesso Administrativo Suspenso Temporariamente' ?>
                </h1>
                <p class="text-xs text-rose-200/90 mt-1">
                    <?= htmlspecialchars($tenant['name']) ?> — CNPJ: <?= formatCnpj($tenant['cnpj']) ?>
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <?php if ($flash): ?>
                <div class="p-3.5 rounded-xl text-xs font-semibold <?= $flash['type'] === 'error' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40' ?>">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <!-- Motivo do Bloqueio -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400">Diagnóstico de Segurança do Sistema:</span>
                <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-medium">
                    <?= htmlspecialchars($check['reason'] ?? 'Licença suspensa pelo servidor central.') ?>
                </p>
            </div>

            <!-- Dados Técnicos da Instalação (Para envio ao Desenvolvedor) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Chave da Licença Local</span>
                    <span class="font-mono font-bold text-sky-400 text-sm"><?= htmlspecialchars($lic['license_key']) ?></span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Vencimento Contratual</span>
                    <span class="font-mono font-bold text-amber-400 text-sm"><?= formatDate($lic['expires_at']) ?></span>
                </div>
                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-950/70 border border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-slate-500">Fingerprint Físico deste Servidor (Anti-Cópia SHA-256)</span>
                        <button type="button" onclick="navigator.clipboard.writeText('<?= $currentFp ?>'); alert('Fingerprint copiado!');" class="text-[11px] text-sky-400 hover:underline">
                            <i class="fa-regular fa-copy"></i> Copiar Hash
                        </button>
                    </div>
                    <p class="font-mono text-[11px] text-slate-300 mt-1 break-all"><?= htmlspecialchars($currentFp) ?></p>
                </div>
            </div>

            <!-- Opção 1: Inserir Token de Desbloqueio Gerado no Vercel -->
            <form action="<?= url('licenca_bloqueada.php') ?>" method="POST" class="space-y-3 pt-2 border-t border-slate-800">
                <input type="hidden" name="action" value="apply_token">
                <label class="block text-xs font-bold text-slate-300">
                    <i class="fa-solid fa-key text-indigo-400 mr-1"></i> Inserir Token Criptográfico de Liberação (Gerado no Painel Vercel):
                </label>
                <div class="flex gap-2">
                    <input type="text" name="signed_token" required placeholder="Cole aqui o Token Base64 assinado pelo Desenvolvedor..."
                           class="flex-1 px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition">
                        Validar & Desbloquear
                    </button>
                </div>
            </form>

            <!-- Opção 2: Sincronizar Online com Vercel ou Desbloqueio Rápido do Dev -->
            <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form action="<?= url('licenca_bloqueada.php') ?>" method="POST">
                    <input type="hidden" name="action" value="sync_vercel">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition">
                        <i class="fa-solid fa-cloud-arrow-down text-sky-400"></i>
                        <span>Verificar Pagamento Online (Vercel API)</span>
                    </button>
                </form>

                <!-- Desbloqueio Direto com PIN do Desenvolvedor -->
                <form action="<?= url('licenca_bloqueada.php') ?>" method="POST" class="flex items-center gap-2">
                    <input type="hidden" name="action" value="dev_unlock">
                    <input type="password" name="dev_pin" placeholder="PIN do Dev (admin2026)" 
                           class="px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white font-mono w-44">
                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                        <i class="fa-solid fa-unlock"></i> Destravar (Dev)
                    </button>
                </form>
            </div>

            <div class="text-center pt-2">
                <a href="<?= url('portal/index.php') ?>" class="text-[11px] text-slate-500 hover:text-slate-300 underline">
                    Ir para o Portal Público da Transparência (Acesso Cidadão continua disponível)
                </a>
            </div>
        </div>
    </div>

</body>
</html>


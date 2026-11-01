<?php
/**
 * Câmara Digital - Tela de Autenticação Segura e Perfis de Demonstração
 */
require_once __DIR__ . '/config/app.php';

$error = null;

// Processa Login Manual
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quick_login_id'])) {
        $userId = (int)$_POST['quick_login_id'];
        if (Auth::loginAs($userId)) {
            $redirect = $_SESSION['redirect_url'] ?? url('dashboard.php');
            unset($_SESSION['redirect_url']);
            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Falha ao autenticar perfil rápido.";
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (Auth::login($email, $password)) {
            $redirect = $_SESSION['redirect_url'] ?? url('dashboard.php');
            unset($_SESSION['redirect_url']);
            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Credenciais inválidas. Verifique seu e-mail e senha.";
        }
    }
}

// Se já estiver logado, vai para o dashboard
if (Auth::check()) {
    header("Location: " . url('dashboard.php'));
    exit;
}

$tenant = Tenant::current();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Restrito — Câmara Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-slate-100">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <a href="<?= url('portal/index.php') ?>" class="inline-flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-sky-500/30">
                <i class="fa-solid fa-landmark text-xl"></i>
            </div>
            <div class="text-left">
                <span class="text-2xl font-bold font-heading tracking-tight text-white">Câmara<span class="text-sky-400">Digital</span></span>
                <span class="block text-[11px] uppercase font-semibold tracking-wider text-slate-400">Plataforma SaaS de Gestão Legislativa</span>
            </div>
        </a>
        <h2 class="mt-4 text-sm font-medium text-slate-300">
            Acesso Restrito para Parlamentares e Servidores Públicos
        </h2>
        <div class="inline-flex items-center gap-2 mt-2 px-3 py-1 rounded-full bg-sky-950/60 border border-sky-800 text-sky-300 text-xs font-semibold">
            <i class="fa-solid fa-building-columns text-[10px]"></i>
            <span><?= htmlspecialchars($tenant['name']) ?></span>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4">
        <div class="bg-slate-900/80 backdrop-blur-md py-8 px-6 sm:px-10 shadow-2xl rounded-2xl border border-slate-800 space-y-6">

            <?php if ($error): ?>
                <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-medium flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulário Manual -->
            <form action="<?= url('login.php') ?>" method="POST" class="space-y-4">
                <?= csrfField() ?>
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300">E-mail Institucional</label>
                    <div class="mt-1 relative">
                        <input id="email" name="email" type="email" required value="admin@camara.ms.gov.br"
                               class="w-full px-3 py-2 bg-slate-800/80 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-300">Senha de Acesso</label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" required value="123456"
                               class="w-full px-3 py-2 bg-slate-800/80 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="rounded bg-slate-800 border-slate-700 text-sky-600 focus:ring-sky-500">
                        <span>Lembrar meu acesso</span>
                    </label>
                    <a href="#" class="text-sky-400 hover:text-sky-300">Esqueceu a senha?</a>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-semibold rounded-lg text-sm shadow-md shadow-sky-600/30 transition-all">
                    Entrar no Sistema
                </button>
            </form>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-800"></div>
                <span class="flex-shrink mx-4 text-slate-500 text-xs font-medium uppercase tracking-wider">Ou acesse com 1 clique para testar</span>
                <div class="flex-grow border-t border-slate-800"></div>
            </div>

            <!-- Quick Profiles for Instant Demo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                <!-- Admin -->
                <form action="<?= url('login.php') ?>" method="POST">
                    <input type="hidden" name="quick_login_id" value="1">
                    <button type="submit" class="w-full text-left p-2.5 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-sky-500/50 transition-all flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-user-shield text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-200">Administrador Geral</span>
                            <span class="block text-[10px] text-slate-400">Acesso Irrestrito (TI / Gestão)</span>
                        </div>
                    </button>
                </form>

                <!-- Presidente / Mesa -->
                <form action="<?= url('login.php') ?>" method="POST">
                    <input type="hidden" name="quick_login_id" value="2">
                    <button type="submit" class="w-full text-left p-2.5 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-amber-500/50 transition-all flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-gavel text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-200">Presidente da Câmara</span>
                            <span class="block text-[10px] text-slate-400">Mesa Diretora & Pautas</span>
                        </div>
                    </button>
                </form>

                <!-- Vereadora -->
                <form action="<?= url('login.php') ?>" method="POST">
                    <input type="hidden" name="quick_login_id" value="3">
                    <button type="submit" class="w-full text-left p-2.5 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-purple-500/50 transition-all flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-user-tie text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-200">Verª. Mariana (Vereador)</span>
                            <span class="block text-[10px] text-slate-400">Proposições & Votação</span>
                        </div>
                    </button>
                </form>

                <!-- Secretaria Legislativa -->
                <form action="<?= url('login.php') ?>" method="POST">
                    <input type="hidden" name="quick_login_id" value="4">
                    <button type="submit" class="w-full text-left p-2.5 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-indigo-500/50 transition-all flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-file-signature text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-200">Secretaria Legislativa</span>
                            <span class="block text-[10px] text-slate-400">Tramitação, Atas & Comissões</span>
                        </div>
                    </button>
                </form>

                <!-- Almoxarifado -->
                <form action="<?= url('login.php') ?>" method="POST">
                    <input type="hidden" name="quick_login_id" value="6">
                    <button type="submit" class="w-full text-left p-2.5 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-emerald-500/50 transition-all flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-boxes-stacked text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-200">Almoxarifado & Bens</span>
                            <span class="block text-[10px] text-slate-400">Estoque & Requisições</span>
                        </div>
                    </button>
                </form>

                <!-- Ouvidoria / SIC -->
                <form action="<?= url('login.php') ?>" method="POST">
                    <input type="hidden" name="quick_login_id" value="10">
                    <button type="submit" class="w-full text-left p-2.5 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-rose-500/50 transition-all flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-headset text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-200">Ouvidoria & SIC</span>
                            <span class="block text-[10px] text-slate-400">Manifestações dos Munícipes</span>
                        </div>
                    </button>
                </form>
            </div>

            <div class="pt-2 text-center text-xs text-slate-400">
                <a href="<?= url('portal/index.php') ?>" class="text-sky-400 hover:text-sky-300 font-medium inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Ir para o Portal Público da Câmara (Transparência / Cidadão)</span>
                </a>
            </div>

        </div>
    </div>

</body>
</html>

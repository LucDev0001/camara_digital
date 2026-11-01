<?php
/**
 * Câmara Digital - Cabeçalho do Painel Administrativo
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/LicenseGuard.php';

Auth::requireLogin();
LicenseGuard::enforce();

// Verifica automaticamente o acesso RBAC ao módulo atual configurado pelo Administrador Geral
$currentScriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
if (str_contains($currentScriptPath, '/modulos/')) {
    $currentModuleSlug = pathinfo($currentScriptPath, PATHINFO_FILENAME);
    Auth::requireModuleAccess($currentModuleSlug);
}

$currentUser = Auth::user();
$currentTenant = Tenant::current();
$allTenants = Tenant::all();
$flash = getFlash();
$pageTitle = $pageTitle ?? 'Painel Geral';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — <?= htmlspecialchars($currentTenant['name']) ?> | Câmara Digital</title>
    <!-- Tailwind CSS CDN com Plugins -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="h-full antialiased text-slate-800 flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo & Mobile Toggle -->
                <div class="flex items-center gap-3">
                    <button id="sidebar-toggle" type="button" class="lg:hidden p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <a href="<?= url('dashboard.php') ?>" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-indigo-700 flex items-center justify-center text-white shadow-md shadow-sky-500/20">
                            <i class="fa-solid fa-landmark text-lg"></i>
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-lg font-bold font-heading tracking-tight text-slate-900">Câmara<span class="text-sky-600">Digital</span></span>
                            <span class="block text-[10px] uppercase font-semibold tracking-wider text-slate-600">SaaS Multi-Câmara v1.0</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Multi-tenant Selector Dropdown -->
                <div class="flex items-center gap-2">
                    <form action="<?= url('api/switch_tenant.php') ?>" method="POST" id="tenant-form" class="flex items-center">
                        <?= csrfField() ?>
                        <div class="relative inline-block">
                            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 hover:border-slate-300 transition-colors">
                                <i class="fa-solid fa-building-columns text-sky-600 text-xs"></i>
                                <select name="tenant_id" onchange="document.getElementById('tenant-form').submit()" class="bg-transparent text-xs font-semibold text-slate-700 focus:outline-none cursor-pointer pr-4">
                                    <?php foreach ($allTenants as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= $t['id'] === $currentTenant['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['state']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right: Quick Search, IA, Portal Público & Profile -->
                <div class="flex items-center gap-3">
                    <!-- Quick Search Trigger -->
                    <button onclick="openModal('quick-search-modal')" class="hidden md:flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                        <span>Pesquisa Rápida...</span>
                        <kbd class="bg-white px-1.5 py-0.5 rounded border border-slate-300 text-[10px] font-mono text-slate-600">Ctrl K</kbd>
                    </button>

                    <!-- IA Assistant Link -->
                    <a href="<?= url('modulos/ia_legislativa.php') ?>" class="flex items-center gap-2 text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 px-3 py-1.5 rounded-lg transition-colors">
                        <i class="fa-solid fa-wand-magic-sparkles text-purple-600"></i>
                        <span class="hidden sm:inline">IA Legislativa</span>
                        <span class="pulse-indicator bg-purple-500"></span>
                    </a>

                    <!-- Portal do Cidadão Link -->
                    <a href="<?= url('portal/index.php') ?>" target="_blank" class="hidden sm:flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-sky-600 hover:bg-slate-100 px-2.5 py-1.5 rounded-lg transition-colors" title="Ver Portal Público do Cidadão">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        <span>Portal do Cidadão</span>
                    </a>

                    <!-- Central de Ajuda & Guia Link -->
                    <a href="<?= url('ajuda.php') ?>" class="flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-sky-600 hover:bg-slate-100 px-2.5 py-1.5 rounded-lg transition-colors" title="Central de Ajuda e Guia do Usuário">
                        <i class="fa-solid fa-circle-question text-sky-600 text-xs"></i>
                        <span class="hidden md:inline">Ajuda</span>
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                            <div class="w-8 h-8 rounded-full bg-slate-800 text-white font-bold text-xs flex items-center justify-center">
                                <?= strtoupper(substr($currentUser['name'], 0, 2)) ?>
                            </div>
                            <div class="hidden lg:block text-left">
                                <span class="block text-xs font-semibold text-slate-800 leading-tight"><?= htmlspecialchars($currentUser['name']) ?></span>
                                <span class="block text-[11px] text-sky-600 font-medium"><?= htmlspecialchars($currentUser['role_name']) ?></span>
                            </div>
                            <a href="<?= url('logout.php') ?>" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors ml-1" title="Encerrar Sessão">
                                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notification Banner -->
    <?php if ($flash): ?>
        <div id="flash-message" class="w-full text-white text-sm font-medium px-6 py-2.5 flex items-center justify-between <?= $flash['type'] === 'success' ? 'bg-emerald-600' : ($flash['type'] === 'error' ? 'bg-rose-600' : ($flash['type'] === 'warning' ? 'bg-amber-600' : 'bg-sky-600')) ?>">
            <div class="flex items-center gap-2 mx-auto">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-info' ?>"></i>
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
            <button onclick="document.getElementById('flash-message').remove()" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Main Workspace Layout -->
    <div class="flex-1 flex overflow-hidden">

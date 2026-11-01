<?php
/**
 * Câmara Digital - Cabeçalho do Portal Público do Cidadão
 */
require_once __DIR__ . '/../config/app.php';

$currentTenant = Tenant::current();
$allTenants = Tenant::all();
$flash = getFlash();
$publicTitle = $publicTitle ?? 'Portal Oficial';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($publicTitle) ?> — <?= htmlspecialchars($currentTenant['name']) ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="min-h-full flex flex-col antialiased text-slate-800 bg-slate-50">

    <!-- Barra de Acessibilidade Governamental (eMAG / WCAG 2.1 AA - Seção 11.2 & 28.4) -->
    <div class="bg-slate-950 text-slate-300 text-[11px] py-1.5 px-4 sm:px-8 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <span class="text-slate-400 font-semibold hidden md:inline">Acessibilidade:</span>
                <a href="#main-content" accesskey="1" class="hover:text-white transition-colors" title="Ir para o conteúdo principal [Alt + 1]">Ir para o conteúdo [1]</a>
                <a href="#main-nav" accesskey="2" class="hover:text-white transition-colors" title="Ir para o menu de navegação [Alt + 2]">Ir para o menu [2]</a>
                <a href="#public-footer" accesskey="4" class="hover:text-white transition-colors" title="Ir para o rodapé [Alt + 4]">Ir para o rodapé [4]</a>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleHighContrast()" id="btn-contrast" class="hover:text-white transition-colors font-semibold flex items-center gap-1" title="Alternar Alto Contraste">
                    <i class="fa-solid fa-circle-half-stroke text-amber-400"></i> Alto Contraste
                </button>
                <div class="flex items-center gap-1.5 font-bold pl-2 border-l border-slate-800 text-xs">
                    <button type="button" onclick="changeFontSize(1)" class="hover:text-white px-1" title="Aumentar Fonte">A+</button>
                    <button type="button" onclick="changeFontSize(-1)" class="hover:text-white px-1" title="Diminuir Fonte">A-</button>
                    <button type="button" onclick="resetFontSize()" class="hover:text-white px-1 text-[11px]" title="Tamanho Padrão">A</button>
                </div>
                <a href="<?= url('portal/carta_servicos.php') ?>" class="text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1 pl-2 border-l border-slate-800">
                    <i class="fa-solid fa-handshake-angle text-[10px]"></i> Carta de Serviços
                </a>
            </div>
        </div>
    </div>

    <!-- Top Institutional Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 sm:px-8 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5 text-slate-400">
                    <i class="fa-solid fa-location-dot text-sky-400 text-xs"></i>
                    <span><?= htmlspecialchars($currentTenant['city']) ?> — <?= htmlspecialchars($currentTenant['state']) ?></span>
                </span>
                <span class="hidden md:inline text-slate-600">•</span>
                <span class="hidden md:inline text-slate-400">
                    CNPJ: <?= formatCnpj($currentTenant['cnpj']) ?>
                </span>
            </div>
            <div class="flex items-center gap-3">
                <!-- Seletor de Município / Câmara Ativa no Portal -->
                <form action="<?= url('api/switch_tenant.php') ?>" method="POST" class="inline-flex items-center" id="public-tenant-form">
                    <?= csrfField() ?>
                    <label class="text-[11px] text-slate-400 mr-1.5 hidden sm:inline">Município:</label>
                    <select name="tenant_id" onchange="document.getElementById('public-tenant-form').submit()" 
                            class="bg-slate-800 text-white text-[11px] font-semibold rounded px-2 py-0.5 border border-slate-700 cursor-pointer focus:outline-none">
                        <?php foreach ($allTenants as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $t['id'] === $currentTenant['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <!-- Link de Acesso Restrito aos Servidores -->
                <a href="<?= url('login.php') ?>" class="text-[11px] text-sky-400 hover:text-sky-300 font-semibold flex items-center gap-1 pl-2 border-l border-slate-700">
                    <i class="fa-solid fa-lock text-[10px]"></i>
                    <span>Área Restrita (Servidores)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm" id="main-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Nome da Câmara -->
                <a href="<?= url('portal/index.php') ?>" class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-600 to-indigo-700 flex items-center justify-center text-white shadow-md shadow-sky-600/20">
                        <i class="fa-solid fa-landmark text-xl"></i>
                    </div>
                    <div>
                        <span class="text-lg sm:text-xl font-bold font-heading text-slate-900 block leading-tight">
                            <?= htmlspecialchars($currentTenant['name']) ?>
                        </span>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-sky-700">
                            Poder Legislativo Municipal
                        </span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden lg:flex items-center gap-1 text-xs font-semibold text-slate-700">
                    <a href="<?= url('portal/index.php') ?>" class="px-3 py-2 rounded-lg hover:text-sky-600 hover:bg-slate-50 transition-colors">Início</a>
                    <a href="<?= url('portal/leis.php') ?>" class="px-3 py-2 rounded-lg hover:text-sky-600 hover:bg-slate-50 transition-colors">Leis & Normas</a>
                    <a href="<?= url('portal/proposicoes.php') ?>" class="px-3 py-2 rounded-lg hover:text-sky-600 hover:bg-slate-50 transition-colors">Proposições</a>
                    <a href="<?= url('portal/sessoes.php') ?>" class="px-3 py-2 rounded-lg hover:text-sky-600 hover:bg-slate-50 transition-colors">Sessões Plenárias</a>
                    <a href="<?= url('portal/transparencia.php') ?>" class="px-3 py-2 rounded-lg hover:text-sky-600 hover:bg-slate-50 transition-colors text-emerald-700 font-bold bg-emerald-50 border border-emerald-100">
                        <i class="fa-solid fa-chart-pie mr-1"></i> Transparência
                    </a>
                    <a href="<?= url('portal/carta_servicos.php') ?>" class="px-3 py-2 rounded-lg hover:text-amber-700 hover:bg-amber-50 transition-colors text-amber-800 font-bold">
                        <i class="fa-solid fa-handshake-angle mr-1"></i> Serviços
                    </a>
                    <a href="<?= url('portal/ouvidoria.php') ?>" class="px-3 py-2 rounded-lg hover:text-purple-600 hover:bg-purple-50 transition-colors text-purple-700 font-bold">
                        <i class="fa-solid fa-headset mr-1"></i> Ouvidoria & SIC
                    </a>
                    <a href="<?= url('portal/validar.php') ?>" class="px-3 py-2 rounded-lg hover:text-slate-900 hover:bg-slate-100 transition-colors text-slate-600">
                        <i class="fa-solid fa-qrcode mr-1"></i> Validar
                    </a>
                    <a href="<?= url('ajuda.php') ?>" class="px-3 py-2 rounded-lg hover:text-sky-600 hover:bg-sky-50 transition-colors text-sky-700">
                        <i class="fa-solid fa-circle-question mr-1"></i> Ajuda
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="lg:hidden p-2 text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-2 text-xs font-semibold">
            <a href="<?= url('portal/index.php') ?>" class="block py-2 text-slate-800">Início</a>
            <a href="<?= url('portal/leis.php') ?>" class="block py-2 text-slate-800">Leis & Normas</a>
            <a href="<?= url('portal/proposicoes.php') ?>" class="block py-2 text-slate-800">Proposições</a>
            <a href="<?= url('portal/sessoes.php') ?>" class="block py-2 text-slate-800">Sessões Plenárias</a>
            <a href="<?= url('portal/transparencia.php') ?>" class="block py-2 text-emerald-700 font-bold">Transparência</a>
            <a href="<?= url('portal/carta_servicos.php') ?>" class="block py-2 text-amber-800 font-bold">Carta de Serviços</a>
            <a href="<?= url('portal/ouvidoria.php') ?>" class="block py-2 text-purple-700 font-bold">Ouvidoria & SIC</a>
            <a href="<?= url('portal/validar.php') ?>" class="block py-2 text-slate-600">Validar Documento</a>
            <a href="<?= url('ajuda.php') ?>" class="block py-2 text-sky-700 font-bold">Guia & Ajuda</a>
        </div>
    </header>

    <!-- Flash message se houver -->
    <?php if ($flash): ?>
        <div id="flash-message" class="w-full text-white text-xs font-semibold px-6 py-3 flex items-center justify-between <?= $flash['type'] === 'success' ? 'bg-emerald-600' : 'bg-rose-600' ?>">
            <div class="flex items-center gap-2 max-w-7xl mx-auto">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>

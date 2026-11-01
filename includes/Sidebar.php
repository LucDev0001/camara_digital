<?php
/**
 * Câmara Digital - Barra Lateral de Navegação (Sidebar)
 * Filtra dinamicamente os módulos de acordo com as permissões definidas pelo Administrador Geral
 */
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
function isActive(string $path, string $currentUri): string {
    return str_contains($currentUri, $path) 
        ? 'bg-sky-50 text-sky-700 font-semibold border-r-4 border-sky-600' 
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
}
?>
<!-- Sidebar Navigation -->
<aside id="sidebar" class="w-64 bg-white border-r border-slate-200 flex flex-col flex-shrink-0 transition-transform duration-300 lg:translate-x-0 -translate-x-full fixed lg:static inset-y-0 left-0 z-30 pt-16 lg:pt-0">
    <!-- Institution Banner in Sidebar -->
    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm shadow-sm">
                <i class="fa-solid fa-landmark"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-slate-800 truncate" title="<?= htmlspecialchars($currentTenant['name']) ?>">
                    <?= htmlspecialchars($currentTenant['name']) ?>
                </p>
                <p class="text-[11px] text-slate-600">
                    CNPJ: <?= formatCnpj($currentTenant['cnpj']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-1">
        <!-- Dashboard -->
        <a href="<?= url('dashboard.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('dashboard.php', $currentUri) ?>">
            <i class="fa-solid fa-gauge-high w-4 text-center"></i>
            <span>Painel Integrado</span>
        </a>

        <div class="pt-2 pb-1 px-3 flex items-center justify-between">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Módulos Habilitados</p>
            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600"><?= htmlspecialchars($currentUser['role_name'] ?? '') ?></span>
        </div>

        <?php if (Auth::canAccessModule('administracao')): ?>
        <!-- Área 1: Administração -->
        <a href="<?= url('modulos/administracao.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('administracao.php', $currentUri) ?>">
            <i class="fa-solid fa-building-user w-4 text-center"></i>
            <span>1. Administração & Acessos</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('legislativo')): ?>
        <!-- Área 2: Processo Legislativo -->
        <a href="<?= url('modulos/legislativo.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('legislativo.php', $currentUri) ?>">
            <i class="fa-solid fa-scale-balanced w-4 text-center text-sky-600"></i>
            <span>2. Processo Legislativo</span>
            <span class="ml-auto text-[10px] bg-sky-100 text-sky-700 font-bold px-1.5 py-0.5 rounded">Plenário</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('rh')): ?>
        <!-- Área 3: Recursos Humanos -->
        <a href="<?= url('modulos/rh.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('rh.php', $currentUri) ?>">
            <i class="fa-solid fa-id-card-clip w-4 text-center"></i>
            <span>3. Recursos Humanos (RH)</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('almoxarifado')): ?>
        <!-- Área 4: Almoxarifado -->
        <a href="<?= url('modulos/almoxarifado.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('almoxarifado.php', $currentUri) ?>">
            <i class="fa-solid fa-boxes-stacked w-4 text-center"></i>
            <span>4. Almoxarifado & Estoque</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('compras')): ?>
        <!-- Área 5: Compras e Contratos -->
        <a href="<?= url('modulos/compras.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('compras.php', $currentUri) ?>">
            <i class="fa-solid fa-file-contract w-4 text-center"></i>
            <span>5. Compras & Contratos</span>
            <span class="ml-auto text-[10px] bg-slate-100 text-slate-600 font-semibold px-1 rounded">PNCP</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('patrimonio')): ?>
        <!-- Área 6: Patrimônio e Frota -->
        <a href="<?= url('modulos/patrimonio.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('patrimonio.php', $currentUri) ?>">
            <i class="fa-solid fa-truck-ramp-box w-4 text-center"></i>
            <span>6. Patrimônio & Frota</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('financeiro')): ?>
        <!-- Área 7: Financeiro -->
        <a href="<?= url('modulos/financeiro.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('financeiro.php', $currentUri) ?>">
            <i class="fa-solid fa-hand-holding-dollar w-4 text-center"></i>
            <span>7. Financeiro & Orçamento</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('transparencia')): ?>
        <!-- Área 8: Transparência -->
        <a href="<?= url('modulos/transparencia.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('transparencia.php', $currentUri) ?>">
            <i class="fa-solid fa-chart-pie w-4 text-center"></i>
            <span>8. Gestão da Transparência</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('ouvidoria')): ?>
        <!-- Área 9: Ouvidoria e SIC -->
        <a href="<?= url('modulos/ouvidoria.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('ouvidoria.php', $currentUri) ?>">
            <i class="fa-solid fa-headset w-4 text-center"></i>
            <span>9. Ouvidoria & SIC</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('protocolo')): ?>
        <!-- Área 10: Protocolo -->
        <a href="<?= url('modulos/protocolo.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('protocolo.php', $currentUri) ?>">
            <i class="fa-solid fa-folder-tree w-4 text-center"></i>
            <span>10. Protocolo & Documentos</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('controle_interno')): ?>
        <!-- Área 11: Controle Interno -->
        <a href="<?= url('modulos/controle_interno.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('controle_interno.php', $currentUri) ?>">
            <i class="fa-solid fa-shield-halved w-4 text-center"></i>
            <span>11. Controle Interno & Riscos</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('noticias')): ?>
        <!-- Área 13: Comunicação & Notícias -->
        <a href="<?= url('modulos/noticias.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('noticias.php', $currentUri) ?>">
            <i class="fa-solid fa-newspaper w-4 text-center text-amber-600"></i>
            <span>Comunicação & Notícias</span>
            <span class="ml-auto text-[9px] bg-amber-100 text-amber-800 font-bold px-1.5 py-0.5 rounded">CMS</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::canAccessModule('ia_legislativa')): ?>
        <!-- Área 12: IA Legislativa & RAG -->
        <div class="pt-2 pb-1 px-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600">Diferencial Tecnológico</p>
        </div>
        <a href="<?= url('modulos/ia_legislativa.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('ia_legislativa.php', $currentUri) ?>">
            <i class="fa-solid fa-wand-magic-sparkles w-4 text-center text-purple-600"></i>
            <span class="font-semibold text-purple-900">12. IA Legislativa & RAG</span>
            <span class="ml-auto text-[9px] bg-purple-100 text-purple-700 font-bold px-1.5 py-0.5 rounded-full">Ativo</span>
        </a>
        <?php endif; ?>

        <?php if (Auth::hasRole('admin') || Auth::canAccessModule('backup')): ?>
        <div class="pt-2 pb-1 px-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Segurança & Licenciamento</p>
        </div>
        <?php if (Auth::hasRole('admin')): ?>
        <a href="<?= url('modulos/administracao.php#rbac-matrix') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs text-slate-700 hover:bg-slate-50 transition-colors">
            <i class="fa-solid fa-user-shield w-4 text-center text-indigo-600"></i>
            <span>Matriz de Acessos (RBAC)</span>
        </a>
        <a href="<?= url('modulos/administracao.php#license-guard') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs text-slate-700 hover:bg-slate-50 transition-colors">
            <i class="fa-solid fa-key w-4 text-center text-emerald-600"></i>
            <span>Licença & Trava Vercel</span>
        </a>
        <?php endif; ?>
        <a href="<?= url('modulos/backup.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('backup.php', $currentUri) ?>">
            <i class="fa-solid fa-database w-4 text-center text-rose-600"></i>
            <span class="font-semibold text-rose-900">Backup & Recuperação</span>
            <span class="ml-auto text-[9px] bg-rose-100 text-rose-700 font-bold px-1.5 py-0.5 rounded-full">DR</span>
        </a>
        <?php endif; ?>

        <!-- Ajuda & Documentação -->
        <div class="pt-2 pb-1 px-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ajuda & Conhecimento</p>
        </div>
        <a href="<?= url('ajuda.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('ajuda.php', $currentUri) ?>">
            <i class="fa-solid fa-circle-question w-4 text-center text-sky-600"></i>
            <span>Central de Ajuda (Guia)</span>
            <span class="ml-auto text-[9px] bg-sky-100 text-sky-700 font-bold px-1.5 py-0.5 rounded-full">FAQ</span>
        </a>
        <a href="<?= url('documentacao.php') ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-colors <?= isActive('documentacao.php', $currentUri) ?>">
            <i class="fa-solid fa-book-bookmark w-4 text-center text-indigo-600"></i>
            <span>Documentação Técnica</span>
            <span class="ml-auto text-[9px] bg-indigo-100 text-indigo-700 font-bold px-1.5 py-0.5 rounded-full">Devs</span>
        </a>
    </nav>

    <!-- Footer Links in Sidebar -->
    <div class="p-3 border-t border-slate-200 bg-slate-50 text-[11px] space-y-1">
        <a href="<?= url('portal/index.php') ?>" target="_blank" class="flex items-center justify-between text-slate-600 hover:text-sky-600 font-medium py-1 px-2 rounded hover:bg-slate-100 transition-colors">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-globe text-sky-600"></i>
                <span>Ver Portal Público</span>
            </span>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
        </a>
        <div class="px-2 py-1 flex items-center justify-between text-slate-600">
            <span>Licença Protegida</span>
            <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Vercel Guard
            </span>
        </div>
    </div>
</aside>

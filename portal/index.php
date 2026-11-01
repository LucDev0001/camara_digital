<?php
/**
 * Câmara Digital - Página Inicial do Portal Público do Cidadão
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Início — Portal Oficial';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Notícias Recentes
$news = $db->query("SELECT * FROM news WHERE tenant_id = {$tenantId} AND is_published = 1 ORDER BY published_at DESC LIMIT 3")->fetchAll();

// Próxima / Recente Sessão
$recentSession = $db->query("SELECT * FROM sessions WHERE tenant_id = {$tenantId} ORDER BY session_date DESC, start_time DESC LIMIT 1")->fetch();

// Vereadores da Legislatura
$councilors = $db->query("SELECT * FROM councilors WHERE tenant_id = {$tenantId} AND is_active = 1 ORDER BY board_role DESC, full_name ASC LIMIT 5")->fetchAll();

// Totais Públicos
$totalLaws = $db->query("SELECT COUNT(*) FROM legal_norms WHERE tenant_id = {$tenantId}")->fetchColumn();
$totalMatters = $db->query("SELECT COUNT(*) FROM legislative_matters WHERE tenant_id = {$tenantId}")->fetchColumn();
$totalContracts = $db->query("SELECT COUNT(*) FROM contracts WHERE tenant_id = {$tenantId} AND status = 'Vigente'")->fetchColumn();

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1">

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-white py-16 sm:py-24 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 text-xs font-semibold">
                <i class="fa-solid fa-building-columns text-[10px]"></i>
                <span>Poder Legislativo de <?= htmlspecialchars($currentTenant['city']) ?></span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight font-heading leading-tight">
                Transparência, Cidadania e <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-300">Democracia Digital</span>
            </h1>

            <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Acesse a legislação municipal, acompanhe as votações em plenário dos vereadores, consulte contas públicas e participe através da Ouvidoria e SIC.
            </p>

            <!-- Search Bar no Hero -->
            <form action="<?= url('portal/leis.php') ?>" method="GET" class="max-w-2xl mx-auto mt-6">
                <div class="flex items-center bg-white rounded-2xl p-2 shadow-2xl border border-white/20">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-base ml-3"></i>
                    <input type="text" name="q" placeholder="Pesquisar por assunto ou lei (ex: calçadas, energia solar, IPTU)..." 
                           class="flex-1 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none">
                    <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-xl shadow-md transition-colors">
                        Pesquisar Leis
                    </button>
                </div>
            </form>

            <div class="pt-4 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-300">
                <span><strong class="text-white font-bold font-mono"><?= $totalLaws ?></strong> Leis Vigentes</span>
                <span class="text-slate-600">•</span>
                <span><strong class="text-white font-bold font-mono"><?= $totalMatters ?></strong> Proposições</span>
                <span class="text-slate-600">•</span>
                <span><strong class="text-white font-bold font-mono"><?= $totalContracts ?></strong> Contratos Ativos</span>
            </div>
        </div>
    </section>

    <!-- Quick Access Services (6 Cards) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <!-- 1. Leis -->
            <a href="<?= url('portal/leis.php') ?>" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md card-hover text-center space-y-2 transition-all-200">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Leis & Normas</h3>
                <p class="text-[10px] text-slate-500">Consulta textual</p>
            </a>

            <!-- 2. Proposições -->
            <a href="<?= url('portal/proposicoes.php') ?>" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md card-hover text-center space-y-2 transition-all-200">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Projetos de Lei</h3>
                <p class="text-[10px] text-slate-500">Tramitações</p>
            </a>

            <!-- 3. Sessões -->
            <a href="<?= url('portal/sessoes.php') ?>" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md card-hover text-center space-y-2 transition-all-200">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-gavel"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Sessões Plenárias</h3>
                <p class="text-[10px] text-slate-500">Pautas e atas</p>
            </a>

            <!-- 4. Transparência -->
            <a href="<?= url('portal/transparencia.php') ?>" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md card-hover text-center space-y-2 transition-all-200">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Transparência</h3>
                <p class="text-[10px] text-slate-500">Contas públicas</p>
            </a>

            <!-- 5. Ouvidoria -->
            <a href="<?= url('portal/ouvidoria.php') ?>" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md card-hover text-center space-y-2 transition-all-200">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Ouvidoria & SIC</h3>
                <p class="text-[10px] text-slate-500">Manifestações</p>
            </a>

            <!-- 6. Validar -->
            <a href="<?= url('portal/validar.php') ?>" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md card-hover text-center space-y-2 transition-all-200">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Validador</h3>
                <p class="text-[10px] text-slate-500">Fé pública SHA-256</p>
            </a>
        </div>
    </section>

    <!-- Sessão Plenária e Notícias Recentes -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">

        <!-- Sessão em Destaque -->
        <?php if ($recentSession): ?>
            <div class="bg-gradient-to-r from-sky-900 via-indigo-900 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider bg-white/20 px-2.5 py-0.5 rounded-full text-sky-200">
                            Plenário das Deliberações
                        </span>
                        <span class="text-xs text-sky-300 font-semibold"><?= formatDate($recentSession['session_date']) ?> às <?= substr($recentSession['start_time'], 0, 5) ?></span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold font-heading text-white">
                        <?= $recentSession['session_number'] ?>ª Sessão <?= htmlspecialchars($recentSession['session_type']) ?>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-200 max-w-2xl">
                        <?= htmlspecialchars($recentSession['summary'] ?: 'Pauta da Ordem do Dia disponível para consulta com as proposições em apreciação pelos vereadores.') ?>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <?php if ($recentSession['video_url']): ?>
                        <a href="<?= htmlspecialchars($recentSession['video_url']) ?>" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs rounded-xl shadow-md transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-play text-xs"></i>
                            <span>Assistir Gravação</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('portal/sessoes.php') ?>" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 transition-colors">
                        Ver Pauta & Votações &rarr;
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Notícias Oficiais -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold font-heading text-slate-900">Notícias do Legislativo</h2>
                    <p class="text-xs text-slate-500">Acompanhe as principais ações e deliberações parlamentares</p>
                </div>
                <a href="<?= url('portal/noticias.php') ?>" class="text-xs font-semibold text-sky-600 hover:underline">Ver todas &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($news as $n): ?>
                    <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <img src="<?= htmlspecialchars($n['image_url'] ?: 'https://images.unsplash.com/photo-1577495508048-b635879837f1?w=600&auto=format&fit=crop&q=80') ?>" 
                             alt="<?= htmlspecialchars($n['title']) ?>" 
                             class="w-full h-44 object-cover">
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                                    <span class="font-bold text-sky-600 uppercase tracking-wider"><?= htmlspecialchars($n['category']) ?></span>
                                    <span><?= formatDate($n['published_at']) ?></span>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900 line-clamp-2"><?= htmlspecialchars($n['title']) ?></h3>
                                <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed"><?= htmlspecialchars($n['content']) ?></p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-400 text-[11px]">Por <?= htmlspecialchars($n['author_name']) ?></span>
                                <a href="<?= url('portal/noticias.php') ?>" class="text-sky-600 font-semibold hover:underline">Ler matéria &rarr;</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Vereadores da Legislatura Ativa -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold font-heading text-slate-900">Vereadores e Mesa Diretora</h2>
                    <p class="text-xs text-slate-500">Representantes eleitos da 11ª Legislatura Municipal</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <?php foreach ($councilors as $c): ?>
                    <div class="text-center p-3 rounded-xl border border-slate-100 hover:border-sky-300 transition-colors space-y-2">
                        <img src="<?= htmlspecialchars($c['photo_url'] ?: 'https://via.placeholder.com/150') ?>" 
                             alt="<?= htmlspecialchars($c['full_name']) ?>" 
                             class="w-20 h-20 rounded-2xl mx-auto object-cover border border-slate-200 shadow-sm">
                        <div>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider <?= $c['board_role'] !== 'Membro' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' ?>">
                                <?= htmlspecialchars($c['board_role']) ?>
                            </span>
                            <h4 class="font-bold text-xs text-slate-900 mt-1 truncate"><?= htmlspecialchars($c['full_name']) ?></h4>
                            <p class="text-[11px] font-semibold text-sky-600"><?= htmlspecialchars($c['political_party']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

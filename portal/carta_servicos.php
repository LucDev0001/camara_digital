<?php
/**
 * Câmara Digital - Carta de Serviços ao Cidadão (Lei Federal nº 13.460/2017)
 * Conforme Seção 26.3, 28.5 e 32 do Planejamento Mestre v1.2
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Carta de Serviços ao Cidadão';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Busca serviços cadastrados
$stmt = $db->prepare("SELECT * FROM carta_servicos WHERE tenant_id = ? AND is_active = 1 ORDER BY category ASC, service_name ASC");
$stmt->execute([$tenantId]);
$servicos = $stmt->fetchAll();

// Agrupamento por categoria
$categorias = [];
foreach ($servicos as $s) {
    $cat = $s['category'] ?: 'Geral';
    $categorias[$cat][] = $s;
}

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Hero Banner da Carta de Serviços -->
    <div class="rounded-2xl bg-gradient-to-r from-sky-800 to-indigo-900 text-white p-6 sm:p-10 shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-200 text-xs font-semibold uppercase tracking-wider">
                <i class="fa-solid fa-handshake-angle"></i>
                <span>Lei Federal nº 13.460/2017 • Direitos do Usuário de Serviços Públicos</span>
            </div>
            <h1 class="text-3xl font-extrabold font-heading tracking-tight">
                Carta de Serviços ao Cidadão
            </h1>
            <p class="text-sm text-sky-100 leading-relaxed">
                Este documento informa os serviços prestados pelo Poder Legislativo Municipal, as formas de acesso, prazos e compromissos com a transparência pública e o atendimento de qualidade.
            </p>
        </div>
        <div class="absolute -right-8 -bottom-8 text-8xl text-white/10 pointer-events-none">
            <i class="fa-solid fa-landmark"></i>
        </div>
    </div>

    <!-- Filtros de Busca Rápida -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="relative w-full sm:w-96">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input 
                type="text" 
                id="service-search-input" 
                placeholder="Pesquisar serviço (ex: certidão, leis, ouvidoria)..." 
                class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-sky-500"
                oninput="filterServices()"
            >
        </div>
        <div class="text-xs text-slate-500">
            Total de <strong><?= count($servicos) ?> serviços</strong> catalogados e disponíveis.
        </div>
    </div>

    <!-- Lista de Categorias e Serviços -->
    <div class="space-y-8" id="services-container">
        <?php foreach ($categorias as $catNome => $listaServicos): ?>
            <div class="service-category-group space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                    <i class="fa-solid fa-folder-open text-sky-600 text-sm"></i>
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider"><?= htmlspecialchars($catNome) ?></h2>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold ml-auto">
                        <?= count($listaServicos) ?>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($listaServicos as $servico): ?>
                        <div class="service-card bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4 hover:border-sky-300 transition-all flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-bold text-sm text-slate-900"><?= htmlspecialchars($servico['service_name']) ?></h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-sky-50 text-sky-700 border border-sky-100 whitespace-nowrap">
                                        <?= $servico['sla_days'] > 0 ? "Até {$servico['sla_days']} dias" : 'Imediato' ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed"><?= htmlspecialchars($servico['description']) ?></p>

                                <div class="p-3 bg-slate-50 rounded-lg space-y-1.5 text-xs text-slate-700">
                                    <div>
                                        <span class="font-bold text-slate-900">Público-alvo:</span>
                                        <span class="text-slate-600"><?= htmlspecialchars($servico['target_audience']) ?></span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900">Requisitos:</span>
                                        <span class="text-slate-600"><?= htmlspecialchars($servico['requirements']) ?></span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900">Etapas do serviço:</span>
                                        <span class="text-slate-600"><?= htmlspecialchars($servico['steps']) ?></span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900">Canais de atendimento:</span>
                                        <span class="text-slate-600"><?= htmlspecialchars($servico['service_channels']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-[11px] text-slate-400">Acompanhamento transparente</span>
                                <?php if (!empty($servico['tracking_channel'])): ?>
                                    <a href="<?= url($servico['tracking_channel']) ?>" class="text-sky-600 font-bold hover:text-sky-800 inline-flex items-center gap-1">
                                        <span>Acessar Serviço</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<script>
function filterServices() {
    const input = document.getElementById('service-search-input');
    const filter = input.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.service-card');
    const groups = document.querySelectorAll('.service-category-group');

    cards.forEach(card => {
        const text = card.innerText.toLowerCase();
        if (text.includes(filter)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });

    // Oculta grupos vazios
    groups.forEach(group => {
        const visibleCards = group.querySelectorAll('.service-card:not(.hidden)');
        if (visibleCards.length === 0) {
            group.classList.add('hidden');
        } else {
            group.classList.remove('hidden');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

<?php
/**
 * Câmara Digital - Notícias e Comunicação Oficial
 */
require_once __DIR__ . '/../config/app.php';

$publicTitle = 'Notícias Oficiais do Legislativo';
$tenantId = Tenant::id();
$db = Database::getConnection();

$news = $db->query("
    SELECT * FROM news 
    WHERE tenant_id = {$tenantId} AND is_published = 1 
    ORDER BY published_at DESC
")->fetchAll();

require_once __DIR__ . '/../includes/PublicHeader.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold font-heading text-slate-900">Comunicação & Notícias Oficiais</h1>
        <p class="text-xs sm:text-sm text-slate-500">Acompanhe as pautas, votações, audiências públicas e comunicados da Câmara Municipal.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($news as $n): ?>
            <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <img src="<?= htmlspecialchars($n['image_url'] ?: 'https://images.unsplash.com/photo-1577495508048-b635879837f1?w=600&auto=format&fit=crop&q=80') ?>" 
                     alt="<?= htmlspecialchars($n['title']) ?>" 
                     class="w-full h-48 object-cover">
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                            <span class="font-bold text-sky-600 uppercase tracking-wider bg-sky-50 px-2 py-0.5 rounded border border-sky-100">
                                <?= htmlspecialchars($n['category']) ?>
                            </span>
                            <span><?= formatDate($n['published_at']) ?></span>
                        </div>
                        <h3 class="font-bold text-base text-slate-900 leading-snug"><?= htmlspecialchars($n['title']) ?></h3>
                        <?php if ($n['subtitle']): ?>
                            <p class="text-xs font-semibold text-slate-600 mt-1"><?= htmlspecialchars($n['subtitle']) ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($n['content']) ?></p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-400">
                        <span>Publicado por <strong><?= htmlspecialchars($n['author_name']) ?></strong></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/PublicFooter.php'; ?>

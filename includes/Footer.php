<?php
/**
 * Câmara Digital - Rodapé Comum e Modais Globais
 */
?>
    </div> <!-- Close flex-1 flex overflow-hidden from Header.php -->

    <!-- Quick Search Modal (Ctrl + K) -->
    <div id="quick-search-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-start justify-center p-4 sm:p-6 lg:p-8 pt-20">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-lg"></i>
                <input type="text" id="global-search-input" placeholder="Buscar leis, proposições, servidores, processos ou atas..." 
                       class="w-full text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none"
                       onkeyup="handleGlobalSearch(this.value)">
                <button onclick="closeModal('quick-search-modal')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div id="global-search-results" class="max-h-96 overflow-y-auto p-4 space-y-2 text-xs">
                <div class="text-slate-600 text-center py-6">
                    <p class="font-medium">Digite ao menos 2 caracteres para pesquisar em toda a Câmara</p>
                    <p class="text-[11px] mt-1">Pesquise por termos como "energia", "primeira infância", "calçadas", "servidor" ou "contrato"</p>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600">
                <div class="flex items-center gap-2">
                    <kbd class="bg-white px-1.5 py-0.5 rounded border border-slate-200">ESC</kbd> <span>para fechar</span>
                </div>
                <div>
                    <span class="font-semibold text-sky-600"><?= htmlspecialchars($currentTenant['name']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"></div>

    <!-- Scripts -->
    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        // Sidebar Toggle for Mobile
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }

        // Global Search AJAX
        let searchTimeout = null;
        function handleGlobalSearch(query) {
            clearTimeout(searchTimeout);
            const resultsContainer = document.getElementById('global-search-results');
            if (!query || query.length < 2) {
                resultsContainer.innerHTML = `
                    <div class="text-slate-600 text-center py-6">
                        <p class="font-medium">Digite ao menos 2 caracteres para pesquisar em toda a Câmara</p>
                    </div>`;
                return;
            }

            searchTimeout = setTimeout(() => {
                resultsContainer.innerHTML = '<div class="text-center py-4 text-slate-600"><i class="fa-solid fa-spinner fa-spin text-base"></i> Pesquisando...</div>';
                fetch('<?= url("api/quick_search.php") ?>?q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        if (!data || data.length === 0) {
                            resultsContainer.innerHTML = '<div class="text-slate-600 text-center py-6">Nenhum resultado encontrado para esta consulta.</div>';
                            return;
                        }
                        let html = '';
                        data.forEach(item => {
                            html += `
                                <a href="${item.url}" class="block p-3 rounded-lg border border-slate-100 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700 bg-sky-100 px-2 py-0.5 rounded">${item.category}</span>
                                        <span class="text-slate-600 text-[11px]">${item.date || ''}</span>
                                    </div>
                                    <p class="font-bold text-slate-800 text-xs mt-1">${item.title}</p>
                                    <p class="text-slate-600 text-[11px] line-clamp-1 mt-0.5">${item.summary || ''}</p>
                                </a>
                            `;
                        });
                        resultsContainer.innerHTML = html;
                    })
                    .catch(() => {
                        resultsContainer.innerHTML = '<div class="text-rose-500 text-center py-4">Erro ao processar a pesquisa.</div>';
                    });
            }, 300);
        }
    </script>
</body>
</html>

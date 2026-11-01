<?php
/**
 * Câmara Digital - Central de Ajuda & Guia Interativo do Usuário (HTML)
 * Permite que qualquer usuário (servidor ou cidadão) tire dúvidas e aprenda a usar o sistema
 */
require_once __DIR__ . '/config/app.php';

$isLoggedIn = Auth::check();
$currentUser = $isLoggedIn ? Auth::user() : null;
$currentTenant = Tenant::current();
$pageTitle = 'Central de Ajuda & Guia do Usuário';

// Se autenticado, usa o layout padrão com Sidebar. Se não, usa cabeçalho limpo.
if ($isLoggedIn) {
    require_once __DIR__ . '/includes/Header.php';
    require_once __DIR__ . '/includes/Sidebar.php';
} else {
    $publicTitle = $pageTitle;
    require_once __DIR__ . '/includes/PublicHeader.php';
}
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto space-y-8">

        <!-- Hero Section com Campo de Busca em Tempo Real -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-700 via-sky-800 to-indigo-900 text-white p-6 sm:p-10 shadow-xl">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl text-white pointer-events-none">
                <i class="fa-solid fa-circle-question"></i>
            </div>
            <div class="relative z-10 max-w-2xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-200 text-xs font-semibold uppercase tracking-wider">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Base de Conhecimento & Manual Interativo</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold font-heading tracking-tight">
                    Como podemos ajudar você hoje?
                </h1>
                <p class="text-sm sm:text-base text-sky-100 leading-relaxed">
                    Tire dúvidas em tempo real sobre o uso do plenário virtual, protocolo, almoxarifado, compras, ouvidoria, transparência e recursos de Inteligência Artificial.
                </p>

                <!-- Search Input Instant Filter -->
                <div class="relative mt-2">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
                    <input 
                        type="text" 
                        id="guide-search-input" 
                        placeholder="Pesquise sua dúvida (ex: 'como votar no plenário', 'estorno de estoque', 'abrir ouvidoria')..." 
                        class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-800 placeholder-slate-400 rounded-xl shadow-lg text-sm font-medium focus:outline-none focus:ring-4 focus:ring-sky-400/30 transition-all"
                        oninput="filterGuideItems()"
                    >
                    <span id="search-counter" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-xs bg-sky-100 text-sky-800 font-bold px-2 py-1 rounded-md"></span>
                </div>
            </div>
        </div>

        <!-- Filtros Rápidos por Categorias -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none" id="category-pills">
            <button onclick="setCategoryFilter('all')" class="category-btn active px-4 py-2 rounded-xl text-xs font-bold transition-all bg-sky-600 text-white shadow-sm" data-category="all">
                <i class="fa-solid fa-layer-group mr-1.5"></i> Todos os Tópicos
            </button>
            <button onclick="setCategoryFilter('plenario')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="plenario">
                <i class="fa-solid fa-scale-balanced text-sky-600 mr-1.5"></i> Plenário & Votação
            </button>
            <button onclick="setCategoryFilter('legislativo')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="legislativo">
                <i class="fa-solid fa-file-signature text-emerald-600 mr-1.5"></i> Processo Legislativo
            </button>
            <button onclick="setCategoryFilter('ia')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="ia">
                <i class="fa-solid fa-wand-magic-sparkles text-purple-600 mr-1.5"></i> IA Legislativa
            </button>
            <button onclick="setCategoryFilter('almoxarifado')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="almoxarifado">
                <i class="fa-solid fa-boxes-stacked text-amber-600 mr-1.5"></i> Almoxarifado
            </button>
            <button onclick="setCategoryFilter('compras')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="compras">
                <i class="fa-solid fa-file-contract text-indigo-600 mr-1.5"></i> Compras (Lei 14.133)
            </button>
            <button onclick="setCategoryFilter('ouvidoria')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="ouvidoria">
                <i class="fa-solid fa-headset text-rose-600 mr-1.5"></i> Ouvidoria & SIC
            </button>
            <button onclick="setCategoryFilter('servicos')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="servicos">
                <i class="fa-solid fa-id-card-clip text-emerald-600 mr-1.5"></i> Carta de Serviços
            </button>
            <button onclick="setCategoryFilter('acessibilidade')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="acessibilidade">
                <i class="fa-solid fa-universal-access text-teal-600 mr-1.5"></i> Acessibilidade & Simples
            </button>
            <button onclick="setCategoryFilter('pntp')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="pntp">
                <i class="fa-solid fa-list-check text-cyan-600 mr-1.5"></i> Radar PNTP 2026
            </button>
            <button onclick="setCategoryFilter('backup')" class="category-btn px-4 py-2 rounded-xl text-xs font-semibold transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-100" data-category="backup">
                <i class="fa-solid fa-database text-rose-600 mr-1.5"></i> Backup & Recuperação
            </button>
        </div>

        <!-- Seção 1: Guia Passo a Passo por Perfil de Usuário -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-sky-600"></i>
                        <span>Guias Rápidos por Perfil de Acesso</span>
                    </h2>
                    <p class="text-xs text-slate-500">Selecione o seu perfil institucional para ver as instruções das suas atividades diárias.</p>
                </div>
            </div>

            <!-- Tab Buttons de Perfis -->
            <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3" id="profile-tabs">
                <button onclick="showProfileTab('vereador')" class="profile-tab active px-3.5 py-1.5 rounded-lg text-xs font-bold bg-sky-100 text-sky-800" data-profile="vereador">
                    <i class="fa-solid fa-user-tie mr-1"></i> Vereador / Parlamentar
                </button>
                <button onclick="showProfileTab('secretaria')" class="profile-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100" data-profile="secretaria">
                    <i class="fa-solid fa-clipboard-list mr-1"></i> Secretaria Legislativa
                </button>
                <button onclick="showProfileTab('almoxarifado_user')" class="profile-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100" data-profile="almoxarifado_user">
                    <i class="fa-solid fa-dolly mr-1"></i> Almoxarife
                </button>
                <button onclick="showProfileTab('compras_user')" class="profile-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100" data-profile="compras_user">
                    <i class="fa-solid fa-handshake mr-1"></i> Agente de Compras
                </button>
                <button onclick="showProfileTab('ouvidor_user')" class="profile-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100" data-profile="ouvidor_user">
                    <i class="fa-solid fa-comments mr-1"></i> Ouvidor / SIC
                </button>
                <button onclick="showProfileTab('cidadao')" class="profile-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100" data-profile="cidadao">
                    <i class="fa-solid fa-users mr-1"></i> Cidadão / Munícipe
                </button>
            </div>

            <!-- Conteúdo das Tabs de Perfil -->
            <div id="profile-content">
                <!-- Vereador -->
                <div id="profile-vereador" class="profile-pane space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-sky-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                            <h3 class="font-bold text-sm text-slate-900">Apresentar Nova Proposição</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Acesse <strong>Processo Legislativo</strong> e clique em <em>"Nova Proposição"</em>. Informe a ementa, o texto articulado e a justificativa. O sistema gera a numeração oficial e protocola a matéria automaticamente.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-purple-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                            <h3 class="font-bold text-sm text-slate-900">Usar o Redator de IA</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Acesse <strong>IA Legislativa</strong>. Digite sua ideia em linguagem natural e clique em <em>"Gerar Minuta"</em>. A IA estrutura a lei nos termos da Lei Complementar nº 95/98 com justificativa técnica.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                            <h3 class="font-bold text-sm text-slate-900">Votar no Plenário Virtual</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Durante a sessão plenária com votação aberta, clique no botão correspondente: <span class="text-emerald-700 font-bold">SIM</span>, <span class="text-rose-700 font-bold">NÃO</span> ou <span class="text-slate-700 font-bold">ABSTENÇÃO</span>. O voto é computado instantaneamente.
                            </p>
                        </div>
                    </div>
                    <?php if ($isLoggedIn): ?>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="<?= url('modulos/legislativo.php') ?>" class="text-xs font-bold text-sky-700 hover:text-sky-800 inline-flex items-center gap-1">
                            <span>Ir para Processo Legislativo</span> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <span class="text-slate-300">•</span>
                        <a href="<?= url('modulos/ia_legislativa.php') ?>" class="text-xs font-bold text-purple-700 hover:text-purple-800 inline-flex items-center gap-1">
                            <span>Acessar IA Legislativa</span> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Secretaria -->
                <div id="profile-secretaria" class="profile-pane hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                            <h3 class="font-bold text-sm text-slate-900">Elaborar Ordem do Dia</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                No módulo Legislativo, selecione a sessão e adicione as proposições que já obtiveram parecer das comissões para entrar na pauta de deliberação.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                            <h3 class="font-bold text-sm text-slate-900">Controlar Painel da Sessão</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Abra a sessão, registre a presença dos parlamentares para confirmação de quórum e libere a votação das matérias uma a uma.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                            <h3 class="font-bold text-sm text-slate-900">Publicar Atas e Resultados</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Ao encerrar a sessão, o placar nominal é consolidado automaticamente e disponibilizado de imediato no Portal da Transparência ao Cidadão.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Almoxarifado -->
                <div id="profile-almoxarifado_user" class="profile-pane hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-amber-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                            <h3 class="font-bold text-sm text-slate-900">Dar Entrada com NF</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Clique em <em>"Entrada de Material"</em>, informe a Nota Fiscal, quantidade e valor unitário. O estoque e o custo médio são recalculados na hora.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-amber-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                            <h3 class="font-bold text-sm text-slate-900">Atender Requisições</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Selecione o gabinete ou setor solicitante e dê saída. O sistema impede saídas maiores que o saldo em estoque, bloqueando saldo negativo.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-amber-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                            <h3 class="font-bold text-sm text-slate-900">Monitorar Ponto de Pedido</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Acompanhe os itens em alerta amarelo ou vermelho no painel de estoque mínimo para emitir pedidos de compra com antecedência.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Compras -->
                <div id="profile-compras_user" class="profile-pane hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                            <h3 class="font-bold text-sm text-slate-900">Fase Preparatória (ETP e TR)</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Cadastre o Estudo Técnico Preliminar e o Termo de Referência conforme os requisitos do Art. 18 da Nova Lei de Licitações (Lei 14.133/21).
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                            <h3 class="font-bold text-sm text-slate-900">Publicar Editais</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Lance o Pregão Eletrônico, Concorrência ou Dispensa com anexos, prazos de envio de propostas e critérios de julgamento.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                            <h3 class="font-bold text-sm text-slate-900">Gestão de Contratos</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Designe o servidor Fiscal do Contrato, registre termos aditivos e monitore a data de vigência para evitar interrupção de serviços essenciais.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ouvidoria -->
                <div id="profile-ouvidor_user" class="profile-pane hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-rose-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                            <h3 class="font-bold text-sm text-slate-900">Triagem de Demandas</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Acesse as manifestações recebidas pelo portal (Reclamações, Denúncias, Elogios, Pedidos LAI). Verifique o prazo limite de 20 dias úteis.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-rose-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                            <h3 class="font-bold text-sm text-slate-900">Despacho Interno</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Encaminhe a demanda ao setor competente (ex: Financeiro ou Almoxarifado) solicitando esclarecimentos ou providências formais.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-rose-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                            <h3 class="font-bold text-sm text-slate-900">Responder ao Munícipe</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Redija a resposta conclusiva ou registre prorrogação justificada por 10 dias. O cidadão visualiza a resposta pelo número de protocolo no portal.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cidadão -->
                <div id="profile-cidadao" class="profile-pane hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-sky-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                            <h3 class="font-bold text-sm text-slate-900">Consultar Leis & Contas</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                No Portal da Transparência, você pode consultar todos os gastos públicos, salários de servidores, leis promulgadas e votações nominais.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-sky-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                            <h3 class="font-bold text-sm text-slate-900">Enviar Pedido Fala.BR</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                No menu Ouvidoria, registre seu pedido de informação pela LAI ou envie denúncias e sugestões com opção de anonimato garantido por lei.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="w-6 h-6 rounded-full bg-sky-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                            <h3 class="font-bold text-sm text-slate-900">Validar Autenticidade</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Para certificar que um ofício ou documento é legítimo, use o Validador de Documentos inserindo o código Hash SHA-256 ou escaneando o QR-Code.
                            </p>
                        </div>
                    </div>
                    <div class="pt-2">
                        <a href="<?= url('portal/index.php') ?>" class="text-xs font-bold text-sky-700 hover:text-sky-800 inline-flex items-center gap-1">
                            <span>Ir para o Portal do Cidadão</span> <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção 2: Perguntas Frequentes (FAQ Interativo com Busca) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-sky-600"></i>
                        <span>Dúvidas Frequentes & Respostas Práticas</span>
                    </h2>
                    <p class="text-xs text-slate-500">Clique nas perguntas para expandir e ler a orientação detalhada.</p>
                </div>
                <button onclick="toggleAllFaq()" id="toggle-all-faq-btn" class="text-xs font-semibold text-sky-700 hover:text-sky-900 bg-sky-50 px-3 py-1.5 rounded-lg border border-sky-200 transition-colors">
                    Expandir Todas
                </button>
            </div>

            <!-- Lista de Itens do FAQ (Filtrável via JS) -->
            <div class="space-y-3" id="faq-list">

                <!-- FAQ Item 1: Plenário -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="plenario" data-keywords="votar votacao plenario vereador presenca quorum rejeicao">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </span>
                            <span>Como funciona o registro de presença e votação no Plenário Virtual?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>1. O operador da Secretaria ou Presidente abre a sessão plenária na aba <strong>Processo Legislativo</strong> ➜ <strong>Plenário</strong>.</p>
                        <p>2. Ao clicar em <em>"Iniciar Sessão"</em>, os vereadores presentes confirmam sua presença. O sistema calcula o quórum regimental automaticamente.</p>
                        <p>3. Quando o Presidente coloca uma proposição em votação, os botões <strong>SIM</strong>, <strong>NÃO</strong> e <strong>ABSTENÇÃO</strong> ficam ativos para os parlamentares.</p>
                        <p>4. O sistema grava o voto nominal no banco de dados e calcula a aprovação ou rejeição com base no quórum exigido (Maioria Simples, Absoluta ou 2/3).</p>
                    </div>
                </div>

                <!-- FAQ Item 2: IA RAG -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="ia" data-keywords="ia inteligencia artificial minuta projeto de lei ementa artigos lc 95 1998 rag semantica">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </span>
                            <span>Como a Inteligência Artificial redige projetos de lei respeitando a técnica legislativa?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>O motor de IA da plataforma foi desenvolvido com regras da <strong>Lei Complementar Federal nº 95/1998</strong>. Ele executa as seguintes etapas:</p>
                        <ul class="list-disc list-inside space-y-1 pl-2">
                            <li>Elabora a <strong>Ementa</strong> concisa indicando o objeto da proposição.</li>
                            <li>Estrutura o articulado com <code>Art. 1º</code>, incisos em algarismos romanos, alíneas e parágrafos.</li>
                            <li>Insere cláusula de vigência regulamentar e revogação.</li>
                            <li>Gera a <strong>Justificativa Técnica</strong> com fundamentação de competência municipal (Art. 30 da CF/88), facilitando a tramitação na CCJ.</li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ Item 3: Almoxarifado -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="almoxarifado" data-keywords="almoxarifado estoque saida requisicao saldo negativo trava atomica bloqueio">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </span>
                            <span>Por que o sistema impede requisições de saída com quantidade maior que o estoque?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>Para manter conformidade com as regras de auditoria do Tribunal de Contas, o sistema utiliza <strong>transações atômicas com bloqueio de concorrência (SELECT FOR UPDATE)</strong>. Se alguém tentar retirar uma quantidade superior ao estoque físico real cadastrado, a operação é rejeitada automaticamente, evitando saldos negativos ou distorção patrimonial.</p>
                    </div>
                </div>

                <!-- FAQ Item 4: Compras -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="compras" data-keywords="compras licitacoes 14133 14.133 etp tr termo de referencia pregao fiscal de contrato aditivo">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-file-contract"></i>
                            </span>
                            <span>O módulo de compras já está adaptado à Nova Lei de Licitações (Lei nº 14.133/2021)?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p><strong>Sim, 100% aderente.</strong> O módulo de compras contempla:</p>
                        <ul class="list-disc list-inside space-y-1 pl-2">
                            <li>Fase Preparatória com Estudo Técnico Preliminar (ETP) e Termo de Referência (TR).</li>
                            <li>Modalidades da nova lei: <em>Pregão Eletrônico</em>, <em>Concorrência</em>, <em>Dispensa</em> e <em>Inexigibilidade</em>.</li>
                            <li>Designação formal do <strong>Fiscal do Contrato</strong> com registro de relatórios de medição.</li>
                            <li>Controle de aditivos de valor e prorrogação de vigência.</li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ Item 5: Ouvidoria e LAI -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="ouvidoria" data-keywords="ouvidoria sic lai prazo 20 dias 10 dias sigilo anonimato recurso">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-headset"></i>
                            </span>
                            <span>Quais são os prazos legais de resposta na Ouvidoria e no SIC (LAI)?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>Conforme o Art. 11 da Lei Federal nº 12.527/2011 (LAI):</p>
                        <p>• O prazo padrão para resposta é de até <strong>20 (vinte) dias corridos</strong> a partir do registro do protocolo.</p>
                        <p>• Caso a demanda exija levantamentos complexos, o Ouvidor pode acionar o botão <em>"Prorrogar Prazo"</em> no sistema, justificando a dilação por mais <strong>10 (dez) dias</strong>.</p>
                        <p>• O sistema emite alerta visual quando faltam menos de 5 dias para o vencimento do prazo legal.</p>
                    </div>
                </div>

                <!-- FAQ Item 6: Protocolo SHA-256 -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="legislativo" data-keywords="protocolo autenticidade hash sha256 sha-256 qr code fe publica certidao">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-fingerprint"></i>
                            </span>
                            <span>Como funciona a verificação de fé pública documental com Hash SHA-256?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>No momento em que qualquer protocolo ou certidão é emitido, o sistema gera uma assinatura digital imutável calculada via algoritmo criptográfico <strong>SHA-256</strong> (uma cadeia única de 64 caracteres hexadecimais) associada a um <strong>QR-Code</strong>.</p>
                        <p>Qualquer cidadão, cartório ou órgão público pode acessar <code>portal/validar.php</code>, colar o Hash ou apontar a câmera do celular para atestar que o documento não sofreu adulteração.</p>
                    </div>
                </div>

                <!-- FAQ Item 7: Backup -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="backup" data-keywords="backup restauracao download seguranca mysqldump pdo recuperacao de desastres cron agendamento">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-database"></i>
                            </span>
                            <span>Como criar uma cópia de segurança (backup) e como funciona a restauração?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p><strong>Pela Web:</strong> Acesse o menu lateral <em>"Backup & Recuperação"</em> (restrito a administradores) e clique em <em>"Criar Backup Agora"</em>. O sistema gera o arquivo <code>.sql</code> auditado por SHA-256 e permite download imediato.</p>
                        <p><strong>Via Terminal / Agendador:</strong> Execute o comando <code>php backup.php --create "Backup Noturno"</code> para rotinas automatizadas no Windows Task Scheduler ou cron do Linux.</p>
                        <p><strong>Restauração:</strong> Na lista de backups, clique no ícone de restauração e confirme. O sistema limpa as tabelas existentes e reconstitui o banco com integridade total.</p>
                    </div>
                </div>

                <!-- FAQ Item 8: Multi-Tenant -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="legislativo" data-keywords="multitenant multi tenant trocar camara municipio cnpj isolamento">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-building-columns"></i>
                            </span>
                            <span>Como alternar entre Câmaras Municipais diferentes no sistema?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>No topo de qualquer tela do painel administrativo, há um seletor suspenso com as Câmaras cadastradas (ex: <em>Câmara Municipal de Exemplo</em> e <em>Câmara Municipal de Testes</em>).</p>
                        <p>Ao selecionar outro município, a sessão é atualizada instantaneamente e todos os dados passam a refletir exclusivamente a instituição selecionada, sem qualquer vazamento de informações.</p>
                    </div>
                </div>

                <!-- FAQ Item 9: Acessibilidade & Linguagem Simples -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="acessibilidade" data-keywords="acessibilidade linguagem simples lei 15263 alto contraste emag wcag fontes atalhos teclado alt 1 alt 2 alt 4">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-universal-access"></i>
                            </span>
                            <span>Como utilizar a Acessibilidade e a Linguagem Simples (Lei 15.263/2025)?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>1. <strong>Barra de Acessibilidade</strong>: No topo de qualquer página pública, use <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded">Alt+1</kbd> para ir ao conteúdo, <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded">Alt+2</kbd> para o menu e <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded">Alt+4</kbd> para alternar Alto Contraste.</p>
                        <p>2. <strong>Ajuste de Tipografia</strong>: Clique em <code>A+</code> para ampliar ou <code>A-</code> para reduzir os textos. A preferência é memorizada no seu navegador.</p>
                        <p>3. <strong>Linguagem Simples Cidadã</strong>: Em Leis e Proposições, selecione a aba <em>"Linguagem Simples"</em> para ler o que a matéria altera no cotidiano da cidade em palavras diretas, preservando o texto jurídico na aba oficial.</p>
                    </div>
                </div>

                <!-- FAQ Item 10: Carta de Serviços ao Cidadão -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="servicos" data-keywords="carta de servicos servicos cidadao lei 13460 catalogo requisitos prazos etapas sla">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </span>
                            <span>Como funciona a Carta de Serviços ao Cidadão (Lei Federal nº 13.460/2017)?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>Acesse <a href="portal/carta_servicos.php" class="text-sky-600 underline font-semibold">portal/carta_servicos.php</a> para visualizar o catálogo oficial de serviços públicos prestados pela Câmara Municipal.</p>
                        <p>Cada serviço apresenta com clareza: <strong>Público-alvo</strong>, <strong>Requisitos obrigatórios</strong>, <strong>Etapas do processo</strong>, <strong>Tempo estimado de atendimento (SLA)</strong> e <strong>Locais de atendimento</strong> (físico ou online).</p>
                    </div>
                </div>

                <!-- FAQ Item 11: Checklist PNTP 2026 -->
                <div class="faq-item bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all" data-category="pntp" data-keywords="pntp radar transparencia atricon tce auditoria criterios percentual evidencia url">
                    <button class="w-full p-4 text-left flex items-center justify-between gap-4 hover:bg-slate-50 focus:outline-none" onclick="toggleFaq(this)">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid fa-list-check"></i>
                            </span>
                            <span>Como a Câmara se prepara para a avaliação do PNTP 2026 (Radar da Transparência)?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div class="faq-content hidden px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2 bg-slate-50/50">
                        <p>No módulo <strong>Transparência</strong> (área administrativa), os gestores têm acesso ao <strong>Checklist PNTP 2026</strong> estruturado com 22 critérios fundamentais avaliados pela Atricon e Tribunais de Contas.</p>
                        <p>É possível monitorar o percentual preliminar de atendimento e cadastrar as URLs públicas de comprovação para cada dimensão, permitindo auditorias rápidas e transparentes sem induzir o cidadão a erro.</p>
                    </div>
                </div>

            </div>

            <!-- Estado Vazio quando a busca não encontra nada -->
            <div id="faq-empty-state" class="hidden text-center py-12 bg-white rounded-xl border border-slate-200 p-6 space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-base">Nenhum resultado encontrado</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Não encontramos tópicos com o termo digitado. Tente pesquisar por palavras-chave mais simples como <em>"votação"</em>, <em>"estoque"</em>, <em>"contrato"</em> ou <em>"ouvidoria"</em>.
                </p>
                <button onclick="clearSearch()" class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-bold rounded-lg border border-sky-200 transition-colors">
                    <i class="fa-solid fa-rotate-left"></i> Limpar pesquisa
                </button>
            </div>
        </div>

        <!-- Seção 3: Links Úteis e Documentação Técnica -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white space-y-3 shadow-md">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-slate-700 text-slate-200 text-[11px] font-bold">
                    <i class="fa-solid fa-code text-sky-400"></i>
                    <span>Área para Programadores</span>
                </div>
                <h3 class="text-lg font-bold">Documentação Técnica para Desenvolvedores</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Consulte os schemas das 34 tabelas, endpoints de API REST, pipeline de IA, fluxos de segurança e guias de extensibilidade para novos módulos.
                </p>
                <a href="<?= url('documentacao.php') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                    <span>Acessar Documentação Técnica HTML</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-sm">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 text-[11px] font-bold">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>Canais de Suporte</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Ainda precisa de esclarecimentos?</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Nossa equipe técnica e pedagógica presta suporte contínuo para garantir a soberania tecnológica e conformidade do seu Poder Legislativo.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 font-semibold bg-slate-100 px-3 py-1.5 rounded-lg">
                        <i class="fa-solid fa-envelope text-slate-500"></i> suporte@camaradigital.gov.br
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-700 font-semibold bg-slate-100 px-3 py-1.5 rounded-lg">
                        <i class="fa-solid fa-headset text-slate-500"></i> 0800 591 0000
                    </span>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Filtro Instantâneo por Texto
function filterGuideItems() {
    const input = document.getElementById('guide-search-input');
    const filter = input.value.toLowerCase().trim();
    const items = document.querySelectorAll('.faq-item');
    const counter = document.getElementById('search-counter');
    const emptyState = document.getElementById('faq-empty-state');
    let visibleCount = 0;

    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        const keywords = item.getAttribute('data-keywords') || '';
        const match = text.includes(filter) || keywords.includes(filter);

        if (match) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });

    if (filter.length > 0) {
        counter.classList.remove('hidden');
        counter.innerText = `${visibleCount} ${visibleCount === 1 ? 'resultado' : 'resultados'}`;
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    } else {
        counter.classList.add('hidden');
        emptyState.classList.add('hidden');
    }
}

function clearSearch() {
    const input = document.getElementById('guide-search-input');
    input.value = '';
    filterGuideItems();
    input.focus();
}

// Filtro por Categoria (Pills)
function setCategoryFilter(category) {
    document.querySelectorAll('.category-btn').forEach(btn => {
        if (btn.getAttribute('data-category') === category) {
            btn.classList.add('bg-sky-600', 'text-white', 'shadow-sm');
            btn.classList.remove('bg-white', 'text-slate-600');
        } else {
            btn.classList.remove('bg-sky-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'text-slate-600');
        }
    });

    const items = document.querySelectorAll('.faq-item');
    let visibleCount = 0;

    items.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        if (category === 'all' || itemCategory === category) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });

    document.getElementById('faq-empty-state').classList.add('hidden');
}

// Alternar FAQ (Accordion)
function toggleFaq(btn) {
    const content = btn.nextElementSibling;
    const icon = btn.querySelector('.fa-chevron-down');
    const isExpanded = !content.classList.contains('hidden');

    if (isExpanded) {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    } else {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    }
}

// Expandir / Recolher Todas
let allExpanded = false;
function toggleAllFaq() {
    allExpanded = !allExpanded;
    const btn = document.getElementById('toggle-all-faq-btn');
    document.querySelectorAll('.faq-item').forEach(item => {
        const content = item.querySelector('.faq-content');
        const icon = item.querySelector('.fa-chevron-down');
        if (allExpanded) {
            content.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    });
    btn.innerText = allExpanded ? 'Recolher Todas' : 'Expandir Todas';
}

// Alternar Tabs de Perfil
function showProfileTab(profileKey) {
    document.querySelectorAll('.profile-tab').forEach(tab => {
        if (tab.getAttribute('data-profile') === profileKey) {
            tab.classList.add('bg-sky-100', 'text-sky-800', 'font-bold');
            tab.classList.remove('text-slate-600');
        } else {
            tab.classList.remove('bg-sky-100', 'text-sky-800', 'font-bold');
            tab.classList.add('text-slate-600');
        }
    });

    document.querySelectorAll('.profile-pane').forEach(pane => {
        if (pane.id === `profile-${profileKey}`) {
            pane.classList.remove('hidden');
        } else {
            pane.classList.add('hidden');
        }
    });
}
</script>

<?php
if ($isLoggedIn) {
    require_once __DIR__ . '/includes/Footer.php';
} else {
    require_once __DIR__ . '/includes/PublicFooter.php';
}
?>

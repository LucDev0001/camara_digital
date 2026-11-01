<?php
/**
 * Câmara Digital - Portal de Documentação Técnica para Desenvolvedores (HTML)
 * Apresenta a arquitetura, modelo de dados das 34 tabelas, APIs, IA RAG e guia de desenvolvimento
 */
require_once __DIR__ . '/config/app.php';

$isLoggedIn = Auth::check();
$currentTenant = Tenant::current();
$pageTitle = 'Documentação Técnica da Engenharia';

// Se autenticado, usa o layout padrão com Sidebar. Se não, usa cabeçalho público.
if ($isLoggedIn) {
    require_once __DIR__ . '/includes/Header.php';
    require_once __DIR__ . '/includes/Sidebar.php';
} else {
    $publicTitle = $pageTitle;
    require_once __DIR__ . '/includes/PublicHeader.php';
}
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8">

        <!-- Sidebar de Navegação Rápida (Sticky no Desktop) -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="sticky top-20 bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-compass text-sky-600"></i> Sumário Técnico
                    </span>
                    <span class="text-[10px] bg-sky-100 text-sky-700 font-bold px-1.5 py-0.5 rounded">v1.0</span>
                </div>

                <!-- Input de Busca Rápida na Documentação -->
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input 
                        type="text" 
                        id="docs-search-input" 
                        placeholder="Filtrar tópicos..." 
                        class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
                        oninput="filterDocsSections()"
                    >
                </div>

                <nav class="space-y-1 text-xs font-medium" id="docs-toc-nav">
                    <a href="#arquitetura" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        1. Arquitetura & Stack
                    </a>
                    <a href="#diretorios" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        2. Estrutura de Pastas
                    </a>
                    <a href="#multitenant" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        3. Mecanismo Multi-Tenant
                    </a>
                    <a href="#banco" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        4. Banco de Dados (37 Tabelas)
                    </a>
                    <a href="#seguranca" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        5. Segurança & RBAC
                    </a>
                    <a href="#ia-rag" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        6. Motor de IA & RAG
                    </a>
                    <a href="#apis" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        7. Especificação de APIs
                    </a>
                    <a href="#backup-dr" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        8. Sistema de Backup (DR)
                    </a>
                    <a href="#extensibilidade" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        9. Criando Novos Módulos
                    </a>
                    <a href="#manutencao" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        10. Comandos & CLI
                    </a>
                    <a href="#conformidade" class="toc-link block px-2.5 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-700 transition-colors">
                        11. Conformidade v1.2 & PNTP
                    </a>
                </nav>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <a href="<?= url('ajuda.php') ?>" class="text-sky-600 hover:underline flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-arrow-left"></i> Ir para Central de Ajuda
                    </a>
                </div>
            </div>
        </aside>

        <!-- Área Principal de Conteúdo -->
        <article class="flex-1 space-y-10 min-w-0">

            <!-- Header da Documentação -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-sky-600 uppercase tracking-wider">
                    <i class="fa-solid fa-terminal"></i>
                    <span>Engenharia de Software Governamental</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-slate-900 tracking-tight">
                    Documentação Técnica da Plataforma Câmara Digital
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Referência técnica completa para engenheiros de software, administradores de sistemas e integradores. Detalha a arquitetura do servidor, isolamento de inquilinos, modelos relacionais, segurança e endpoints.
                </p>
                <div class="flex flex-wrap items-center gap-2 pt-2">
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-semibold">PHP 8.2+ Nativo</span>
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-semibold">MySQL 8.0+ / MariaDB</span>
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-semibold">Tailwind CSS</span>
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-semibold">Vanilla JS (No Framework)</span>
                    <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-mono font-semibold">Tempo de Resposta &lt; 100ms</span>
                </div>
            </div>

            <!-- Seção 1: Arquitetura & Stack -->
            <section id="arquitetura" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">01</span>
                        <span>Arquitetura de Software e Filosofia</span>
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    A plataforma adota o padrão <strong>Modular MVC sem acoplamento a frameworks pesados</strong> (como Laravel ou Symfony). Essa decisão de engenharia reduz o consumo de memória para menos de 4 MB por requisição, viabilizando execução em servidores compartilhados ou instâncias enxutas em nuvem com alta capacidade de concorrência.
                </p>
                <div class="p-4 rounded-xl bg-slate-900 text-slate-200 font-mono text-xs overflow-x-auto leading-relaxed">
                    <pre>
┌────────────────────────────────────────────────────────┐
│             PORTAL PÚBLICO & PAINEL DE CONTROLE        │
│          Tailwind CSS + FontAwesome + Vanilla JS       │
└───────────────────────────┬────────────────────────────┘
                            │ Fetch AJAX / HTTP POST (CSRF)
┌───────────────────────────▼────────────────────────────┐
│              ROTEAMENTO E CONTROLADORES                │
│    /portal/*.php  |  /modulos/*.php  |  /api/*.php     │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│               CAMADA DE SERVIÇOS CORE                  │
│   Auth.php  |  Tenant.php  |  AIAssistant.php          │
│   Audit.php |  Backup.php  |  Database.php             │
└───────────────────────────┬────────────────────────────┘
                            │ PDO (utf8mb4_unicode_ci)
┌───────────────────────────▼────────────────────────────┐
│         BANCO DE DADOS RELACIONAL (34 TABELAS)         │
│               MySQL 8.0 / InnoDB Multi-Tenant          │
└────────────────────────────────────────────────────────┘</pre>
                </div>
            </section>

            <!-- Seção 2: Estrutura de Pastas -->
            <section id="diretorios" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">02</span>
                        <span>Estrutura de Diretórios e Arquivos</span>
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    O ecossistema é estritamente separado em diretórios funcionais para facilitar manutenção e auditoria de código:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-mono font-bold text-sky-700">/config/</span>
                        <p class="text-slate-500">Conexão PDO singleton (<code>database.php</code>) e helpers globais, CSRF e sanitização (<code>app.php</code>).</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-mono font-bold text-sky-700">/includes/</span>
                        <p class="text-slate-500">Classes de serviço do backend: <code>Auth.php</code>, <code>Tenant.php</code>, <code>AIAssistant.php</code>, <code>Backup.php</code> e <code>Audit.php</code>.</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-mono font-bold text-sky-700">/modulos/</span>
                        <p class="text-slate-500">As 12 áreas institucionais (Legislativo, Plenário, Compras, RH, Almoxarifado, Transparência, etc.).</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-mono font-bold text-sky-700">/portal/</span>
                        <p class="text-slate-500">Páginas de acesso livre do munícipe (leis, proposições, sessões, transparência e ouvidoria).</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-mono font-bold text-sky-700">/api/</span>
                        <p class="text-slate-500">Endpoints AJAX/REST com respostas JSON (votação, estoque, RAG, despachos, alternância de tenant).</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-mono font-bold text-sky-700">/backups/</span>
                        <p class="text-slate-500">Armazenamento isolado de arquivos <code>.sql</code> protegido por diretiva <code>Deny from all</code>.</p>
                    </div>
                </div>
            </section>

            <!-- Seção 3: Mecanismo Multi-Tenant -->
            <section id="multitenant" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">03</span>
                        <span>Mecanismo Multi-Tenant (Isolamento de Dados)</span>
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    O sistema utiliza a estratégia <strong>Single-Database Multi-Tenant with Shared Schema</strong>. Todas as tabelas que contêm dados municipais possuem a coluna <code>tenant_id</code> com integridade referencial ligada à tabela principal <code>tenants</code>.
                </p>
                <div class="relative bg-slate-900 rounded-xl p-4 text-slate-200 font-mono text-xs">
                    <button onclick="copyCode(this)" class="absolute top-3 right-3 text-slate-400 hover:text-white bg-slate-800 px-2 py-1 rounded text-[11px] border border-slate-700">
                        <i class="fa-regular fa-copy"></i> Copiar
                    </button>
                    <pre><code class="language-php">// Exemplo de isolamento obrigatório na camada de dados:
$tenantId = Tenant::id(); // Obtém da sessão segura
$stmt = $pdo->prepare("SELECT * FROM proposicoes WHERE tenant_id = ? AND status = ?");
$stmt->execute([$tenantId, 'em_tramitacao']);
$proposicoes = $stmt->fetchAll();</code></pre>
                </div>
            </section>

            <!-- Seção 4: Banco de Dados (37 Tabelas) -->
            <section id="banco" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">04</span>
                        <span>Dicionário do Banco de Dados Relacional (37 Tabelas)</span>
                    </h2>
                    <span class="text-xs font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600 font-bold">InnoDB utf8mb4</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    O esquema relacional é estruturado em 12 módulos funcionais e núcleos de conformidade com integridade de chaves estrangeiras:
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Nome da Tabela</th>
                                <th class="p-3">Módulo Funcional</th>
                                <th class="p-3">Finalidade Técnica</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-600">
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">01</td><td class="p-2.5 font-bold font-mono text-slate-900">tenants</td><td class="p-2.5">Core</td><td class="p-2.5">Câmaras cadastradas, CNPJ, município e brasão</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">02</td><td class="p-2.5 font-bold font-mono text-slate-900">users</td><td class="p-2.5">Core / Auth</td><td class="p-2.5">Usuários, senhas BCRYPT e papéis de acesso (RBAC)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">03</td><td class="p-2.5 font-bold font-mono text-slate-900">mesa_diretora</td><td class="p-2.5">1. Administração</td><td class="p-2.5">Membros da mesa por biênio e legislatura</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">04</td><td class="p-2.5 font-bold font-mono text-slate-900">comissoes</td><td class="p-2.5">1. Administração</td><td class="p-2.5">Comissões permanentes (CCJ, Finanças) e temporárias</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">05</td><td class="p-2.5 font-bold font-mono text-slate-900">comissoes_membros</td><td class="p-2.5">1. Administração</td><td class="p-2.5">Vinculação dos parlamentares a cada comissão</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">06</td><td class="p-2.5 font-bold font-mono text-slate-900">atos_oficiais</td><td class="p-2.5">1. Administração</td><td class="p-2.5">Decretos legislativos, portarias e resoluções</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">07</td><td class="p-2.5 font-bold font-mono text-slate-900">vereadores</td><td class="p-2.5">2. Legislativo</td><td class="p-2.5">Perfil parlamentar, partido, biografia e foto</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">08</td><td class="p-2.5 font-bold font-mono text-slate-900">proposicoes</td><td class="p-2.5">2. Legislativo</td><td class="p-2.5">Projetos de Lei, Indicações, Requerimentos e Moções</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">09</td><td class="p-2.5 font-bold font-mono text-slate-900">proposicoes_tramit</td><td class="p-2.5">2. Legislativo</td><td class="p-2.5">Histórico e árvore cronológica de tramitação</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">10</td><td class="p-2.5 font-bold font-mono text-slate-900">sessoes_plenarias</td><td class="p-2.5">2. Plenário</td><td class="p-2.5">Sessões ordinárias e extraordinárias convocadas</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">11</td><td class="p-2.5 font-bold font-mono text-slate-900">sessoes_pautas</td><td class="p-2.5">2. Plenário</td><td class="p-2.5">Matérias da Ordem do Dia para deliberação</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">12</td><td class="p-2.5 font-bold font-mono text-slate-900">plen_votacoes</td><td class="p-2.5">2. Plenário</td><td class="p-2.5">Registro nominal do voto (SIM, NAO, ABSTENCAO)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">13</td><td class="p-2.5 font-bold font-mono text-slate-900">rh_servidores</td><td class="p-2.5">3. Recursos Humanos</td><td class="p-2.5">Quadro funcional (efetivos, comissionados, agentes)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">14</td><td class="p-2.5 font-bold font-mono text-slate-900">rh_cargos</td><td class="p-2.5">3. Recursos Humanos</td><td class="p-2.5">Plano de cargos, vencimentos e padrões</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">15</td><td class="p-2.5 font-bold font-mono text-slate-900">rh_folha</td><td class="p-2.5">3. Recursos Humanos</td><td class="p-2.5">Folha mensal e contracheques para a transparência</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">16</td><td class="p-2.5 font-bold font-mono text-slate-900">almox_categorias</td><td class="p-2.5">4. Almoxarifado</td><td class="p-2.5">Grupos de materiais de consumo e suprimentos</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">17</td><td class="p-2.5 font-bold font-mono text-slate-900">almox_itens</td><td class="p-2.5">4. Almoxarifado</td><td class="p-2.5">Itens, estoque físico atual e ponto de reposição</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">18</td><td class="p-2.5 font-bold font-mono text-slate-900">almox_movim</td><td class="p-2.5">4. Almoxarifado</td><td class="p-2.5">Movimentações atômicas de entrada e saída</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">19</td><td class="p-2.5 font-bold font-mono text-slate-900">compras_processos</td><td class="p-2.5">5. Compras (14.133)</td><td class="p-2.5">Processos licitatórios (Pregão, Dispensa, etc.)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">20</td><td class="p-2.5 font-bold font-mono text-slate-900">compras_fases</td><td class="p-2.5">5. Compras (14.133)</td><td class="p-2.5">Fases: ETP, Termo de Referência, Julgamento</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">21</td><td class="p-2.5 font-bold font-mono text-slate-900">contratos</td><td class="p-2.5">5. Compras (14.133)</td><td class="p-2.5">Contratos administrativos, vigência e valores</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">22</td><td class="p-2.5 font-bold font-mono text-slate-900">contratos_aditivos</td><td class="p-2.5">5. Compras (14.133)</td><td class="p-2.5">Termos aditivos de prazo e reequilíbrio de valor</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">23</td><td class="p-2.5 font-bold font-mono text-slate-900">patrimonio_bens</td><td class="p-2.5">6. Patrimônio</td><td class="p-2.5">Bens tombados com número de plaqueta e depreciação</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">24</td><td class="p-2.5 font-bold font-mono text-slate-900">frota_veiculos</td><td class="p-2.5">6. Frota</td><td class="p-2.5">Veículos oficiais da Câmara (placa, modelo, km)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">25</td><td class="p-2.5 font-bold font-mono text-slate-900">frota_viagens</td><td class="p-2.5">6. Frota</td><td class="p-2.5">Missões oficiais e controle de abastecimento</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">26</td><td class="p-2.5 font-bold font-mono text-slate-900">fin_duodecimo</td><td class="p-2.5">7. Financeiro</td><td class="p-2.5">Repasses mensais recebidos do Executivo (CF/88 29-A)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">27</td><td class="p-2.5 font-bold font-mono text-slate-900">fin_despesas</td><td class="p-2.5">7. Financeiro</td><td class="p-2.5">Empenho, liquidação e pagamento de fornecedores</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">28</td><td class="p-2.5 font-bold font-mono text-slate-900">transp_publicacoes</td><td class="p-2.5">8. Transparência</td><td class="p-2.5">Relatórios fiscais de gestão (RGF) e dados abertos</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">29</td><td class="p-2.5 font-bold font-mono text-slate-900">ouvidoria_manif</td><td class="p-2.5">9. Ouvidoria / SIC</td><td class="p-2.5">Manifestações do cidadão (Fala.BR e LAI 12.527)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">30</td><td class="p-2.5 font-bold font-mono text-slate-900">ouvidoria_respostas</td><td class="p-2.5">9. Ouvidoria / SIC</td><td class="p-2.5">Respostas formais e prorrogações do Ouvidor</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">31</td><td class="p-2.5 font-bold font-mono text-slate-900">protocolos</td><td class="p-2.5">10. Protocolo Geral</td><td class="p-2.5">Protocolos emitidos e cálculo do Hash SHA-256</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">32</td><td class="p-2.5 font-bold font-mono text-slate-900">protocolo_tramit</td><td class="p-2.5">10. Protocolo Geral</td><td class="p-2.5">Despachos e movimentações entre setores</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">33</td><td class="p-2.5 font-bold font-mono text-slate-900">ctrl_riscos</td><td class="p-2.5">11. Controle Interno</td><td class="p-2.5">Matriz de riscos e apontamentos de auditoria</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">34</td><td class="p-2.5 font-bold font-mono text-slate-900">audit_logs</td><td class="p-2.5">11. Trilha de Auditoria</td><td class="p-2.5">Registro imutável de todas as ações no sistema</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">35</td><td class="p-2.5 font-bold font-mono text-slate-900">tenant_compliance_profiles</td><td class="p-2.5">1. Governança</td><td class="p-2.5">Perfil de conformidade jurídica, LOM, Regimento e Diário Oficial</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">36</td><td class="p-2.5 font-bold font-mono text-slate-900">pntp_checklist</td><td class="p-2.5">8. Transparência Ativa</td><td class="p-2.5">Critérios de avaliação PNTP 2026 (Radar da Atricon / TCE)</td></tr>
                            <tr class="hover:bg-slate-50"><td class="p-2.5 font-mono">37</td><td class="p-2.5 font-bold font-mono text-slate-900">carta_servicos</td><td class="p-2.5">9. Carta ao Cidadão</td><td class="p-2.5">Catálogo de serviços públicos e prazos (Lei 13.460/2017)</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Seção 5: Segurança & RBAC -->
            <section id="seguranca" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">05</span>
                        <span>Camada de Segurança, Autenticação e RBAC</span>
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    A plataforma aplica os mais altos padrões de segurança cibernética preconizados pelo OWASP:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                        <span class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-key text-sky-600"></i> Hashing de Senhas
                        </span>
                        <p class="text-slate-600">Senhas protegidas via <code>password_hash($pwd, PASSWORD_BCRYPT, ['cost' => 10])</code>. O sistema não armazena senhas em texto puro em nenhuma hipótese.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                        <span class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-rose-600"></i> Proteção Anti-CSRF
                        </span>
                        <p class="text-slate-600">Cada formulário POST emite um token único gerado com <code>bin2hex(random_bytes(32))</code>, verificado rigorosamente no processamento.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                        <span class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-database text-emerald-600"></i> Prevenção contra SQL Injection
                        </span>
                        <p class="text-slate-600">100% das consultas ao banco utilizam <strong>PDO Prepared Statements</strong> com passagem de parâmetros por array. Nenhuma variável é concatenada em queries.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                        <span class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-lock text-indigo-600"></i> RBAC Granular
                        </span>
                        <p class="text-slate-600">Validação estrita de papéis no cabeçalho das páginas via <code>Auth::requireRole('admin')</code> ou <code>Auth::requireAuth()</code>.</p>
                    </div>
                </div>
            </section>

            <!-- Seção 6: Motor de IA & RAG -->
            <section id="ia-rag" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-mono">06</span>
                        <span>Motor de IA Legislativa & Pipeline RAG</span>
                    </h2>
                    <span class="text-xs font-mono bg-purple-100 text-purple-800 font-bold px-2 py-0.5 rounded">AIAssistant.php</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    A classe <code>includes/AIAssistant.php</code> implementa o pipeline de Recuperação Aumentada por Geração (RAG). Ela realiza a tokenização léxica da consulta em linguagem natural do usuário, executa busca ponderada no banco de normas jurídicas da Câmara e constrói o contexto para geração de minutas conforme a <strong>LC 95/1998</strong>.
                </p>
                <div class="relative bg-slate-900 rounded-xl p-4 text-slate-200 font-mono text-xs">
                    <button onclick="copyCode(this)" class="absolute top-3 right-3 text-slate-400 hover:text-white bg-slate-800 px-2 py-1 rounded text-[11px] border border-slate-700">
                        <i class="fa-regular fa-copy"></i> Copiar
                    </button>
                    <pre><code class="language-php">// Chamada interna do pipeline RAG:
$resultado = AIAssistant::draftBill([
    'theme' => 'Incentivo ao plantio de árvores nativas em calçadas públicas',
    'type' => 'Projeto de Lei Ordinária',
    'tenant_id' => Tenant::id()
]);
// Retorna: array com 'title', 'ementa', 'artigos', 'justificativa' e 'conformidade_lc95'</code></pre>
                </div>
            </section>

            <!-- Seção 7: Especificação de APIs -->
            <section id="apis" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">07</span>
                        <span>Especificação das APIs REST e Endpoints AJAX</span>
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Todos os endpoints residem em <code>/api/</code> e respondem em JSON com cabeçalho <code>Content-Type: application/json</code>:
                </p>

                <!-- Cards de Endpoints -->
                <div class="space-y-3">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-mono font-bold text-[11px]">POST</span>
                            <span class="font-mono text-xs font-bold text-slate-800">/api/vote_plenary.php</span>
                        </div>
                        <p class="text-xs text-slate-600">Registra o voto nominal de um vereador em uma matéria de plenário em andamento.</p>
                        <p class="text-[11px] font-mono text-slate-500">Payload: <code>{ "sessao_id": 1, "proposicao_id": 14, "vereador_id": 3, "voto": "SIM" }</code></p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-mono font-bold text-[11px]">POST</span>
                            <span class="font-mono text-xs font-bold text-slate-800">/api/stock_action.php</span>
                        </div>
                        <p class="text-xs text-slate-600">Movimentação atômica de entrada ou requisição de saída de materiais do almoxarifado.</p>
                        <p class="text-[11px] font-mono text-slate-500">Payload: <code>{ "action": "out", "item_id": 2, "quantity": 5, "destination": "Gabinete 01" }</code></p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-mono font-bold text-[11px]">POST</span>
                            <span class="font-mono text-xs font-bold text-slate-800">/api/ai_assistant.php</span>
                        </div>
                        <p class="text-xs text-slate-600">Geração de minutas, busca semântica RAG e sumarização executiva da Ordem do Dia.</p>
                        <p class="text-[11px] font-mono text-slate-500">Payload: <code>{ "action": "draft_bill", "theme": "Hortas urbanas", "type": "PL" }</code></p>
                    </div>
                </div>
            </section>

            <!-- Seção 8: Sistema de Backup (DR) -->
            <section id="backup-dr" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-sm font-mono">08</span>
                        <span>Engenharia do Sistema de Backup & Disaster Recovery</span>
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    O motor de backup (<code>includes/Backup.php</code>) opera em regime híbrido de alta disponibilidade:
                </p>
                <ul class="list-disc list-inside text-xs text-slate-600 space-y-1.5 pl-2">
                    <li><strong>Método 1 (mysqldump)</strong>: Executa o binário do MySQL com flags transacionais completas (<code>--routines --triggers --single-transaction --default-character-set=utf8mb4</code>).</li>
                    <li><strong>Método 2 (Dumper Nativo em PHP PDO)</strong>: Ativado automaticamente se o executável do MySQL não estiver disponível no servidor. Gera o DDL completo (<code>SHOW CREATE TABLE</code>) e exporta os dados paginados em lotes com tratamento contra injeção via <code>$pdo->quote()</code>.</li>
                    <li><strong>Integridade Criptográfica SHA-256</strong>: Cada backup gerado gera um arquivo <code>.json</code> correspondente com o checksum SHA-256 e o carimbo de data/hora imutável.</li>
                </ul>
            </section>

            <!-- Seção 9: Extensibilidade -->
            <section id="extensibilidade" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">09</span>
                        <span>Guia de Extensibilidade: Criando um Novo Módulo</span>
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Para adicionar uma nova funcionalidade ao sistema, siga este ciclo de 4 etapas:
                </p>
                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <p class="font-bold text-slate-900">1. Criar a Tabela Relacional com <code>tenant_id</code>:</p>
                        <p class="text-slate-600 mt-1">Insira a instrução <code>CREATE TABLE</code> com chave estrangeira apontando para <code>tenants(id) ON DELETE CASCADE</code>.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <p class="font-bold text-slate-900">2. Criar a Interface em <code>modulos/novo_modulo.php</code>:</p>
                        <p class="text-slate-600 mt-1">Inclua <code>Auth::requireRole()</code>, <code>Tenant::id()</code> e invoque <code>includes/Header.php</code> e <code>includes/Sidebar.php</code>.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <p class="font-bold text-slate-900">3. Registrar as Modificações na Trilha de Auditoria:</p>
                        <p class="text-slate-600 mt-1">Sempre que dados forem criados ou alterados, chame <code>Audit::log('ACAO', 'modulo', $id, 'Descricao')</code>.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <p class="font-bold text-slate-900">4. Adicionar o Link na Sidebar:</p>
                        <p class="text-slate-600 mt-1">Insira o item no menu de navegação em <code>includes/Sidebar.php</code>.</p>
                    </div>
                </div>
            </section>

            <!-- Seção 10: Comandos & CLI -->
            <section id="manutencao" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">10</span>
                        <span>Comandos de Manutenção via Terminal (CLI)</span>
                    </h2>
                </div>
                <div class="relative bg-slate-900 rounded-xl p-4 text-slate-200 font-mono text-xs">
                    <button onclick="copyCode(this)" class="absolute top-3 right-3 text-slate-400 hover:text-white bg-slate-800 px-2 py-1 rounded text-[11px] border border-slate-700">
                        <i class="fa-regular fa-copy"></i> Copiar
                    </button>
                    <pre><code class="language-powershell"># Gerar backup do banco via linha de comando (para cron / Task Scheduler):
php backup.php --create "Backup Noturno Diario"

# Listar todos os backups e hashes SHA-256 no terminal:
php backup.php --list

# Restaurar banco de dados a partir de um backup específico:
php backup.php --restore backup_camara_exemplo.sql

# Executar instalador e recarregar dados de demonstração:
php install.php</code></pre>
                </div>
            </section>

            <!-- Seção 11: Conformidade Regulatória v1.2, PNTP 2026 & Linguagem Simples -->
            <section id="conformidade" class="doc-section bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-xl font-bold font-heading text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-mono">11</span>
                        <span>Conformidade Regulatória v1.2, PNTP 2026 & Linguagem Simples</span>
                    </h2>
                    <span class="text-xs font-mono bg-emerald-100 px-2 py-0.5 rounded text-emerald-800 font-bold">Leis 15.263/25 e 14.063/20</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-600">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-feather text-sky-600"></i>
                            Política de Linguagem Simples (Lei 15.263/2025)
                        </h3>
                        <p class="leading-relaxed">
                            O texto normativo original e oficial <strong>não é substituído</strong>. A plataforma renderiza o resumo simplificado ao lado do texto jurídico em abas dinâmicas, com geração assistida por IA e gravação no schema nas colunas <code class="font-mono bg-white px-1 py-0.5 rounded border border-slate-200">plain_language_summary</code>.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-file-shield text-indigo-600"></i>
                            Níveis de Assinatura Eletrônica (Lei 14.063/2020)
                        </h3>
                        <p class="leading-relaxed">
                            Parametrização formal em 3 níveis: <strong>Simples</strong> (login municipal e senha), <strong>Avançada</strong> (certificado institucional e hash SHA-256) e <strong>Qualificada</strong> (ICP-Brasil). Configurado no trâmite legislativo.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-emerald-600"></i>
                            Checklist PNTP 2026 (Radar da Transparência)
                        </h3>
                        <p class="leading-relaxed">
                            Matriz de 22 dimensões com percentual preliminar de atendimento por tenant e links de comprovação pública direta. O sistema veda expressamente selos falsos de aprovação, mantendo foco técnico de preparação.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-universal-access text-purple-600"></i>
                            Barra de Acessibilidade Governamental
                        </h3>
                        <p class="leading-relaxed">
                            Aderência ao eMAG e WCAG 2.1 AA com atalhos de teclado (<kbd class="px-1 bg-white border border-slate-200 rounded">Alt+1</kbd>, <kbd class="px-1 bg-white border border-slate-200 rounded">Alt+2</kbd>, <kbd class="px-1 bg-white border border-slate-200 rounded">Alt+4</kbd>), modo Alto Contraste de alto impacto e ajuste tipográfico com persistência local.
                        </p>
                    </div>
                </div>
            </section>

        </article>

    </div>
</main>

<script>
// Filtro Instantâneo das Seções da Documentação
function filterDocsSections() {
    const input = document.getElementById('docs-search-input');
    const filter = input.value.toLowerCase().trim();
    const links = document.querySelectorAll('#docs-toc-nav .toc-link');

    links.forEach(link => {
        const text = link.innerText.toLowerCase();
        if (text.includes(filter)) {
            link.classList.remove('hidden');
        } else {
            link.classList.add('hidden');
        }
    });
}

// Botão de Copiar Código
function copyCode(btn) {
    const pre = btn.nextElementSibling;
    const code = pre ? pre.innerText : '';
    navigator.clipboard.writeText(code).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> Copiado!';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    });
}

// Highlighting do link ativo conforme scroll
window.addEventListener('scroll', () => {
    const sections = document.querySelectorAll('.doc-section');
    const scrollPos = window.scrollY + 120;

    sections.forEach(sec => {
        const top = sec.offsetTop;
        const height = sec.offsetHeight;
        const id = sec.getAttribute('id');
        if (scrollPos >= top && scrollPos < top + height) {
            document.querySelectorAll('#docs-toc-nav .toc-link').forEach(link => {
                if (link.getAttribute('href') === `#${id}`) {
                    link.classList.add('bg-sky-50', 'text-sky-700', 'font-bold');
                } else {
                    link.classList.remove('bg-sky-50', 'text-sky-700', 'font-bold');
                }
            });
        }
    });
});
</script>

<?php
if ($isLoggedIn) {
    require_once __DIR__ . '/includes/Footer.php';
} else {
    require_once __DIR__ . '/includes/PublicFooter.php';
}
?>

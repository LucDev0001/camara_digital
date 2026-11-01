<?php
/**
 * Câmara Digital - Módulo 12: Inteligência Artificial Legislativa e RAG
 * Conforme Seção 5 do Planejamento Mestre do Produto
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Inteligência Artificial Legislativa & RAG';
$tenantId = Tenant::id();
$db = Database::getConnection();

$matters = $db->query("SELECT id, code, title FROM legislative_matters WHERE tenant_id = {$tenantId} ORDER BY id DESC")->fetchAll();
$recentAIQueries = $db->query("
    SELECT * FROM ai_conversations 
    WHERE tenant_id = {$tenantId} 
    ORDER BY id DESC 
    LIMIT 10
")->fetchAll();

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-purple-700 uppercase tracking-wider">
                    <span>Área 12</span>
                    <span>•</span>
                    <span>Inteligência Artificial e RAG</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1 flex items-center gap-2">
                    <span>Assistente de IA Legislativa</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500 animate-pulse"></span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">Recuperação Aumentada por Geração (RAG) em normas municipais, redação assistida e análise documental auditável.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-purple-100 text-purple-800 text-xs font-bold border border-purple-200">
                    <i class="fa-solid fa-microchip mr-1"></i> RAG Engine v1.0
                </span>
            </div>
        </div>

        <!-- Seção 1: Grid com 3 Ferramentas de IA -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Card/Aba 1: Consulta RAG com Fontes Legais -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-magnifying-glass-location text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Pesquisa Normativa Fundamentada (RAG)</h2>
                                <p class="text-[11px] text-slate-500">Busca semântica no acervo de leis e projetos com citação direta de fontes</p>
                            </div>
                        </div>
                        <span class="text-[10px] uppercase font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">Base Auditada</span>
                    </div>

                    <!-- Sugestões de Perguntas Rápidas -->
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <span class="text-[10px] text-slate-600 font-semibold py-0.5">Sugestões:</span>
                        <button onclick="setQuery('Quais leis municipais tratam sobre meio ambiente e energia solar?')" class="text-[10px] bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 px-2 py-0.5 rounded-full border border-slate-200 transition-colors">
                            Meio ambiente e energia solar
                        </button>
                        <button onclick="setQuery('Existe alguma lei sobre calçadas acessíveis ou mobilidade urbana?')" class="text-[10px] bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 px-2 py-0.5 rounded-full border border-slate-200 transition-colors">
                            Calçadas acessíveis
                        </button>
                        <button onclick="setQuery('Quais projetos tratam de apoio à infância ou educação?')" class="text-[10px] bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 px-2 py-0.5 rounded-full border border-slate-200 transition-colors">
                            Apoio à infância e educação
                        </button>
                    </div>

                    <!-- Input Box -->
                    <div class="mt-4 flex gap-2">
                        <input type="text" id="ai-prompt-input" placeholder="Pergunte sobre leis, tramitações ou normas do município..."
                               class="flex-1 px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none"
                               onkeydown="if(event.key === 'Enter') submitAIQuery()">
                        <button onclick="submitAIQuery()" id="ai-submit-btn" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs rounded-xl shadow-md shadow-purple-600/30 flex items-center gap-1.5 transition-all">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>Consultar</span>
                        </button>
                    </div>

                    <!-- Output Area -->
                    <div id="ai-response-box" class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 min-h-[160px] text-xs text-slate-700 whitespace-pre-wrap leading-relaxed relative">
                        <p class="text-slate-600 italic">As respostas fundamentadas em leis municipais com citação de fontes serão exibidas aqui.</p>
                    </div>
                </div>

                <!-- Footer do Card -->
                <div id="ai-sources-bar" class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-600 flex items-center justify-between">
                    <span id="ai-sources-text">Fontes consultadas: Base normativa oficial</span>
                    <span id="ai-latency-text" class="font-mono text-purple-600 font-semibold"></span>
                </div>
            </div>

            <!-- Card 2: Resumidor para Ordem do Dia & Classificador -->
            <div class="space-y-6">

                <!-- Resumidor de Matéria -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <i class="fa-solid fa-compress text-sky-600"></i>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Síntese para Ordem do Dia</h2>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">Gera briefing executivo de qualquer proposição cadastrada para os parlamentares.</p>
                    
                    <div class="mt-3 space-y-2">
                        <select id="summarize-select" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                            <?php foreach ($matters as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['code']) ?> — <?= htmlspecialchars($m['title']) ?></option>
                            <?php endforeach; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <button onclick="runMatterSummary()" class="py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-lg transition-colors">
                                Síntese Ordem do Dia
                            </button>
                            <button onclick="runPlainLanguage()" class="py-2 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-sm" title="Traduzir para Linguagem Simples Cidadã (Lei 15.263/2025)">
                                <i class="fa-solid fa-feather text-amber-200"></i>
                                <span>Linguagem Simples (Lei 15.263)</span>
                            </button>
                        </div>
                    </div>
                    <div id="summary-result-box" class="mt-3 p-3 rounded-lg bg-slate-50 border border-slate-200 text-[11px] text-slate-700 whitespace-pre-wrap hidden max-h-48 overflow-y-auto"></div>
                </div>

                <!-- Classificador de Mensagem da Ouvidoria -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <i class="fa-solid fa-tags text-indigo-600"></i>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Classificador de Demandas (Ouvidoria)</h2>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">Classificação automática de assunto, urgência e setor competente conforme LAI.</p>
                    
                    <div class="mt-3 space-y-2">
                        <textarea id="classify-input" rows="2" placeholder="Cole o texto recebido de um munícipe..."
                                  class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        <button onclick="runClassification()" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition-colors">
                            Classificar Demanda com IA
                        </button>
                    </div>
                    <div id="classify-result-box" class="mt-3 p-2.5 rounded-lg bg-indigo-50 border border-indigo-200 text-[11px] text-indigo-950 hidden"></div>
                </div>

            </div>

        </div>

        <!-- Seção 2: Redator Assistido de Minutas Legislativas -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Redator Assistido de Proposições e Justificativas</h2>
                        <p class="text-[11px] text-slate-500">Geração de minutas estruturadas com fundamentação constitucional (Art. 30 da CF/88 e Lei Orgânica)</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <!-- Formulário de Parâmetros -->
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700">Tipo de Proposição</label>
                        <select id="draft-type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="Projeto de Lei Ordinária">Projeto de Lei Ordinária</option>
                            <option value="Projeto de Lei Complementar">Projeto de Lei Complementar</option>
                            <option value="Indicação">Indicação ao Poder Executivo</option>
                            <option value="Requerimento">Requerimento de Informações</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700">Tema / Objeto Central</label>
                        <input type="text" id="draft-theme" placeholder="Ex: Criação da Horta Comunitária Escolar"
                               class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700">Público-Alvo Prioritário</label>
                        <input type="text" id="draft-target" placeholder="Ex: Alunos da Rede Municipal e Comunidade"
                               class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700">Argumentos Adicionais do Parlamentar</label>
                        <textarea id="draft-args" rows="3" placeholder="Insira pontos de impacto positivo para o bairro ou cidade..."
                                  class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none"></textarea>
                    </div>

                    <button onclick="generateDraftWithAI()" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-semibold rounded-lg shadow-md transition-all">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i> Redigir Minuta Completa
                    </button>
                </div>

                <!-- Resultado da Minuta Gerada -->
                <div class="md:col-span-2 space-y-2">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                        <span>Minuta Gerada e Justificativa Fundamentada:</span>
                        <button onclick="copyDraftToClipboard()" class="text-purple-600 hover:text-purple-700 flex items-center gap-1 font-bold">
                            <i class="fa-regular fa-copy"></i> Copiar Texto
                        </button>
                    </div>
                    <textarea id="draft-output" rows="13" readonly placeholder="A minuta gerada pela IA com artigos numerados, ementa e justificativa técnica aparecerá aqui..."
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 leading-relaxed focus:outline-none"></textarea>
                    <p class="text-[10px] text-slate-600 italic">
                        * Revisão Humana Obrigatória: Toda proposição gerada deve ser conferida pela assessoria jurídica do gabinete antes do protocolo regimental formal.
                    </p>
                </div>
            </div>
        </div>

        <!-- Seção 3: Registro e Governança de Consultas de IA -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-database text-purple-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Trilha de Governança e Rastreabilidade da IA</h2>
                </div>
                <span class="text-xs text-slate-500">Últimas consultas registradas</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Data/Hora</th>
                            <th class="p-3">Módulo</th>
                            <th class="p-3">Consulta (Prompt)</th>
                            <th class="p-3">Fontes Citadas</th>
                            <th class="p-3">Latência</th>
                            <th class="p-3">Feedback</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($recentAIQueries as $q): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono text-[11px] text-slate-500 whitespace-nowrap"><?= formatDate($q['created_at'], true) ?></td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                                        <?= htmlspecialchars($q['module_context']) ?>
                                    </span>
                                </td>
                                <td class="p-3 text-slate-900 font-medium"><?= htmlspecialchars($q['prompt']) ?></td>
                                <td class="p-3 text-[11px] text-slate-600"><?= htmlspecialchars($q['sources_cited']) ?></td>
                                <td class="p-3 font-mono text-purple-600 font-bold whitespace-nowrap"><?= $q['latency_ms'] ?> ms</td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="text-emerald-600 font-bold text-[10px]"><i class="fa-regular fa-thumbs-up"></i> Validado</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<script>
function setQuery(text) {
    document.getElementById('ai-prompt-input').value = text;
    submitAIQuery();
}

function submitAIQuery() {
    const input = document.getElementById('ai-prompt-input');
    const prompt = input.value.trim();
    if (!prompt) return;

    const box = document.getElementById('ai-response-box');
    const btn = document.getElementById('ai-submit-btn');
    const sourcesBar = document.getElementById('ai-sources-text');
    const latencyBar = document.getElementById('ai-latency-text');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processando...';
    box.innerHTML = '<div class="text-slate-400 py-6 text-center"><i class="fa-solid fa-spinner fa-spin text-lg"></i><p class="mt-2">Consultando o acervo de normas e leis municipais via RAG...</p></div>';

    const formData = new FormData();
    formData.append('action', 'query');
    formData.append('prompt', prompt);
    formData.append('module', 'Legislativo');

    fetch('<?= url("api/ai_assistant.php") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i><span>Consultar</span>';

        if (data.response) {
            box.textContent = data.response;
            sourcesBar.innerHTML = '<strong>Fontes Fundamentadas:</strong> ' + (data.sources_text || 'Base legal local');
            latencyBar.textContent = data.latency_ms + ' ms';
            showToast('success', 'Resposta RAG gerada com sucesso com fontes normativas citadas!');
        } else {
            box.textContent = data.error || 'Erro na consulta.';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i><span>Consultar</span>';
        box.textContent = 'Falha na conexão com o serviço de IA.';
    });
}

function generateDraftWithAI() {
    const type = document.getElementById('draft-type').value;
    const theme = document.getElementById('draft-theme').value.trim();
    const target = document.getElementById('draft-target').value.trim();
    const args = document.getElementById('draft-args').value.trim();

    if (!theme) {
        showToast('warning', 'Informe o tema ou objeto central da proposição.');
        return;
    }

    const output = document.getElementById('draft-output');
    output.value = 'Redigindo minuta legislativa com técnica legislativa e fundamentação constitucional...';

    const formData = new FormData();
    formData.append('action', 'generate_draft');
    formData.append('type', type);
    formData.append('theme', theme);
    formData.append('target_group', target);
    formData.append('justification', args);

    fetch('<?= url("api/ai_assistant.php") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.draft) {
            output.value = data.type.toUpperCase() + "\n\nEMENTA: " + data.ementa + "\n\n" + data.draft + "\n\n" + data.justification;
            showToast('success', 'Minuta e justificativa redigidas com sucesso!');
        } else {
            output.value = 'Erro ao gerar minuta: ' + (data.error || 'Desconhecido');
        }
    })
    .catch(() => {
        output.value = 'Erro de comunicação ao redigir minuta.';
    });
}

function copyDraftToClipboard() {
    const output = document.getElementById('draft-output');
    if (!output.value) return;
    navigator.clipboard.writeText(output.value).then(() => {
        showToast('info', 'Texto da minuta copiado para a área de transferência!');
    });
}

function runMatterSummary() {
    const select = document.getElementById('summarize-select');
    const matterId = select.value;
    const box = document.getElementById('summary-result-box');

    box.classList.remove('hidden');
    box.textContent = 'Gerando síntese executiva para a Ordem do Dia...';

    const formData = new FormData();
    formData.append('action', 'summarize');
    formData.append('matter_id', matterId);

    fetch('<?= url("api/ai_assistant.php") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.summary) {
            box.textContent = data.summary;
        } else {
            box.textContent = 'Erro ao gerar resumo.';
        }
    });
}

function runPlainLanguage() {
    const select = document.getElementById('summarize-select');
    const matterId = select.value;
    const box = document.getElementById('summary-result-box');

    box.classList.remove('hidden');
    box.textContent = 'Traduzindo para Linguagem Simples (Lei 15.263/2025)...';

    const formData = new FormData();
    formData.append('action', 'plain_language');
    formData.append('matter_id', matterId);

    fetch('<?= url("api/ai_assistant.php") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.plain_language_summary) {
            box.textContent = data.plain_language_summary;
            showToast('success', 'Explicação em Linguagem Simples gerada e salva na proposição!');
        } else {
            box.textContent = 'Erro ao gerar: ' + (data.error || 'Desconhecido');
        }
    })
    .catch(() => {
        box.textContent = 'Falha de comunicação com o serviço de IA.';
    });
}

function runClassification() {
    const input = document.getElementById('classify-input');
    const msg = input.value.trim();
    if (!msg) {
        showToast('warning', 'Cole a mensagem para classificar.');
        return;
    }

    const box = document.getElementById('classify-result-box');
    box.classList.remove('hidden');
    box.textContent = 'Analisando demanda...';

    const formData = new FormData();
    formData.append('action', 'classify');
    formData.append('message', msg);

    fetch('<?= url("api/ai_assistant.php") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        box.innerHTML = `
            <strong>Classificação Sugerida:</strong> ${data.suggested_type}<br>
            <strong>Setor Recomendado:</strong> ${data.suggested_department}<br>
            <strong>Grau de Urgência:</strong> ${data.suggested_urgency}<br>
            <span class="text-[10px] text-slate-500">Confiança do Modelo: ${data.confidence}</span>
        `;
    });
}
</script>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

<?php
/**
 * Câmara Digital - Rodapé do Portal Público do Cidadão
 */
?>
    <!-- Public Footer -->
    <footer class="bg-slate-900 text-slate-300 mt-auto border-t border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Coluna 1: Câmara -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold">
                            <i class="fa-solid fa-landmark text-sm"></i>
                        </div>
                        <span class="text-sm font-bold text-white font-heading"><?= htmlspecialchars($currentTenant['name']) ?></span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Compromisso com a legalidade, a transparência ativa e a participação cidadã na construção de políticas públicas municipais.
                    </p>
                    <p class="text-[11px] text-slate-400">
                        CNPJ: <?= formatCnpj($currentTenant['cnpj']) ?>
                    </p>
                </div>

                <!-- Coluna 2: Atendimento -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white">Sede & Atendimento</h4>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        <?= htmlspecialchars($currentTenant['address'] ?? 'Avenida Principal da Câmara') ?><br>
                        <?= htmlspecialchars($currentTenant['city']) ?> — <?= htmlspecialchars($currentTenant['state']) ?>
                    </p>
                    <p class="text-[11px] text-slate-400">
                        <i class="fa-solid fa-phone mr-1.5 text-sky-400"></i> <?= htmlspecialchars($currentTenant['phone'] ?? '(67) 3316-1500') ?>
                    </p>
                    <p class="text-[11px] text-slate-400">
                        <i class="fa-solid fa-envelope mr-1.5 text-sky-400"></i> <?= htmlspecialchars($currentTenant['email'] ?? 'contato@camara.ms.gov.br') ?>
                    </p>
                    <p class="text-[11px] text-slate-400">
                        Horário: Segunda a Sexta, das 08h às 17h
                    </p>
                </div>

                <!-- Coluna 3: Links Rápidos do Cidadão -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white">Serviços ao Munícipe</h4>
                    <ul class="space-y-1.5 text-[11px]">
                        <li><a href="<?= url('portal/leis.php') ?>" class="text-slate-400 hover:text-white transition-colors">Pesquisa de Leis e Decretos</a></li>
                        <li><a href="<?= url('portal/proposicoes.php') ?>" class="text-slate-400 hover:text-white transition-colors">Acompanhar Projetos de Lei</a></li>
                        <li><a href="<?= url('portal/sessoes.php') ?>" class="text-slate-400 hover:text-white transition-colors">Pautas e Atas das Sessões</a></li>
                        <li><a href="<?= url('portal/transparencia.php') ?>" class="text-slate-400 hover:text-white transition-colors">Portal da Transparência (LAI)</a></li>
                        <li><a href="<?= url('portal/ouvidoria.php') ?>" class="text-slate-400 hover:text-white transition-colors">Abrir Manifestação na Ouvidoria</a></li>
                        <li><a href="<?= url('portal/validar.php') ?>" class="text-slate-400 hover:text-white transition-colors">Validar Autenticidade de Documento</a></li>
                        <li><a href="<?= url('ajuda.php') ?>" class="text-sky-400 hover:text-sky-300 font-semibold transition-colors">Central de Ajuda (Guia do Usuário)</a></li>
                        <li><a href="<?= url('documentacao.php') ?>" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">Documentação Técnica para Devs</a></li>
                    </ul>
                </div>

                <!-- Coluna 4: Conformidade Legal -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white">Bases Legais & Transparência</h4>
                    <ul class="space-y-1 text-[11px] text-slate-400">
                        <li>• Lei de Acesso à Informação (Lei 12.527/11)</li>
                        <li>• Lei de Responsabilidade Fiscal (LC 101/00)</li>
                        <li>• Lei Geral de Proteção de Dados (LGPD)</li>
                        <li>• Nova Lei de Licitações (Lei 14.133/21)</li>
                    </ul>
                    <div class="pt-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] bg-slate-800 text-emerald-400 px-2 py-1 rounded border border-slate-700">
                            <i class="fa-solid fa-lock"></i> Conexão Segura & Auditada
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="mt-8 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($currentTenant['name']) ?>. Todos os direitos reservados.</p>
                <div class="flex items-center gap-4">
                    <a href="<?= url('ajuda.php') ?>" class="hover:text-white transition-colors">Guia do Usuário</a>
                    <span>•</span>
                    <a href="<?= url('documentacao.php') ?>" class="hover:text-white transition-colors">Docs Técnicos</a>
                    <span>•</span>
                    <p>Plataforma <span class="text-white font-semibold">Câmara Digital</span></p>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>

# Câmara Digital — Planejamento Mestre do Produto

**Versão:** 1.2  
**Data:** 26 de outubro de 2026  
**Status:** Planejamento mestre atualizado com matriz normativa e checklist de transparência. É uma especificação de produto em evolução; a aplicabilidade legal por Câmara, o código existente e as integrações precisam ser auditados e validados antes da produção.  
**Objetivo:** Evoluir o projeto Câmara Digital para uma plataforma SaaS multi-Câmara, modular, segura, acessível, auditável e com recursos de Inteligência Artificial aplicados à rotina legislativa e administrativa.

> **Regra principal de desenvolvimento:** não reescrever o que já funciona sem necessidade. Primeiro auditar o repositório, documentar o que existe, separar o que está completo do que está parcial e só então transformar este plano em tarefas de implementação.

---

## Índice

1. [Visão do produto](#1-visão-do-produto)
2. [Objetivos e princípios](#2-objetivos-e-princípios)
3. [Perfis de usuário](#3-perfis-de-usuário)
4. [Escopo funcional: 12 áreas](#4-escopo-funcional-12-áreas)
5. [Inteligência Artificial: diferencial do produto](#5-inteligência-artificial-diferencial-do-produto)
6. [Plataforma SaaS multi-tenant](#6-plataforma-saas-multi-tenant)
7. [Arquitetura técnica proposta](#7-arquitetura-técnica-proposta)
8. [Modelo de dados conceitual](#8-modelo-de-dados-conceitual)
9. [Segurança, privacidade e auditoria](#9-segurança-privacidade-e-auditoria)
10. [Integrações externas](#10-integrações-externas)
11. [Portal público, acessibilidade e experiência](#11-portal-público-acessibilidade-e-experiência)
12. [Relatórios e indicadores](#12-relatórios-e-indicadores)
13. [Estratégia de entrega e roadmap](#13-estratégia-de-entrega-e-roadmap)
14. [Backlog inicial de épicos](#14-backlog-inicial-de-épicos)
15. [Critérios de aceite transversais](#15-critérios-de-aceite-transversais)
16. [Riscos e decisões em aberto](#16-riscos-e-decisões-em-aberto)
17. [Auditoria inicial do projeto existente](#17-auditoria-inicial-do-projeto-existente)
18. [Como trabalhar com o Codex](#18-como-trabalhar-com-o-codex)
19. [Referências oficiais e técnicas](#19-referências-oficiais-e-técnicas)
20. [Especificação detalhada de RH e obrigações digitais](#20-especificação-detalhada-de-rh-e-obrigações-digitais)
21. [Assinatura eletrônica, sessões e biometria](#21-assinatura-eletrônica-sessões-e-biometria)
22. [Portal público, solicitações e publicações oficiais](#22-portal-público-solicitações-e-publicações-oficiais)
23. [Pacotes comerciais e limites entre módulos](#23-pacotes-comerciais-e-limites-entre-módulos)
24. [Critérios de aceite específicos para essas integrações](#24-critérios-de-aceite-específicos-para-essas-integrações)
25. [Matriz normativa nacional e municipal](#25-matriz-normativa-nacional-e-municipal)
26. [Checklist de transparência alinhado ao PNTP 2026](#26-checklist-de-transparência-alinhado-ao-pntp-2026)
27. [Especificação de publicação e processo legislativo](#27-especificação-de-publicação-e-processo-legislativo)
28. [SIC, ouvidoria, linguagem simples e acessibilidade](#28-sic-ouvidoria-linguagem-simples-e-acessibilidade)
29. [Integrações oficiais: versões e operação](#29-integrações-oficiais-versões-e-operação)
30. [Requisitos de IA responsável para o setor público](#30-requisitos-de-ia-responsável-para-o-setor-público)
31. [Configuração jurídica por Câmara e registro de obrigações](#31-configuração-jurídica-por-câmara-e-registro-de-obrigações)
32. [Backlog regulatório adicional e testes de conformidade](#32-backlog-regulatório-adicional-e-testes-de-conformidade)
33. [Fontes normativas adicionais e data de verificação](#33-fontes-normativas-adicionais-e-data-de-verificação)

---

## 1. Visão do produto

O Câmara Digital deve ser uma plataforma de gestão institucional para Câmaras Municipais brasileiras. A plataforma unifica rotinas administrativas, processo legislativo, atendimento ao cidadão, transparência e análise de dados em um único ambiente, com módulos que podem ser ativados de acordo com a realidade de cada cliente.

### 1.1 Proposta de valor

- **Para a Câmara:** reduzir retrabalho, padronizar procedimentos, dar previsibilidade aos prazos e melhorar a rastreabilidade das decisões administrativas.
- **Para vereadores e gabinetes:** acompanhar proposições, agendas, documentos, solicitações e compromissos institucionais.
- **Para servidores:** executar rotinas com responsabilidades claras, histórico e menos duplicação de dados.
- **Para a gestão:** acompanhar pendências, indicadores, contratos, patrimônio, estoque e informações que sustentam decisões.
- **Para o cidadão:** localizar leis e proposições, consultar informações públicas e acompanhar manifestações e solicitações.
- **Para o fornecedor SaaS:** operar uma plataforma padronizada, configurável por Câmara, com implantação repetível e manutenção centralizada.

### 1.2 O que o produto não deve ser

- Uma coleção de telas sem processos integrados.
- Um sistema que trate todas as Câmaras como se tivessem o mesmo Regimento Interno, organograma ou fluxo administrativo.
- Uma promessa de conformidade legal automática sem testes e validação especializada.
- Uma substituição imediata de todos os sistemas contábeis, folha de pagamento ou plataformas oficiais sem estudo de viabilidade.
- Um sistema em que a IA tome decisões administrativas, jurídicas, financeiras ou legislativas autonomamente.

### 1.3 Resultados esperados

1. Ter uma base multi-tenant confiável e auditável.
2. Implantar módulos gradualmente, com processos realmente utilizáveis.
3. Reaproveitar funcionalidades existentes após auditoria técnica.
4. Permitir implantação de novas Câmaras sem duplicar o código ou misturar dados.
5. Oferecer uma experiência pública acessível e responsiva.
6. Usar IA para diminuir tempo de pesquisa e trabalho repetitivo, mantendo a fonte original e a decisão final sob responsabilidade humana.

---

## 2. Objetivos e princípios

### 2.1 Princípios de produto

- **Modularidade:** cada área tem limites claros, mas compartilha serviços comuns.
- **Configuração antes de bifurcação:** as diferenças entre Câmaras devem ser resolvidas por configuração quando isso não comprometer a integridade do domínio.
- **Segurança desde a concepção:** isolamento de clientes, menor privilégio, auditoria e proteção de dados desde a primeira entrega.
- **Rastreabilidade:** atos, tramitações, aprovações, versões e publicações devem ter histórico verificável.
- **Interoperabilidade:** oferecer APIs e integrações documentadas quando houver utilidade e base técnica/legal.
- **Transparência por origem:** dados divulgados devem ter origem identificável e processo de atualização conhecido.
- **Acessibilidade:** operar bem com teclado, leitores de tela, zoom, dispositivos móveis e conexões limitadas.
- **Evolução segura:** migrações de banco, versões e integrações devem ser controladas e reversíveis sempre que possível.
- **IA assistiva:** IA sugere, localiza, resume, classifica e alerta; pessoas autorizadas revisam e decidem.

### 2.2 Indicadores de sucesso do produto

Os indicadores devem ser definidos com dados reais após pilotos. Como ponto de partida:

- Percentual de processos concluídos dentro do prazo.
- Tempo médio entre abertura e conclusão de solicitações.
- Percentual de registros com documentos obrigatórios completos.
- Taxa de erro e retrabalho em operações de estoque, compras e protocolo.
- Atualização e cobertura dos dados publicados no portal de transparência.
- Disponibilidade do serviço e tempo de recuperação após incidentes.
- Adoção por perfil e frequência de uso dos módulos.
- Qualidade das respostas da IA, incluindo taxa de respostas sem fonte adequada e taxa de correções feitas por usuários.
- Tempo médio de implantação de uma nova Câmara.

Metas numéricas só devem ser fixadas depois de medir a linha de base e entender a capacidade operacional do produto.

---

## 3. Perfis de usuário

A lista é inicial. Cada Câmara deverá poder configurar usuários e permissões dentro dos limites de segurança do produto.

| Perfil | Necessidades principais | Cuidados de permissão |
|---|---|---|
| Administrador da plataforma | Gestão técnica do SaaS, planos e operação | Acesso operacional limitado; não deve ter acesso irrestrito por padrão aos dados de conteúdo dos clientes |
| Administrador da Câmara | Configurar a instituição, usuários e módulos | Não recebe automaticamente permissão para toda informação sigilosa ou função incompatível |
| Presidência e Mesa Diretora | Gestão institucional, pautas, atos e visão gerencial | Ações sensíveis devem respeitar alçadas e regras regimentais |
| Vereador | Proposições, agenda, sessões, documentos e gabinete | Acesso somente aos dados institucionais autorizados e aos próprios fluxos de gabinete |
| Assessor parlamentar | Apoiar atividade parlamentar e acompanhar expedientes | Permissões delegadas e revogáveis; não presumir poderes de assinatura do vereador |
| Secretaria legislativa | Tramitação, pauta, comissões, sessões e atas | Registro de autoria, prazos, versões e atos de publicação |
| Recursos humanos | Dados funcionais e rotinas de pessoal | Acesso restrito a dados pessoais e funcionais |
| Compras e contratos | Processos de contratação, fornecedores e instrumentos | Segregação entre solicitar, aprovar, contratar, fiscalizar e pagar, conforme regras da Câmara |
| Almoxarifado e patrimônio | Materiais, movimentações e bens | Ajustes de estoque e baixa patrimonial precisam de autorização e justificativa |
| Financeiro/contabilidade | Dados e processos financeiros, exportações e conciliações | Não confundir painel gerencial com sistema contábil oficial sem integração validada |
| Controle interno | Auditoria, evidências, riscos e pendências | Acesso de leitura amplo conforme atribuição, sem permitir alteração indevida de evidências |
| Ouvidoria/SIC | Receber e responder manifestações e pedidos | Separar acesso restrito, denúncias e dados pessoais dos registros públicos |
| Comunicação | Notícias, sessões, publicações e mídia | Conteúdo publicado deve ter fluxo editorial e histórico de revisão |
| Cidadão | Consulta pública, solicitação e acompanhamento | Só pode acessar informações públicas ou os próprios protocolos autenticados |
| Auditor externo autorizado | Consulta temporária de dados e evidências | Acesso limitado por finalidade, escopo, prazo e trilha de auditoria |

### 3.1 Autorização

O sistema deve combinar papéis (RBAC) com regras contextuais quando necessário, como Câmara, setor, vínculo, recurso, estado do processo e nível de sigilo. A autorização deve ser verificada no servidor em toda operação relevante, e não apenas na interface.

---

## 4. Escopo funcional: 12 áreas

Cada módulo será desenvolvido com funcionalidades essenciais, integrações e critérios próprios. A priorização definitiva depende da auditoria do projeto e de validação com possíveis usuários de Câmaras Municipais.

### 4.1 Área 1 — Administração e gestão institucional

**Objetivo:** configurar a estrutura e administrar a rotina interna da Câmara.

**Funcionalidades essenciais**

- Cadastro institucional: nome, CNPJ, endereço, contatos, brasão/logotipo, domínio e dados de publicação.
- Estrutura organizacional: unidades, setores, departamentos, gabinetes, cargos de estrutura e responsáveis.
- Legislaturas, mandatos, Mesa Diretora e composição de comissões, com períodos de vigência.
- Usuários, convites, ativação/desativação, troca de lotação e delegação temporária.
- Agenda institucional, compromissos, reuniões, prazos e tarefas.
- Memorandos, comunicações internas e distribuição de expedientes.
- Painel de pendências, alertas, calendários e atividades recentes.
- Parâmetros da Câmara: feriados locais, fuso horário, numeração, modelos, fluxos e regras configuráveis.
- Gestão de implantação, checklist inicial e importação dos cadastros necessários.

**Evoluções possíveis**

- Reserva de salas e equipamentos.
- Gestão de eventos institucionais e visitas escolares.
- Central de serviços de TI, suporte interno e solicitações de manutenção.
- Gestão de riscos operacionais e continuidade de serviços.

**Aceite básico**

- A Câmara consegue configurar sua estrutura sem intervenção no código.
- Registros com vigência preservam o histórico anterior.
- Alterações administrativas ficam auditadas e respeitam permissões.

### 4.2 Área 2 — Processo legislativo

**Objetivo:** acompanhar o ciclo de vida de proposições e atos legislativos.

**Funcionalidades essenciais**

- Cadastro e numeração de projetos de lei, projetos de resolução, decretos legislativos, emendas, requerimentos, indicações, moções e outros tipos configurados.
- Autor, coautores, ementa, texto, justificativa, anexos, assunto, legislatura e situação.
- Controle de versões e substitutivos, com identificação de alterações.
- Tramitação com origem, destino, despacho, responsável, data, prazo, resultado e documentos.
- Comissões, composição, relatorias, pareceres e reuniões.
- Pautas, ordens do dia, sessões ordinárias/extraordinárias/solenes e itens deliberados.
- Presença, quórum, votação nominal/simbólica/outras modalidades conforme regra aplicável, resultado e registro dos votos.
- Atas, expedientes, anexos, revisão, aprovação e publicação.
- Cadastro, pesquisa e compilação de leis e normas jurídicas.
- Pesquisa por número, ano, autor, palavra-chave, situação, tema e período.
- Portal público de acompanhamento legislativo e histórico do processo.
- Configuração por Lei Orgânica e Regimento Interno, incluindo prazos, tipos de proposição e fluxos.

**Evoluções possíveis**

- Painel eletrônico do plenário.
- Transmissão de sessões e associação de vídeo a pauta, fala ou votação.
- Transcrição de áudio, com marcação de trechos e revisão humana.
- Análise de relacionamento entre projetos, emendas e normas existentes.
- Comparação assistida de versões de textos legais.

**Regras críticas**

- Não permitir que a IA crie resultado oficial de votação.
- Registrar a autoria e o instante dos atos relevantes.
- Distinguir rascunho, texto apresentado, texto em tramitação, texto aprovado e norma publicada.
- Alterações após publicação devem seguir procedimento formal e preservar a versão anterior.
- A automação deve respeitar o Regimento Interno e os procedimentos locais validados.

**Referência de estudo:** SAPL/Interlegis, sem presumir cópia integral ou equivalência jurídica das regras.

### 4.3 Área 3 — Recursos humanos e gestão de pessoas

**Objetivo:** organizar informações e processos funcionais da Câmara com proteção reforçada de dados.

**Funcionalidades essenciais**

- Cadastro de servidores, comissionados, efetivos, estagiários e demais vínculos aplicáveis.
- Cargos, funções, lotações, atos de nomeação/exoneração e histórico de vínculo.
- Documentação funcional, dependentes e informações pessoais estritamente necessárias.
- Férias, licenças, afastamentos, cessões, substituições e retornos.
- Frequência, escalas e jornada quando aplicáveis.
- Avaliação de período probatório ou desempenho, se adotada pela Câmara.
- Benefícios e eventos para integração com folha de pagamento.
- Portal do servidor: documentos, solicitações, férias e consulta de dados permitidos.
- Relatórios e exportações para sistemas de folha/eSocial, conforme leiautes aplicáveis.
- Controle de documentos obrigatórios, prazos e pendências.

**Escopo cauteloso para folha**

A folha de pagamento completa exige estudo específico de regimes funcionais, rubricas, incidências, cálculos, retenções, eventos periódicos, fechamento e retificações. A primeira etapa pode priorizar cadastro, fluxo de eventos e integração com fornecedor especializado, em vez de construir um motor de folha sem validação suficiente.

**Aceite básico**

- Dados pessoais restritos não aparecem a usuários sem autorização.
- Eventos funcionais mantêm histórico e documentos de origem.
- Exportações são validadas contra leiautes oficiais vigentes e mantêm registro do envio/retorno.

### 4.4 Área 4 — Almoxarifado e estoque

**Objetivo:** controlar materiais, estoque e atendimento às demandas internas.

**Funcionalidades essenciais**

- Catálogo de itens, categorias, unidades de medida, códigos e especificações.
- Depósitos, prateleiras ou locais de armazenamento.
- Entradas, saídas, devoluções, transferências, perdas e ajustes.
- Requisições por setor, aprovação por alçada, reserva e separação.
- Saldo disponível, saldo reservado, estoque mínimo e alerta de reposição.
- Inventário periódico, contagem física e divergências justificadas.
- Vínculo da entrada com compra, nota fiscal, fornecedor e recebimento quando disponível.
- Histórico de movimentações, responsáveis, datas e motivo.
- Etiquetas QR Code/código de barras, como recurso posterior.
- Relatórios de consumo por setor, item, período e centro de custo.

**Regras críticas**

- Não editar diretamente um saldo sem registrar a movimentação correspondente.
- Impedir saldo negativo por padrão, permitindo exceção somente se uma regra explícita e auditada autorizar.
- Usar transações e controles de concorrência para evitar saídas duplicadas.
- Ajustes e cancelamentos devem preservar a trilha de auditoria.

**Aceite básico**

Uma requisição aprovada deve gerar a saída de estoque apenas uma vez. O saldo deve ser consistente com o histórico de movimentações e verificável por inventário.

### 4.5 Área 5 — Compras, licitações e contratos

**Objetivo:** acompanhar a contratação pública do planejamento à fiscalização e ao encerramento.

**Funcionalidades essenciais**

- Demanda de compra e justificativa da necessidade.
- Planejamento anual das contratações, quando aplicável.
- Estudo técnico preliminar, termo de referência/projeto básico e artefatos preparatórios exigidos.
- Pesquisa de preços, fornecedores consultados, datas e evidências.
- Estimativa de custo, disponibilidade e encaminhamento orçamentário.
- Processos de licitação, contratação direta e demais modalidades/situações conforme legislação vigente.
- Edital, anexos, propostas, atas, julgamento, adjudicação/homologação e resultados conforme fluxo aplicável.
- Cadastro de fornecedores e documentos de habilitação, com validade e alertas.
- Contratos, instrumentos equivalentes, aditivos, apostilamentos, vigência, valores e garantias.
- Gestor/fiscal do contrato, designação, ocorrências, medição e atestes.
- Obrigações, marcos de entrega, reajustes, prorrogações e encerramento.
- Publicação e preparação de dados para o PNCP e portal de transparência, quando aplicável.
- Vínculo com requisição, estoque, patrimônio, empenho e pagamento sem duplicar o registro da origem.

**Regras críticas**

- Modelos de documentos não substituem análise jurídica nem atos formais.
- O sistema não deve presumir modalidade, dispensa ou inexigibilidade apenas com base em IA.
- Prazos e alertas devem ser configuráveis e validados a partir da legislação e do regulamento da Câmara.
- Registrar publicação, protocolo externo, resposta de integração e pendências.

### 4.6 Área 6 — Patrimônio, frota e manutenção

**Objetivo:** manter o inventário de bens e controlar sua guarda e utilização.

**Funcionalidades essenciais**

- Cadastro de bens móveis, equipamentos, móveis, informática e veículos.
- Número patrimonial/tombamento, descrição, marca, modelo, número de série, valor, origem, data e documento.
- Localização, responsável, termo de carga, cessão e transferência.
- Inventário físico, conferência por QR Code e tratamento de divergências.
- Depreciação e avaliação patrimonial conforme política contábil validada.
- Baixa, alienação, perda, dano, obsolescência e documentação justificadora.
- Registro de manutenção, garantia, ordem de serviço, custo e indisponibilidade.
- Para frota: veículo, condutores autorizados, quilometragem, combustível, seguro, multas, licenciamento e revisões.
- Alertas de vencimentos, manutenção preventiva e documentação.
- Relatórios por categoria, localização, responsável e situação.

**Aceite básico**

Cada bem deve ter identificador único na Câmara, histórico de movimentações e documentos de suporte. Transferência ou baixa não pode apagar o histórico da guarda anterior.

### 4.7 Área 7 — Financeiro, orçamento e contabilidade

**Objetivo:** acompanhar a execução financeira da Câmara e interagir com os sistemas oficiais já utilizados.

**Funcionalidades essenciais, conforme escopo validado**

- Estrutura de planejamento e dotações orçamentárias.
- Acompanhamento de saldo e comprometimento de recursos.
- Solicitações de despesa e fluxo de autorização.
- Vinculação de processo de compra, contrato, recebimento, documento fiscal e dados de pagamento.
- Diárias, adiantamentos, prestação de contas e devoluções.
- Conciliações e acompanhamento de pendências.
- Centros de custo e relatórios gerenciais.
- Exportação/importação e integração com sistemas contábeis e de folha.
- Controle de remessas, arquivos, validações, retornos e correções.
- Alertas de limites e prazos, usando regras configuradas e fontes oficiais revisadas.

**Estratégia recomendada**

Começar por dados gerenciais, workflow e integração com sistema contábil já utilizado. Não afirmar que o Câmara Digital substitui a contabilidade oficial até que os requisitos contábeis, legais, de escrituração e de prestação de contas estejam integralmente especificados, implementados e testados.

### 4.8 Área 8 — Transparência pública

**Objetivo:** permitir consulta pública de informações que devem ser divulgadas, com atualização rastreável.

**Funcionalidades essenciais**

- Estrutura organizacional, contatos e responsáveis institucionais.
- Agendas, sessões, proposições, normas e atas publicáveis.
- Despesas e informações orçamentárias conforme competência e obrigação aplicável.
- Contratos, licitações, fornecedores e dados de execução publicáveis.
- Quadro de pessoal e remuneração divulgável conforme regras aplicáveis.
- Diárias, viagens, patrimônio ou outros temas previstos em norma e política de transparência.
- Ferramenta de pesquisa, filtros, paginação e exportação de dados.
- Documentos para download e indicação da data de atualização.
- Dados abertos em formatos reutilizáveis quando possível.
- Painel interno de integridade, itens pendentes e atualização do conteúdo.
- Histórico de publicação e correção.

**Regras críticas**

- Publicar somente campos classificados para divulgação.
- Não expor automaticamente dados pessoais, credenciais, assinaturas, endereços pessoais ou conteúdo protegido.
- Distinguir dado ausente, dado não aplicável, informação ainda não publicada e falha de integração.
- Definir responsável e frequência de atualização para cada conjunto de dados.
- A matriz de obrigações deve ser validada com base na LAI, LGPD, legislação financeira, orientações dos tribunais de contas e normas locais.

### 4.9 Área 9 — Portal do cidadão, ouvidoria e SIC

**Objetivo:** centralizar relacionamento com a população e dar rastreabilidade às manifestações.

**Funcionalidades essenciais**

- Formulário público para solicitações, sugestões, reclamações, elogios e manifestações de ouvidoria.
- Serviço de Informação ao Cidadão (SIC) para pedidos de acesso à informação.
- Identificador/protocolo, comprovante de recebimento e acompanhamento.
- Classificação, distribuição, prazos, responsável, resposta e encaminhamento.
- Recursos e reabertura conforme regras aplicáveis.
- Gestão diferenciada de denúncia, sigilo e dados pessoais.
- Anexos com validação de tipo/tamanho e proteção contra arquivos maliciosos.
- Relatórios de volume, prazo, assunto, encaminhamento e situação.
- Conteúdo de ajuda e perguntas frequentes.
- Acesso sem conta para serviços compatíveis; autenticação para consultar informações pessoais ou restritas.

**Regras críticas**

- Não exibir ao público o conteúdo interno de uma denúncia nem seus anexos por padrão.
- O protocolo deve ser difícil de adivinhar e não servir sozinho como autorização para acessar dados sensíveis.
- O sistema precisa permitir atendimento por canais alternativos quando a pessoa não puder utilizar o portal.
- Prazos legais devem ser configurados e validados; não codificar prazos sem fonte ou validação.

### 4.10 Área 10 — Protocolo, documentos e assinaturas

**Objetivo:** compartilhar uma infraestrutura documental comum aos módulos.

**Funcionalidades essenciais**

- Entrada, saída, registro, classificação e numeração de documentos.
- Protocolo, remetente, destinatário, setor atual, prioridade, assunto e prazo.
- Encaminhamentos, recebimentos, despachos, devoluções e comprovantes.
- Modelos documentais e preenchimento a partir de dados confiáveis.
- Anexos, versões, metadados e vínculo com processos relacionados.
- Geração de PDF e documentos acessíveis quando aplicável.
- Busca por texto e metadados, respeitando permissões.
- Assinatura eletrônica por integração adequada ao requisito jurídico e de segurança.
- Verificação de assinatura, evidências, validade temporal e integridade do arquivo conforme o provedor escolhido.
- Tabelas de temporalidade, arquivamento e política de retenção, definidas com orientação competente.

**Regras críticas**

- Alterar um documento assinado gera nova versão ou novo documento; não substitui silenciosamente o arquivo assinado.
- O hash do arquivo, versão, autor, data e eventos importantes devem ser preservados conforme política de auditoria.
- Não construir assinatura própria e declará-la juridicamente válida sem análise específica.

### 4.11 Área 11 — Controle interno, riscos e conformidade

**Objetivo:** dar visibilidade às responsabilidades, evidências, falhas e medidas corretivas.

**Funcionalidades essenciais**

- Matriz de risco e controle por processo.
- Registro de achados, recomendações, responsáveis, prazos e evidências.
- Planos de ação, acompanhamento e encerramento justificado.
- Segregação de funções e identificação de conflitos de permissão.
- Relatórios de alterações críticas e tentativas de acesso não autorizado.
- Histórico de políticas e aceite de procedimentos internos.
- Checklist de processos, contratação, publicação, prestação de contas e encerramento.
- Exportação de evidências com índice e data de extração.
- Painel de pendências por setor, criticidade, idade e prazo.
- Canal de tratamento de incidentes e vulnerabilidades.

**Regras críticas**

- A plataforma apoia a função de controle, mas não substitui a independência nem o julgamento dos responsáveis.
- Evidências de auditoria devem ter acesso restrito, integridade e política de retenção.
- A ferramenta não deve permitir que um usuário audite e altere silenciosamente a própria evidência.

### 4.12 Área 12 — Aplicação móvel, comunicação e Inteligência Artificial

**Objetivo:** tornar serviços e informações úteis em celular e incorporar IA de maneira responsável.

**Aplicação móvel**

- Começar com web responsiva e PWA instalável, evitando manter duas aplicações separadas antes de validar a necessidade.
- Login seguro para servidores e área pública acessível sem login quando apropriado.
- Notificações de prazos, mudanças de estado e sessões, conforme preferências e permissões.
- Consulta de sessões, leis, proposições, agenda e notícias.
- Acompanhamento de protocolos e solicitações pessoais autenticadas.
- Ações de campo, como leitura de etiquetas patrimoniais, podem entrar em fase posterior.

**Comunicação**

- Notícias, comunicados, agenda pública, pautas e alertas.
- Fluxo editorial com rascunho, revisão, aprovação e publicação.
- Integração com transmissão/arquivo de vídeo, quando a fonte técnica estiver disponível.
- Histórico de conteúdo e correções.

**IA**

A IA deve ser transversal aos módulos, com acesso limitado aos dados que cada usuário já pode consultar e com origem documental visível. Os recursos são detalhados na seção seguinte.

---

## 5. Inteligência Artificial: diferencial do produto

A IA pode ser uma vantagem competitiva real, desde que resolva tarefas frequentes, economize tempo e produza resultados verificáveis. O diferencial não deve ser apenas um chatbot genérico: precisa entender a estrutura documental e os fluxos da Câmara.

### 5.1 Princípios para a IA

1. **Resposta fundamentada:** quando responder sobre leis, contratos, atos ou documentos internos, mostrar as fontes utilizadas, incluindo título, data, seção ou página quando possível.
2. **Permissões herdadas:** a IA não pode consultar nem revelar documentos que o usuário não poderia abrir diretamente.
3. **Separação de clientes:** contexto, arquivos, embeddings, cache e histórico de IA precisam respeitar o tenant ativo.
4. **Incerteza explícita:** quando não encontrar evidência suficiente, deve dizer que não encontrou suporte suficiente, em vez de preencher lacunas com suposições.
5. **Revisão humana:** minutas, resumos oficiais, classificações sensíveis e recomendações devem poder ser revisados antes de qualquer ato formal.
6. **Sem decisões autônomas de alto impacto:** IA não aprova licitações, não decide recursos do cidadão, não determina resultado de votação e não efetua pagamentos.
7. **Privacidade por padrão:** enviar ao modelo apenas os dados necessários, conforme política de segurança, contrato do provedor e base legal validada.
8. **Avaliação contínua:** testar qualidade, citações, vazamento entre tenants, prompt injection e comportamento diante de documentos maliciosos.
9. **Rastreabilidade:** registrar modelo/versão, fontes consultadas, usuário, data e ação realizada conforme a política definida.
10. **Portabilidade:** separar a lógica de negócio do fornecedor de modelo, permitindo troca do provedor quando necessário.

### 5.2 Funcionalidades de IA por caso de uso

#### A. Assistente de legislação e processo legislativo

- Pesquisa em leis municipais, Regimento Interno, Lei Orgânica, proposições, pareceres, atas e documentos autorizados.
- Respostas com fontes e links para o documento original.
- Comparação de versões de proposições e normas.
- Localização de dispositivos relacionados e referências cruzadas.
- Resumo do histórico de tramitação de uma proposição.
- Preparação de síntese para reunião ou sessão.
- Sinalização de possíveis duplicidades ou conflitos textuais para revisão humana, sem emitir conclusão jurídica definitiva.

**Exemplo:** “Quais proposições sobre transporte escolar tiveram tramitação nos últimos dois anos?” A resposta deve listar resultados encontrados, critérios da busca e links para cada proposição.

#### B. Assistente de documentos e protocolo

- Classificar documentos por assunto e tipo, sugerindo categoria e destino.
- Extrair metadados de arquivos recebidos, com conferência pelo servidor.
- Resumir expedientes extensos e gerar índice.
- Identificar documentos faltantes em um processo a partir de checklist configurado.
- Rascunhar despachos e comunicações com base em modelos aprovados.
- Encontrar documentos semelhantes em arquivos autorizados.

**Controle:** documentos externos devem ser tratados como conteúdo não confiável. Instruções encontradas dentro de um PDF nunca devem sobrepor as regras do sistema ou autorizar acesso a outros dados.

#### C. Assistente de compras, licitações e contratos

- Resumir objetos, valores, prazos e obrigações de editais e contratos.
- Apontar datas importantes e sugerir alertas para conferência.
- Comparar versões de contrato e destacar cláusulas alteradas.
- Verificar presença de itens em uma lista de documentos exigidos pelo processo configurado.
- Classificar demandas de compra e sugerir agrupamento por categoria.
- Identificar indícios de inconsistência em quantidades, valores ou datas para avaliação humana.
- Elaborar rascunhos de ETP/TR a partir de dados autorizados e modelos aprovados, com indicação dos trechos que exigem validação.

**Limites:** não decidir modalidade de contratação, habilitação, julgamento ou adjudicação; não inventar pesquisa de preços, fornecedor, cotações ou evidências.

#### D. Assistente de RH

- Responder a dúvidas internas a partir de políticas e regulamentos aprovados, respeitando permissões.
- Localizar políticas de férias, licenças e procedimentos.
- Preparar checklists de admissão, desligamento ou mudança de vínculo.
- Extrair pendências documentais de forma assistida.

**Limites:** não expor dados funcionais a usuários sem autorização; não decidir contratação, promoção, disciplina ou qualquer matéria de pessoal por critérios automatizados sem análise legal específica.

#### E. Assistente do almoxarifado e patrimônio

- Consultar saldo, giro, itens sem movimentação e estoque mínimo usando dados operacionais reais.
- Prever consumo apenas depois de existir histórico suficiente e mensurar erro do modelo.
- Sugerir itens para reposição, apresentando dados e pressupostos.
- Classificar solicitações e indicar itens semelhantes no catálogo.
- Ajudar a conciliar divergências de inventário, sem alterar saldo autonomamente.
- Responder “onde está o bem?” com base no registro atualizado e no histórico de movimentação.

#### F. Assistente de transparência e atendimento ao cidadão

- Ajudar o cidadão a encontrar leis, despesas, sessões, contratos e dados já publicados.
- Guiar o usuário até o formulário adequado para uma solicitação.
- Sugerir classificação inicial da manifestação para o servidor responsável.
- Rascunhar respostas com base na informação validada pela Câmara.
- Detectar possíveis duplicidades e acompanhar prazos.

**Limites:** nunca divulgar registros restritos, não prometer concessão de um pedido, não tomar a decisão administrativa sobre o atendimento e não transformar manifestação sigilosa em conteúdo público.

#### G. Assistente de gestão e indicadores

- Explicar variações em indicadores a partir dos dados disponíveis.
- Resumir pendências vencidas e contratos próximos do vencimento.
- Gerar relatórios narrativos a partir de consultas autorizadas e reproduzíveis.
- Permitir perguntas em linguagem natural sobre métricas previamente definidas.
- Explicar fórmula, período, filtros e fonte dos indicadores.

**Controle:** toda resposta numérica precisa ser baseada em consulta determinística ou cálculo verificável, não em estimativa textual do modelo.

#### H. Transcrição e resumo de sessões

- Transcrição de áudio/vídeo quando houver base legal, qualidade técnica e autorização.
- Separação aproximada de falas, sujeita a correção humana.
- Rascunho de resumo por item da pauta.
- Indexação de trechos por tema, orador ou instante de vídeo.
- Busca por expressão ou assunto no acervo audiovisual.

**Limites:** transcrição gerada por IA não é ata oficial. Erros de nomes, falas, números e votações devem ser corrigidos antes de publicação ou uso formal.

### 5.3 Arquitetura lógica da IA

A arquitetura deve ser desacoplada dos módulos de negócio e conter, no mínimo:

1. **AI Gateway:** interface comum entre a plataforma e os provedores de modelos.
2. **Política de acesso:** identifica usuário, Câmara, permissões e finalidade antes de recuperar documentos.
3. **Ingestão documental:** extração de texto, metadados, versão, classificação de acesso e fonte.
4. **Busca híbrida:** combinação de pesquisa textual e semântica quando fizer sentido.
5. **RAG (geração aumentada por recuperação):** recupera trechos autorizados e fornece contexto ao modelo, em vez de pedir respostas apenas com memória geral.
6. **Referências de fonte:** cada trecho recuperado deve manter vínculo com documento, versão e localização.
7. **Validação de saída:** verifica presença de fontes quando obrigatórias, formato, campos e limites de conteúdo.
8. **Log de uso:** registro de modelo, latência, custo estimado, usuário, tenant, fontes e feedback, sem registrar segredos desnecessários.
9. **Avaliação:** conjunto de perguntas de teste com respostas esperadas, avaliação humana e detecção de regressões.
10. **Limites operacionais:** quota por tenant/usuário, controle de custos, rate limits e fallback quando o provedor estiver indisponível.

### 5.4 Gestão de documentos para RAG

- Documentos devem ser particionados em trechos preservando títulos, seções, números e referências.
- Metadados mínimos: `tenant_id`, módulo/origem, tipo de documento, identificador interno, versão, data, classificação de acesso e status de publicação.
- Remoções e mudanças de permissão devem propagar-se aos índices de busca.
- Os índices devem impedir recuperação entre tenants e suportar filtros de permissão.
- Trechos recuperados devem ser apresentados com links para o registro original.
- A resposta precisa diferenciar texto literal, resumo, inferência e sugestão.
- Nenhum conteúdo inserido por usuário externo pode ser tratado como instrução de sistema.

### 5.5 Fases de implantação da IA

| Fase | Entrega | Dependências | Condição para liberar |
|---|---|---|---|
| IA-0 | Base de governança, seleção de provedor, avaliação de privacidade, limites de custo e registro | Arquitetura e segurança | Aprovação técnica e de proteção de dados |
| IA-1 | Busca assistida em documentos públicos, com fontes | Catálogo e indexação de documentos | Testes de citação e ausência de vazamento |
| IA-2 | Busca interna com permissões herdadas | Identidade, RBAC e isolamento multi-tenant | Testes de acesso negativo entre perfis e tenants |
| IA-3 | Resumos e rascunhos revisáveis | Fluxos de aprovação e versionamento | Revisão humana obrigatória; original preservado |
| IA-4 | Classificação de solicitações e assistentes por módulo | Taxonomia e exemplos rotulados | Métrica de qualidade e possibilidade de corrigir a classificação |
| IA-5 | Indicadores em linguagem natural e previsões operacionais | Dados limpos e métricas determinísticas | Validação contra consultas de referência e avaliação de erro |
| IA-6 | Voz, transcrição e busca audiovisual | Acervo, infraestrutura, direitos e revisão | Precisão medida e revisão antes de publicação |

### 5.6 Métricas específicas de IA

- Precisão da recuperação documental.
- Percentual de respostas com fonte correta e suficiente.
- Percentual de respostas que declaram incerteza quando as fontes são insuficientes.
- Taxa de citações inválidas ou que não sustentam a afirmação.
- Taxa de vazamento em testes de permissão e tenant: objetivo zero.
- Taxa de correção humana por tipo de tarefa.
- Tempo economizado em tarefas repetitivas, validado por amostragem.
- Latência, custo médio por operação e falhas por provedor.
- Satisfação do usuário por função.

Não colocar IA em produção para tarefas sensíveis apenas porque a demonstração parece convincente. A liberação depende de avaliação formal, dados de teste representativos e processo de correção.

---

## 6. Plataforma SaaS multi-tenant

O sistema deve nascer preparado para múltiplas Câmaras, mesmo que o piloto inicial seja realizado com apenas uma.

### 6.1 Conceitos

- **Plataforma:** operação SaaS, planos, configuração global, monitoramento e suporte.
- **Tenant:** uma Câmara Municipal cliente.
- **Usuário:** identidade que pode atuar em uma ou mais Câmaras quando autorizado.
- **Membership/vínculo:** relação explícita entre usuário, tenant, papel e estado de acesso.
- **Módulo contratado:** capacidade ativada para determinado tenant.
- **Configuração do tenant:** regras institucionais, branding, numerações, parâmetros e integrações.

### 6.2 Isolamento de dados

Decidir a estratégia final após auditoria e análise de banco/infraestrutura. Como linha inicial, o isolamento lógico por `tenant_id` pode funcionar com banco relacional compartilhado, desde que todas as tabelas tenant-scoped e todas as consultas sejam protegidas de modo consistente.

Requisitos:

- A identificação do tenant deve vir de contexto confiável e ser validada no servidor; não confiar cegamente em um valor enviado pelo navegador.
- Cada registro tenant-scoped deve ter associação obrigatória a um tenant.
- Leituras, criações, alterações, exclusões, relatórios, exportações, busca e tarefas em segundo plano devem aplicar o tenant correto.
- Revisar jobs assíncronos, cache, busca textual/semântica, URLs de arquivos, logs e integrações externas.
- Testes automatizados devem tentar consultar ou modificar dados de outra Câmara.
- Nenhum usuário da Câmara A pode obter acesso à Câmara B alterando identificadores de URL, corpo de requisição ou filtros.
- Rotinas de suporte excepcional devem ser limitadas, autorizadas, temporárias e auditadas.
- Separar ambientes de desenvolvimento, teste e produção, usando dados sintéticos ou anonimizados para testes.

### 6.3 Configuração por tenant

- Dados institucionais e identidade visual.
- Fuso horário e formatos de data/moeda.
- Estrutura organizacional e mandatos.
- Tipos de documentos, numeração, modelos e fluxos.
- Regras regimentais e prazos, com fonte e data de validação.
- Módulos habilitados e quotas.
- Integrações e respectivas credenciais protegidas.
- Preferências de notificações e publicação.
- Políticas específicas, dentro do que a plataforma permite.

### 6.4 Módulos, planos e cobrança

A implementação comercial pode incluir:

- Cadastro de planos, módulos e recursos habilitados.
- Período de avaliação e onboarding.
- Limites por usuários, armazenamento, consumo de IA ou volume de documentos.
- Faturas, pagamentos, inadimplência e histórico comercial.
- Estados do contrato: teste, ativo, suspensão administrativa, cancelamento e encerramento.
- Exportação de dados e procedimento de saída do cliente.

A cobrança não deve apagar nem tornar inacessíveis dados públicos ou registros legalmente sujeitos a retenção. Política de suspensão, exportação e encerramento precisa de análise contratual e jurídica.

---

## 7. Arquitetura técnica proposta

### 7.1 Diretriz inicial

Adotar, em princípio, um **monólito modular**, se a tecnologia atual permitir. Um único produto implantável, dividido em módulos internos com contratos claros, costuma ser mais simples para uma equipe pequena do que dezenas de microsserviços.

**Importante:** a stack real do projeto não foi confirmada nesta especificação. A auditoria deve identificar as tecnologias existentes e justificar qualquer mudança; este documento não determina que o sistema já use uma stack específica.

### 7.2 Componentes lógicos

1. **Frontend:** interfaces autenticadas, área administrativa, legislativa, gabinetes e portal público responsivo.
2. **Backend/API:** regras de negócio, validação, autorização, transações, integrações e documentação dos endpoints.
3. **Banco relacional:** entidades transacionais, integridade referencial, índices e migrações controladas.
4. **Armazenamento de objetos:** documentos e mídias privados/públicos, conforme classificação, com links de acesso temporário quando necessário.
5. **Fila e workers:** notificações, geração de documentos, importações, sincronização e tarefas demoradas.
6. **Busca:** indexação e pesquisa textual; busca semântica para IA apenas com filtros de tenant e permissões.
7. **AI Gateway:** adaptação de modelos, orçamento, logs e políticas de uso.
8. **Integrações:** conectores versionados, retentativas, idempotência e monitoramento.
9. **Observabilidade:** logs estruturados, métricas, rastreamento de erros e alertas.
10. **Administração do SaaS:** clientes, planos, recursos, configuração e suporte.

### 7.3 Limites entre módulos

Cada módulo deve controlar suas regras e expor operações claras aos demais. Um módulo não deve modificar diretamente tabelas internas de outro módulo sem um contrato estabelecido.

Exemplo: compras registra o processo e o contrato; estoque registra o recebimento e a movimentação; financeiro acompanha os dados financeiros; transparência consome os campos liberados para publicação. Os vínculos devem preservar identificadores de origem e estado de cada processo.

### 7.4 Eventos e tarefas assíncronas

Eventos úteis incluem:

- `RequestSubmitted`
- `RequestApproved`
- `StockMovementPosted`
- `PurchaseProcessUpdated`
- `ContractExpiringSoon`
- `LegislativeMatterMoved`
- `SessionPublished`
- `CitizenRequestReceived`
- `CitizenRequestDeadlineApproaching`
- `DocumentPublished`

Os nomes são exemplos internos, não um contrato definitivo. Eventos devem conter identificadores necessários, evitar dados sensíveis desnecessários e respeitar tenant e permissões. Tarefas reprocessáveis precisam ser idempotentes para evitar envio duplicado ou movimentação duplicada.

### 7.5 API e versionamento

- Documentar endpoints e schemas.
- Validar entrada no servidor e padronizar erros.
- Autenticar e autorizar cada ação relevante.
- Aplicar limites de requisição nos pontos expostos publicamente.
- Versionar integrações e mudanças incompatíveis.
- Não devolver campos restritos por conveniência.
- Registrar chamadas externas e respostas relevantes, sem armazenar segredos.

### 7.6 Ambientes e entrega contínua

- Desenvolvimento local.
- Teste/CI.
- Homologação/staging.
- Produção.
- Segredos em cofre ou variáveis protegidas, não em código.
- Build e testes automatizados antes da implantação.
- Migração de banco revisada e plano de rollback.
- Backup anterior a mudanças críticas.
- Monitoramento após implantação.

---

## 8. Modelo de dados conceitual

Esta é uma lista inicial de domínios; não é um esquema SQL final. Os nomes e relacionamentos devem ser validados contra o código existente e contra os fluxos definidos.

### 8.1 Plataforma e identidade

- `Tenant`
- `TenantSettings`
- `User`
- `TenantMembership`
- `Role`
- `Permission`
- `RolePermission`
- `MembershipRole`
- `ModuleEntitlement`
- `Subscription` (quando a cobrança estiver no escopo)
- `AuditEvent`
- `IntegrationConnection`
- `NotificationPreference`

### 8.2 Estrutura e protocolo

- `OrganizationalUnit`
- `InstitutionalPosition`
- `Mandate`
- `BoardComposition`
- `Committee`
- `Document`
- `DocumentVersion`
- `DocumentAttachment`
- `ProtocolRecord`
- `WorkflowInstance`
- `WorkflowTask`
- `WorkflowTransition`
- `SignatureRequest` (se adotado provedor de assinatura)

### 8.3 Legislativo

- `LegislativeMatter`
- `MatterType`
- `MatterAuthor`
- `MatterVersion`
- `MatterMovement`
- `CommitteeOpinion`
- `Session`
- `SessionAgendaItem`
- `AttendanceRecord`
- `VoteRecord`
- `SessionMinutes`
- `LegalNorm`
- `LegalNormVersion`
- `PublicationRecord`

### 8.4 Administração

- `EmployeeRecord`
- `EmploymentRelationship`
- `PositionAssignment`
- `LeaveRecord`
- `VacationRecord`
- `AttendanceRecord` (diferenciar domínio de frequência funcional do registro de presença em sessão)
- `PayrollIntegrationBatch`
- `PayrollIntegrationResult`

### 8.5 Compras, estoque e patrimônio

- `MaterialItem`
- `Warehouse`
- `StockBalance` (se adotado como projeção consistente)
- `StockMovement`
- `StockRequisition`
- `StockRequisitionItem`
- `Supplier`
- `ProcurementDemand`
- `ProcurementProcess`
- `ProcurementDocument`
- `Contract`
- `ContractAmendment`
- `ContractObligation`
- `ContractInspectionRecord`
- `Asset`
- `AssetMovement`
- `InventoryCount`
- `Vehicle`
- `MaintenanceOrder`

### 8.6 Financeiro e conformidade

- `BudgetAllocation`
- `ExpenseRequest`
- `FinancialReference` (para referência a sistema externo, quando aplicável)
- `TravelAllowance`
- `AccountabilityReport`
- `ControlFinding`
- `RiskRecord`
- `ActionPlan`
- `EvidenceRecord`
- `ComplianceChecklist`

### 8.7 Cidadão e transparência

- `CitizenRequest`
- `OmbudsmanManifestation`
- `InformationAccessRequest`
- `CitizenRequestMessage`
- `PublicationDataset`
- `PublicationJob`
- `PublicAttachment`

### 8.8 IA

- `AIUsageEvent`
- `AIConversation` (quando necessário e com retenção definida)
- `AIMessage`
- `AIWorkflowRun`
- `AIEvaluationCase`
- `DocumentIndexJob`
- `DocumentChunkReference`
- `AIUserFeedback`

### 8.9 Regras de modelagem

- Entidades com escopo de Câmara devem possuir tenant explícito e restrições adequadas.
- Usar chaves únicas compostas quando o número oficial for único apenas dentro de uma Câmara/ano/legislatura.
- Datas de vigência precisam permitir consulta histórica.
- Estados de processos devem ser explícitos e validados por transições autorizadas, em vez de strings livres.
- Valores monetários devem usar tipo decimal adequado, nunca ponto flutuante binário para cálculo financeiro.
- Operações financeiras/estoque/votação devem preservar evento histórico; correção deve ser registrada, não apagada silenciosamente.
- Arquivos devem ter metadados e estado de varredura/validação quando aplicável.
- Dados sensíveis devem ter classificação, política de acesso e retenção.

---

## 9. Segurança, privacidade e auditoria

### 9.1 Controles mínimos

- Autenticação segura; MFA para perfis privilegiados quando viável.
- Política de senhas e sessões, proteção contra força bruta e recuperação de conta segura.
- RBAC/ABAC e validação no backend.
- Isolamento multi-tenant em todas as camadas.
- Princípio do menor privilégio.
- Segredos fora do repositório e com rotação.
- Criptografia em trânsito e proteção adequada para dados armazenados.
- URLs temporárias e controle de acesso para arquivos privados.
- Proteção contra CSRF/XSS/SQL injection/SSRF e demais riscos relevantes à stack escolhida.
- Validação de uploads, limites de tamanho, tipos permitidos e análise contra malware quando aplicável.
- Logs de auditoria protegidos contra alteração indevida.
- Alertas de acessos anormais e erros recorrentes.
- Plano de resposta a incidentes e comunicação.
- Backup automático, criptografado e isolado do ambiente principal.
- Teste periódico de restauração; backup sem teste de restauração não é evidência suficiente de recuperação.
- Revisão de dependências, atualizações de segurança e gestão de vulnerabilidades.

### 9.2 LGPD e governança de dados

- Mapear dados pessoais e finalidade de tratamento por módulo.
- Definir os papéis de cada parte no tratamento conforme contrato e operação real.
- Coletar apenas os dados necessários.
- Controlar retenção, correção, exportação, anonimização e eliminação conforme obrigação legal/arquivística aplicável.
- Identificar dados e documentos sujeitos a sigilo ou restrição de acesso.
- Manter registro de operações de tratamento conforme necessário.
- Formalizar contratos com provedores de hospedagem, IA, mensageria e assinatura, incluindo segurança e tratamento de dados.
- Avaliar se é necessária uma avaliação de impacto ou documento equivalente para tratamentos de maior risco.
- Estabelecer procedimento para incidentes, pedidos de titulares e demandas da Câmara.

### 9.3 Auditoria

Todo evento relevante deve registrar, dentro da política definida:

- tenant;
- usuário/identidade que executou a ação;
- ação e recurso afetado;
- data/hora com fuso controlado;
- resultado da tentativa;
- identificador de correlação;
- justificativa quando exigida;
- origem técnica relevante, sem coletar dados excessivos.

A trilha de auditoria deve permitir reconstruir eventos críticos e impedir exclusão/alteração por usuários comuns. A retenção precisa ser definida por requisitos legais, contratuais e de segurança.

---

## 10. Integrações externas

Nenhuma integração deve ser considerada concluída apenas por ter uma tela de configuração. Ela precisa de documentação, credenciais seguras, testes, monitoramento, controle de duplicidade e tratamento de erros.

| Integração | Objetivo | Estratégia inicial | Dependências/alertas |
|---|---|---|---|
| PNCP | Publicação/consulta de dados de contratação quando aplicável | Estudar API/documentação e validar fluxo antes da implementação | Credenciais, esquema vigente, regras do órgão, respostas e retificações |
| eSocial | Envio/validação de eventos funcionais quando aplicável | Primeiro mapear fornecedor e dados necessários; implementar conector por versão | Leiautes atualizados, certificados, eventos, protocolos e retificações |
| Sistema contábil/folha | Evitar duplicidade e apoiar prestação de contas | Priorizar importação/exportação documentada e integração com fornecedor atual | Formatos, contratos, regras contábeis e testes de conciliação |
| TCE-MS/e-Sfinge | Apoiar remessa e validação de dados exigidos | Analisar manuais vigentes do exercício e os serviços disponíveis | Regras de remessa mudam; validar com TI/contabilidade e documentação atual |
| Assinatura eletrônica | Assinar e validar documentos | Selecionar provedor conforme exigência jurídica, segurança e custo | Validade, identidade, trilha de evidências e armazenamento |
| E-mail | Notificações institucionais | SMTP/API com logs e retentativas | Entregabilidade, domínio, privacidade e limites |
| SMS/WhatsApp | Alertas ou notificações, se aprovados | Conector opcional; nunca requisito para uso básico | Consentimento/base legal, custo, janela de envio, template e privacidade |
| Vídeo/transmissão | Sessões e busca audiovisual | Vínculo com URLs e metadados antes de construir streaming próprio | Direitos, disponibilidade, retenção e acessibilidade |
| Mapas/QR/códigos | Localização patrimonial ou outros usos | Avaliar após validar necessidade operacional | Custo, funcionamento offline e segurança do link |

### 10.1 Requisitos de cada conector

- Especificação de campos e mapeamento de origem/destino.
- Ambiente de teste/homologação quando disponível.
- Controle de versão da API/leiaute.
- Idempotência e proteção contra duplicidade.
- Retentativa com backoff para falhas transitórias.
- Fila de erros e ferramenta de reprocessamento autorizado.
- Armazenamento de protocolo/recibo de envio.
- Métrica de sucesso e alerta de falha.
- Teste de conciliação entre sistema de origem e destino.
- Documentação de credenciais, validade e rotação.

---

## 11. Portal público, acessibilidade e experiência

### 11.1 Áreas públicas recomendadas

- Página inicial da Câmara com informações institucionais e contatos.
- Vereadores, Mesa Diretora e comissões.
- Agenda e calendário institucional.
- Sessões, pautas, atas e vídeos quando disponíveis.
- Projetos e tramitações legislativas.
- Busca e consulta de leis e normas.
- Transparência por tema, com data de atualização.
- Licitações, contratos e documentos divulgáveis.
- Ouvidoria e SIC.
- Notícias e avisos.
- Links, serviços, perguntas frequentes e acessibilidade.

### 11.2 Experiência autenticada

- Painel por perfil com tarefas, prazos e indicadores relevantes.
- Filtros persistentes e pesquisa geral autorizada.
- Formulários com validação clara e preservação de rascunhos quando seguro.
- Histórico da solicitação e explicação do próximo passo.
- Notificações sem revelar conteúdo sensível no assunto ou na tela bloqueada.
- Exportações em formatos previsíveis.
- Ajuda contextual e mensagens de erro compreensíveis.

### 11.3 Acessibilidade e mobile

- HTML semântico e navegação por teclado.
- Foco visível e ordem lógica.
- Rótulos associados aos campos e mensagens de validação acessíveis.
- Contraste adequado e informações não dependentes apenas da cor.
- Texto redimensionável e layout responsivo.
- Alternativas textuais para imagens informativas.
- Tabelas com cabeçalhos e estrutura acessível.
- Documentos publicados com acessibilidade sempre que possível.
- Teste com leitor de tela e dispositivos reais antes do lançamento.

---

## 12. Relatórios e indicadores

O sistema deve separar dados operacionais, indicadores calculados e informações publicáveis. Cada indicador deve ter fórmula, fonte, período, filtros e responsável definidos.

### 12.1 Painel institucional

- Pendências por setor, responsável, idade e prazo.
- Processos aguardando aprovação.
- Documentos a vencer ou sem evidência obrigatória.
- Agenda próxima e tarefas críticas.
- Status de integrações e publicações.

### 12.2 Painel legislativo

- Proposições por tipo, autor, situação e período.
- Tempo de tramitação por etapa, com interpretação cuidadosa.
- Pautas e atas pendentes.
- Sessões realizadas e documentos publicados.
- Produção legislativa com definição transparente dos critérios.

### 12.3 Painel administrativo

- Requisições de estoque por status e tempo de atendimento.
- Consumo por setor/item/período.
- Contratos a vencer, obrigações e ocorrências.
- Bens sem conferência recente, manutenção pendente e inventários.
- Demandas de atendimento, ouvidoria e SIC por prazo e situação.

### 12.4 Qualidade e governança dos dados

- Data/hora da última atualização.
- Fonte e origem do registro.
- Indicador de falha ou incompletude de integração.
- Regras de cálculo documentadas.
- Possibilidade de exportar os dados de base para conferência.
- Não usar IA para gerar valores numéricos sem consulta a fonte estruturada verificável.

---

## 13. Estratégia de entrega e roadmap

O roadmap é uma ordem sugerida, não um cronograma fechado. A ordem pode mudar após a auditoria técnica e conversas com usuários reais.

### Fase 0 — Diagnóstico do legado e descoberta

**Objetivo:** saber exatamente o que já existe e definir uma linha de base.

Entregáveis:

- Inventário do repositório e instruções para executar localmente.
- Mapa de páginas, módulos e fluxos.
- Mapa do banco, entidades, migrações e dados de demonstração.
- Identificação de autenticação e autorização.
- Mapa de APIs, provedores e variáveis de ambiente necessárias.
- Relatório de bugs, riscos, código duplicado e testes existentes.
- Matriz “manter / corrigir / refatorar / construir / remover”.
- Lista de decisões técnicas e perguntas de negócio.

**Critério de saída:** equipe consegue explicar como iniciar, testar e implantar o projeto e quais partes podem ser reaproveitadas.

### Fase 1 — Fundação SaaS e segurança

- Tenant, membership e resolução confiável do tenant ativo.
- Perfis e permissões verificadas no backend.
- Migrações, logging, auditoria e identificadores de correlação.
- Armazenamento de arquivos com política de acesso.
- Gestão inicial das configurações da Câmara.
- Testes de isolamento entre dois tenants.
- CI com lint, tipos, testes e verificações disponíveis na stack.

**Critério de saída:** fluxos essenciais passam nos testes de permissão e tentativas de acessar dados de outro tenant falham.

### Fase 2 — Administração, documentos e workflow

- Estrutura institucional e cadastros comuns.
- Protocolo, documentos e versões.
- Tarefas, aprovações e notificações.
- Painéis de pendências.
- Auditoria de ações críticas.

**Critério de saída:** pelo menos um processo real pode ser aberto, encaminhado, aprovado, concluído e auditado de ponta a ponta.

### Fase 3 — Primeiro módulo operacional

Recomendação para piloto: estoque e requisições, patrimônio básico ou protocolo, conforme o que já existir e o acesso a usuários para validação.

- Fluxo de ponta a ponta e tratamento de erros.
- Relatórios e exportação.
- Teste de concorrência, duplicidade e cancelamento.
- Treinamento e feedback de usuários.

**Critério de saída:** uso real acompanhado, com conciliação dos registros e sem falhas críticas conhecidas.

### Fase 4 — Processo legislativo

- Tipos de proposição e configuração das regras da Câmara-piloto.
- Autores, versões, tramitação e comissões.
- Sessões, pauta, votação e atas conforme regra validada.
- Publicação e consulta pública.
- Importação gradual de legislação/documentos quando viável.

**Critério de saída:** ciclo legislativo piloto validado por profissional da Câmara e com histórico verificável.

### Fase 5 — Transparência, SIC e ouvidoria

- Conjuntos de dados publicados e calendário de atualização.
- Portal público acessível.
- Protocolo e resposta de pedidos/manifestações.
- Controle de sigilo, classificação e acesso.
- Painel de pendências de transparência.

**Critério de saída:** matriz de publicação revisada por responsáveis e testes de acesso público versus restrito concluídos.

### Fase 6 — Compras, contratos, patrimônio e financeiro integrado

- Planejamento e processo de compra.
- Gestão contratual e alertas.
- Patrimônio/frota, caso ainda não esteja completo.
- Integração com sistema contábil/folha escolhido.
- Conectores oficiais conforme documentação atual.

**Critério de saída:** dados reconciliados com sistemas de origem, falhas de integração controladas e validação por especialistas.

### Fase 7 — RH avançado e obrigações

- Processos funcionais mais completos.
- Interface de integração de folha e eSocial.
- Eventos, retornos, retificações e logs.
- Revisão de segurança de dados pessoais.

**Critério de saída:** cenários reais testados com responsável de RH/contabilidade e documentação dos leiautes aplicáveis.

### Fase 8 — IA aplicada e expansão SaaS

- Assistente documental com fonte e autorização.
- Busca semântica isolada por tenant.
- Resumos, rascunhos e classificação revisável.
- Métricas de qualidade/custo e botão de feedback.
- Planos, billing, onboarding, suporte e monitoramento.
- PWA e recursos de comunicação.

**Critério de saída:** avaliação de IA aprovada, testes de vazamento zerados, custos monitorados e processo de suporte definido.

---

## 14. Backlog inicial de épicos

IDs iniciais para organizar o trabalho. Eles devem ser convertidos em issues menores após a auditoria do projeto.

| ID | Épico | Prioridade | Dependência | Resultado esperado |
|---|---|---|---|---|
| CD-001 | Auditoria do repositório existente | P0 | Nenhuma | Inventário verificável e plano de reaproveitamento |
| CD-002 | Execução local e documentação de desenvolvimento | P0 | CD-001 | Desenvolvedor consegue iniciar e testar o projeto |
| CD-003 | Modelo de tenant e membership | P0 | CD-001 | Base multi-Câmara com tenant explícito |
| CD-004 | Autorização e políticas de acesso | P0 | CD-003 | Permissões verificadas em todas as operações relevantes |
| CD-005 | Testes automáticos de isolamento | P0 | CD-003, CD-004 | Regressões de vazamento entre tenants detectadas |
| CD-006 | Migrações e integridade dos dados | P0 | CD-001 | Banco versionado e integridade documentada |
| CD-007 | Auditoria de eventos críticos | P0 | CD-004 | Histórico confiável de ações relevantes |
| CD-008 | Configuração institucional por Câmara | P1 | CD-003 | Configuração sem alterações de código |
| CD-009 | Documentos e armazenamento seguro | P1 | CD-004 | Anexos e versões com controle de acesso |
| CD-010 | Workflow e tarefas | P1 | CD-004, CD-009 | Processos com estados, responsáveis e prazos |
| CD-011 | Protocolo e comunicações | P1 | CD-009, CD-010 | Entrada, encaminhamento e histórico de documentos |
| CD-012 | Estoque e requisições | P1 | CD-010 | Movimentação consistente e auditada |
| CD-013 | Patrimônio | P2 | CD-009 | Cadastro e histórico de bens |
| CD-014 | Administração legislativa | P1 | CD-003, CD-009 | Configuração de mandatos, comissões e estrutura |
| CD-015 | Proposições e tramitação | P1 | CD-014, CD-010 | Ciclo de proposições rastreável |
| CD-016 | Sessões, pauta, votação e atas | P1 | CD-015 | Fluxo de sessão validado pelo Regimento |
| CD-017 | Catálogo de normas e pesquisa pública | P1 | CD-009, CD-015 | Legislação pesquisável e versionada |
| CD-018 | Transparência e publicação | P1 | Dados confiáveis dos módulos | Conjuntos publicáveis e atualizáveis |
| CD-019 | SIC e ouvidoria | P1 | CD-010, CD-009 | Protocolo, prazo, restrições e resposta |
| CD-020 | Compras e contratos | P2 | CD-010, CD-011 | Processo de contratação rastreável |
| CD-021 | Integração PNCP | P2 | CD-020 | Publicação/consulta conforme fluxo validado |
| CD-022 | Financeiro e integrações contábeis | P2 | Definição de fornecedor/escopo | Dados reconciliados e exportações confiáveis |
| CD-023 | Cadastro de RH e eventos funcionais | P2 | CD-004 | Dados funcionais protegidos |
| CD-024 | Integração eSocial | P2 | CD-023 | Eventos validados segundo leiaute vigente |
| CD-025 | Integração e-Sfinge/TCE-MS | P2 | CD-022 | Requisitos do exercício documentados e testados |
| CD-026 | Portal público acessível e PWA | P1 | CD-017, CD-018 | Experiência móvel e pública adequada |
| CD-027 | AI Gateway e governança de IA | P1 | CD-004, CD-009 | Uso controlado de modelos e custo monitorado |
| CD-028 | Busca documental com citações | P1 | CD-017, CD-027 | Respostas com fontes autorizadas |
| CD-029 | Resumo e rascunho com revisão humana | P2 | CD-009, CD-027 | Produção assistida sem substituir atos oficiais |
| CD-030 | Avaliação e segurança de IA | P0 para IA em produção | CD-027 | Testes de vazamento, citação, prompt injection e qualidade |
| CD-031 | Planos, onboarding e billing | P2 | Base SaaS estável | Operação comercial de novas Câmaras |
| CD-032 | Monitoramento, backup e recuperação | P0 | Ambiente implantável | Alertas e restauração comprovada |

**Legenda sugerida:** P0 = bloqueador/base; P1 = primeira entrega operacional; P2 = expansão posterior. A prioridade pode mudar após a auditoria e o piloto.

---

## 15. Critérios de aceite transversais

Uma funcionalidade só deve ser considerada concluída quando cumprir, conforme aplicável:

### 15.1 Funcionalidade

- Fluxo principal implementado de ponta a ponta.
- Estados válidos e transições documentadas.
- Validação de campos e mensagens de erro compreensíveis.
- Casos de cancelamento, edição, duplicidade e falha tratados.
- Critério de aceite revisado pelo responsável do módulo.

### 15.2 Segurança

- Autorização verificada no backend.
- Isolamento entre tenants testado.
- Dados sensíveis não expostos por logs, URLs ou mensagens.
- Acesso a anexos validado no servidor.
- Auditoria ativa para ações críticas.

### 15.3 Qualidade

- Testes unitários para regras relevantes.
- Testes de integração para banco, API e workflow.
- Testes de ponta a ponta para fluxos críticos.
- Lint, tipagem e build passando na stack real.
- Migração e procedimento de retorno revisados quando houver mudança no banco.
- Sem segredos adicionados ao repositório.

### 15.4 Usabilidade e acessibilidade

- Layout adequado para computador e celular.
- Teclado e foco funcionais.
- Rótulos e erros acessíveis.
- Estados de carregamento, vazio, sucesso e falha tratados.
- Instruções claras para tarefas comuns.

### 15.5 Operação

- Logs e métricas suficientes para investigar falhas.
- Alertas para integrações e tarefas críticas.
- Backups e recuperação definidos para dados importantes.
- Documentação de configuração atualizada.
- Dono funcional e responsável técnico identificados.

### 15.6 IA

- Fontes visíveis quando exigidas.
- Autorização e tenant respeitados no retrieval e na resposta.
- Cenários de incerteza, prompt injection e documento malicioso testados.
- Resposta editável antes de uso formal.
- Custos e latência monitorados.
- Feedback e correção registrados.
- Nenhuma ação de alto impacto executada automaticamente sem regra expressa e revisão adequada.

---

## 16. Riscos e decisões em aberto

### 16.1 Riscos principais

| Risco | Impacto | Mitigação inicial |
|---|---|---|
| Reescrever módulos que já funcionam | Alto retrabalho | Auditar código e criar matriz de reaproveitamento |
| Vazamento entre Câmaras | Crítico | Tenant obrigatório, autorização no backend e testes negativos |
| Escopo grande demais | Alto atraso e produto incompleto | Roadmap por fases, piloto e critérios de saída |
| Regras legislativas divergentes | Atos incorretos | Configuração e validação do Regimento/Lei Orgânica de cada cliente |
| Integração oficial mudar | Dados rejeitados | Adaptadores versionados e monitoramento de documentação |
| IA produzir informação falsa | Decisão ou publicação incorreta | RAG, fontes, incerteza, revisão humana e avaliações |
| Custo imprevisível de IA/armazenamento | Margem negativa | Quotas, medição por tenant e alertas de custo |
| Dados funcionais/denúncias expostos | Dano jurídico e reputacional | Classificação, permissões estritas, logs e revisão de privacidade |
| Dependência de fornecedor | Bloqueio de produto | Camada de abstração, exportação de dados e avaliação de portabilidade |
| Falhas de backup | Perda de dados | Restauração periódica e plano de recuperação testado |
| Dificuldade de implantação | Custo de onboarding alto | Importação padronizada, templates e checklist de implantação |
| Escopo de folha/contabilidade subestimado | Alto risco financeiro e legal | Integrar primeiro com sistemas especializados e validar escopo |

### 16.2 Decisões que exigem evidência

- Qual é a stack atual e quais módulos já estão executáveis?
- Qual banco, autenticação, armazenamento e hospedagem já existem?
- Quais telas vistas nos protótipos correspondem a funcionalidades reais?
- Qual Câmara ou grupo de usuários poderá validar o primeiro piloto?
- Qual módulo gera mais valor e pode ser validado mais cedo?
- Qual sistema contábil/folha precisa ser integrado primeiro?
- Qual provedor de IA atende aos requisitos de privacidade, custo e qualidade?
- Quais serviços oficiais têm APIs ou leiautes apropriados ao fluxo escolhido?
- Quais documentos e dados precisam ser importados na primeira implantação?
- Quais níveis de serviço, suporte e recuperação serão oferecidos comercialmente?

---

## 17. Auditoria inicial do projeto existente

Antes de alterar o código, produzir um relatório de auditoria contendo:

### 17.1 Inventário

- Linguagens, frameworks, versões e ferramentas de build.
- Estrutura de diretórios e módulos.
- Comandos de instalação, desenvolvimento, testes e build.
- Variáveis de ambiente necessárias, sem revelar valores secretos.
- Banco de dados, ORM, schema e migrações.
- Autenticação, sessões e autorização.
- API routes/endpoints e integrações externas.
- Telas existentes e ligações reais com o backend.
- Testes, cobertura útil e falhas conhecidas.
- Estado dos protótipos e componentes reutilizáveis.

### 17.2 Matriz de reaproveitamento

| Componente existente | Estado | Evidência | Decisão | Próxima tarefa |
|---|---|---|---|---|
| [Preencher após inspeção] | Completo / parcial / quebrado / ausente | Arquivo, teste ou execução | Manter / corrigir / refatorar / construir / remover | Issue vinculada |

### 17.3 Verificações técnicas mínimas

- Instalar dependências de acordo com o lockfile.
- Rodar lint, typecheck, testes e build, conforme scripts disponíveis.
- Subir o sistema e testar as rotas principais.
- Inspecionar schema e migrações sem alterar dados de produção.
- Localizar segredos acidentalmente versionados.
- Identificar operações sem validação de tenant ou permissão.
- Revisar uploads e acesso a documentos.
- Verificar estados incompletos, mocks e dados de demonstração.
- Avaliar dependências desatualizadas e vulnerabilidades conhecidas.
- Não executar migrações destrutivas ou limpeza de dados sem backup e plano aprovado.

### 17.4 Saída da auditoria

O documento de auditoria deve terminar com:

1. O que funciona.
2. O que funciona parcialmente.
3. O que está quebrado.
4. O que existe apenas como protótipo.
5. O que falta construir.
6. Riscos críticos que impedem avançar.
7. Arquivos/componentes que podem ser reaproveitados.
8. Primeiro lote de tarefas com critério de aceite.
9. Comandos reproduzíveis para executar e verificar o projeto.
10. Decisões técnicas justificadas com base em evidências, não suposições.

---

## 18. Como trabalhar com o Codex

### 18.1 Regra de trabalho

O Codex deve ser usado em tarefas pequenas e revisáveis. Não solicitar “implemente o sistema inteiro” em uma única etapa. Cada lote deve ter objetivo, arquivos ou domínio esperado, testes, critério de aceite e resumo do que foi alterado.

### 18.2 Prompt inicial para auditoria

Copiar e enviar ao Codex dentro do repositório do Câmara Digital:

```text
Leia integralmente o arquivo PLANEJAMENTO_CAMARA_DIGITAL.md antes de executar qualquer alteração.

Nesta primeira tarefa, NÃO implemente novos módulos e NÃO faça refatorações amplas. Quero uma auditoria baseada em evidências do repositório existente.

Objetivos:
1. Descobrir a stack real, versões, scripts e como executar o projeto localmente.
2. Mapear páginas, módulos, APIs, autenticação, autorização, banco de dados, migrações, integrações, uploads, testes e configuração.
3. Separar funcionalidades reais de protótipos, mocks e telas sem backend.
4. Executar as verificações seguras disponíveis: lint, typecheck, testes e build. Relatar exatamente os comandos e resultados; não inventar execuções.
5. Identificar riscos de segurança, especialmente autenticação, autorização, isolamento de tenants, acesso a arquivos, segredos e logs.
6. Preparar a matriz manter/corrigir/refatorar/construir/remover.
7. Comparar o estado atual com as 12 áreas e com os requisitos transversais deste documento.
8. Criar ou atualizar AUDITORIA_TECNICA.md com resultados, evidências, arquivos relevantes, lacunas e limitações.
9. Propor as próximas 5 a 10 tarefas menores, ordenadas por dependência e risco, com critérios de aceite e testes.

Restrições:
- Não afirmar que algo funciona sem executar ou inspecionar evidência suficiente.
- Não trocar a stack apenas por preferência.
- Não criar tabelas ou migrações destrutivas nesta etapa.
- Não expor valores de segredos em relatórios.
- Não remover funcionalidades existentes sem registrar o motivo.
- Se alguma verificação não puder ser executada, explicar exatamente o bloqueio.
- Ao final, entregar um resumo objetivo do que foi encontrado, comandos executados, arquivos analisados e próximos passos recomendados.
```

### 18.3 Modelo de prompt para cada implementação

```text
Leia PLANEJAMENTO_CAMARA_DIGITAL.md e AUDITORIA_TECNICA.md.

Implemente somente a tarefa [ID e título]. Antes de codificar, confirme dependências e arquivos afetados. Preserve o comportamento já existente, exceto quando a tarefa exige mudança explícita.

Critérios de aceite:
- [critério funcional 1]
- [critério de permissão/tenant]
- [tratamento de erro]
- [teste esperado]

Requisitos de qualidade:
- Validar entradas no servidor.
- Reutilizar padrões existentes quando forem seguros e adequados.
- Criar/atualizar testes.
- Não enfraquecer autenticação ou isolamento multi-tenant.
- Não inventar conformidade legal nem integração não testada.
- Documentar migrações, variáveis novas e configuração.

Ao terminar, informe arquivos alterados, comportamento implementado, testes realmente executados, resultado e limitações restantes. Não prossiga para outra tarefa sem fechar esta etapa.
```

### 18.4 Arquivos de documentação recomendados no repositório

- `PLANEJAMENTO_CAMARA_DIGITAL.md` — visão e plano mestre do produto.
- `AUDITORIA_TECNICA.md` — estado real do código e evidências.
- `ARQUITETURA.md` — componentes, limites de módulo e decisões técnicas.
- `MODELO_DE_DADOS.md` — entidades, relações e regras de integridade.
- `SEGURANCA_E_LGPD.md` — política técnica de acesso, dados, retenção e incidentes.
- `INTEGRACOES.md` — sistemas externos, versões, contratos e tratamento de falhas.
- `ROADMAP.md` — fases, épicos, dependências e estado das tarefas.
- `CHANGELOG.md` — mudanças por versão.
- `RUNBOOK.md` — implantação, monitoramento, backup e recuperação.

Esses arquivos podem ser criados em etapas. Evitar duplicar conteúdo; o planejamento mestre deve apontar para documentos técnicos especializados.

---

## 19. Referências oficiais e técnicas

As referências a seguir são pontos de partida para especificação e validação. **Antes de implementar integrações ou declarar conformidade, confirmar os manuais, leiautes, leis e versões vigentes na data de implantação.**

### Processo legislativo

- [SAPL — Sistema de Apoio ao Processo Legislativo, Interlegis/Senado Federal](https://www12.senado.leg.br/interlegis/produtos/sapl)
- [Produtos e serviços Interlegis](https://www12.senado.leg.br/interlegis/produtos/)

### Contratações públicas

- [Lei nº 14.133/2021 — Lei de Licitações e Contratos Administrativos](https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2021/lei/l14133.htm)
- [Portal Nacional de Contratações Públicas (PNCP)](https://www.gov.br/pncp/pt-br)
- [Portal de Compras do Governo Federal — documentação e orientações](https://www.gov.br/compras/pt-br)

### Acesso à informação, privacidade e gestão fiscal

- [Lei nº 12.527/2011 — Lei de Acesso à Informação](https://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm)
- [Lei nº 13.709/2018 — Lei Geral de Proteção de Dados Pessoais](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm)
- [Lei Complementar nº 101/2000 — Lei de Responsabilidade Fiscal](https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp101.htm)
- [Lei nº 4.320/1964 — Normas Gerais de Direito Financeiro](https://www.planalto.gov.br/ccivil_03/leis/l4320.htm)

### eSocial e prestação de contas em Mato Grosso do Sul

- [Documentação Técnica do eSocial](https://www.gov.br/esocial/pt-br/documentacao-tecnica)
- [Portal do Jurisdicionado — TCE-MS, manuais e-Sfinge](https://tce.ms.gov.br/portaljurisdicionado/conteudos/lista/4/22)
- [Manual e-Sfinge — versão 2.0, exercício 2026 (PDF)](https://www.tce.ms.gov.br/portaljurisdicionado/files/conteudos/arquivo/1052/e153e977b513820b55c02680fea30a033ea14e51d53f245a2b6231d112730000.pdf)

### Fontes adicionais de transparência e normas correlatas

- [Constituição Federal — art. 5º, XXXIII, e art. 37](https://www.planalto.gov.br/ccivil_03/constituicao/constituicaocompilado.htm)
- [Lei nº 13.460/2017 — direitos dos usuários dos serviços públicos e ouvidoria](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2017/lei/l13460.htm)
- [Lei Complementar nº 131/2009 — transparência da gestão fiscal](https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp131.htm)
- [Lei nº 13.146/2015 — Lei Brasileira de Inclusão, art. 63 sobre acessibilidade em sites](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2015/lei/l13146.htm)
- [Lei nº 15.263/2025 — Política Nacional de Linguagem Simples](https://www.planalto.gov.br/ccivil_03/_ato2023-2026/2025/lei/l15263.htm)
- [Programa Nacional de Transparência Pública (PNTP) — metodologia 2026](https://pntp.atricon.org.br/o-programa/)
- [Cartilha PNTP 2026](https://radardatransparencia.atricon.org.br/pdf/Cartilha-PNTP-2026.pdf)
- [Manual de Integração do PNCP — versão publicada como 2.6 em 31/08/2026](https://pncp.gov.br/manual/pt-br/latest/singlehtml/index.html)
- [Manual e-Sfinge do TCE-MS — versão 2.0, exercício 2026](https://www.tce.ms.gov.br/portaljurisdicionado/files/conteudos/arquivo/1052/e153e977b513820b55c02680fea30a033ea14e51d53f245a2b6231d112730000.pdf)

### Uso dessas referências

- As referências orientam a descoberta dos requisitos, mas não substituem parecer jurídico, validação contábil, análise de segurança ou consulta ao órgão responsável.
- Regras que mudam por exercício, versão de leiaute, regulamento municipal ou Regimento Interno devem ser registradas com fonte, data de consulta e responsável pela validação.
- Registrar a versão do manual utilizada em cada integração e manter um processo de atualização.

---

## 20. Especificação detalhada de RH e obrigações digitais

> **Importante:** esta seção é uma especificação de produto e engenharia, não um parecer jurídico ou contábil. A matriz de obrigações deve ser confirmada para cada Câmara, considerando o regime funcional, a forma de contratação, a existência de RPPS, os responsáveis pela folha, a legislação municipal e os manuais vigentes na data da entrega.

### 20.1 O RH não pode ser modelado como uma folha única para todos

O cadastro funcional deve distinguir, no mínimo:

- Servidor estatutário efetivo.
- Servidor exclusivamente comissionado.
- Empregado público celetista, se houver.
- Agente político e vereador, conforme o enquadramento jurídico e previdenciário aplicável.
- Estagiário, temporário, cedido, requisitado ou outro vínculo efetivamente usado pela Câmara.
- Aposentado/pensionista somente se a Câmara tiver responsabilidade operacional direta por esses benefícios.

Para cada vínculo, registrar o regime jurídico, regime previdenciário (RGPS/RPPS/outro aplicável), categoria eSocial, matrícula, cargo/função, lotação, datas de vigência, rubricas pertinentes e fonte normativa. **Não presumir que toda Câmara tem RPPS próprio nem que todos os vínculos são celetistas.** A classificação precisa ser validada pelo RH/contabilidade da Câmara antes de liberar a transmissão.

### 20.2 Integrações prioritárias do RH

| Integração/serviço | Para que serve | Estratégia no Câmara Digital | Obrigatoriedade/observação |
|---|---|---|---|
| eSocial — eventos de empregador e tabelas | Identificação do órgão, estabelecimentos/unidades, rubricas e lotações tributárias | Adaptador que valida leiaute vigente, prepara XML, transmite pelo mecanismo oficial permitido, acompanha recibos e trata rejeições | Obrigação varia conforme vínculo e evento. Usar documentação técnica vigente do eSocial |
| eSocial — vínculos e eventos não periódicos | Admissão/ingresso, alterações contratuais/funcionais, afastamentos e desligamentos, conforme o caso | Gerar evento a partir de processo de RH aprovado, validar dependências e guardar protocolo/recibo | Eventos são acionados pelo fato ocorrido; não são todos mensais |
| eSocial — remuneração, pagamentos e fechamento | Informações periódicas da folha e dos pagamentos | Integrar com a folha, validar totais, controlar sequência, fechamento/reabertura e retificação | Em geral, S-1200 para remunerações RGPS e S-1202 para RPPS, conforme elegibilidade; S-1210 registra pagamentos e S-1299 fecha eventos periódicos |
| Receita Federal — DCTFWeb | Consolidar/declarar débitos com base em eSocial, EFD-Reinf e outros insumos aplicáveis | Preparar dados e relatórios de conciliação; priorizar exportação/integrador oficial validado em vez de simular acesso por navegador | Periodicidade normalmente mensal; o prazo geral atual informado pela Receita é o último dia útil do mês seguinte aos fatos geradores. Confirmar regras e exceções vigentes |
| EFD-Reinf | Pagamentos e retenções não abrangidos pelo eSocial, conforme natureza do pagamento | Compartilhar dados com Financeiro/Compras; gerar eventos segundo o beneficiário e a natureza da retenção; conciliar com a contabilidade | R-4010 pode se aplicar a pagamento a pessoa física sem relação de trabalho; R-4020, entre outros, a pagamento a pessoa jurídica com retenção. Não duplicar pagamentos de trabalho que pertencem ao eSocial |
| FGTS Digital | Apuração/recolhimento de FGTS, quando houver trabalhadores abrangidos | Consumir dados de folha/eSocial, conciliar guias e estados; parametrizar conforme regime do vínculo | Não tratar como obrigação universal de todos os vínculos estatutários; confirmar incidência para cada categoria |
| SST no eSocial | CAT, monitoramento de saúde ocupacional e exposição a agentes nocivos, conforme aplicabilidade | Integrar ou trocar dados com prestador de saúde ocupacional; segregar documentos médicos e limitar acesso | Eventos S-2210, S-2220 e S-2240 quando aplicáveis; requisitos dependem da situação laboral e das regras vigentes |
| TCE-MS — e-Sfinge e outros canais de controle | Prestação de contas e informações exigidas do jurisdicionado | Criar adaptador separado, com exportação segundo o manual/leiaute, validação prévia, recibos e painel de pendências | Para MS, tomar o manual vigente do exercício como fonte de verdade. Prazos, estruturas e remessas específicas devem ser extraídos da versão atual, não codificados por suposição |
| Certificado digital/procuração | Autenticar transmissões e representantes autorizados | Cofre de segredos/certificados, alertas de vencimento, controle de autorização e rotação segura | Nunca armazenar senha/certificado em código, repositório, logs ou tabela acessível a usuários comuns |
| Sistema de folha/contabilidade já contratado | Evitar recálculo indevido e duplicidade de escrituração | Preferir integração documentada por API, arquivo padronizado ou troca segura com validação e reconciliação | A interface disponível depende do fornecedor; exigir documentação e ambiente de homologação antes de prometer integração automática |

### 20.3 Eventos eSocial que o produto deve contemplar no desenho

A lista abaixo orienta a modelagem; não significa que todos os eventos serão usados em cada Câmara:

- **Cadastro e tabelas:** S-1000 (empregador/órgão público), S-1005 (estabelecimentos/unidades), S-1010 (rubricas), S-1020 (lotações tributárias) e outros eventos de tabela necessários ao enquadramento real.
- **Ingresso/vínculo:** S-2200 e/ou S-2190 quando cabível; S-2300 para categorias sem vínculo empregatício que se enquadrem no leiaute; outros eventos conforme categoria e regime.
- **Alterações e ocorrências:** S-2205/S-2206, S-2230, S-2299/S-2399 e demais eventos compatíveis com o vínculo e a ocorrência.
- **Remuneração e pagamento:** S-1200 para remuneração vinculada ao RGPS, S-1202 para remuneração vinculada ao RPPS, S-1210 para pagamentos e eventos de fechamento/reabertura, conforme a situação.
- **Saúde e segurança:** S-2210, S-2220 e S-2240 quando forem devidos.
- **Benefícios de entes públicos:** avaliar eventos próprios de benefício somente se a Câmara for efetivamente responsável por cadastrar ou informar esse tipo de benefício.

O sistema deve usar uma matriz mantida por versão: evento, condição de aplicabilidade, dados obrigatórios, dependências, prazo, responsável, esquema XSD/versão, resultado, recibo, correção/retificação e registro de validação. Os leiautes mudam; não fixar regras eternamente no código sem versionamento.

### 20.4 Rotina mensal sugerida para RH/folha

A aplicação deve transformar as obrigações em uma rotina de fechamento com etapas claras, sem assumir que todas têm o mesmo prazo:

1. Conferir admissões, desligamentos, alterações, férias, licenças e ocorrências do período.
2. Validar dados funcionais, matrícula, lotação, regime, tabelas e rubricas.
3. Importar ou calcular a folha pela solução responsável e reconciliar totais com o cadastro funcional.
4. Gerar e transmitir eventos de remuneração e pagamentos pertinentes; registrar recibos e rejeições.
5. Resolver erros impeditivos, reapresentar eventos quando necessário e registrar retificações.
6. Fechar os eventos periódicos e verificar a totalização.
7. Conciliar os resultados com DCTFWeb, FGTS Digital quando aplicável, retenções e sistemas financeiros/contábeis.
8. Produzir relatórios para o responsável legal/contábil revisar e executar as declarações ou pagamentos que dependam de ação formal do órgão.
9. Gerar o pacote de prestação de contas exigido pelo TCE-MS e por outros órgãos competentes, conforme o calendário e o leiaute vigentes.
10. Encerrar a competência somente após conferência dos estados, evidências e pendências; permitir reabertura controlada quando legalmente necessária.

**Prazos:** a documentação oficial consultada informa, para o fechamento S-1299, prazo geral até o dia 15 do mês subsequente, ressalvadas regras especiais; a Receita Federal informa, para a DCTFWeb mensal, o último dia útil do mês seguinte. Essas datas não substituem o calendário completo de cada obrigação. O sistema deve manter calendário configurável/versionado e consultar as regras atuais antes de cada exercício.

### 20.5 Painel de conformidade de RH

O painel deve mostrar, no mínimo:

- Obrigação, período e fundamento/manual de referência.
- Responsável principal e substituto.
- Prazo interno e prazo oficial configurado.
- Estado: não iniciado, em preparação, validado, enviado, aceito, rejeitado, retificado ou dispensado com justificativa.
- Erro retornado, evento relacionado, ação corretiva e prazo para solução.
- Recibo/protocolo, data/hora, usuário/transmissor e arquivo de retorno.
- Conciliação dos totais de folha, pagamentos, retenções e declarações derivadas.
- Alertas de alteração de leiaute, certificado próximo do vencimento e pendências antes do fechamento.

O dashboard serve para gestão e evidência; não deve apresentar um selo genérico “100% legal” sem matriz de obrigações validada e resultados verificáveis.

---

## 21. Assinatura eletrônica, sessões e biometria

### 21.1 Não criar uma única assinatura para todos os documentos

Implementar um **serviço central de assinatura eletrônica**, chamado pelos diferentes módulos, com uma política por tipo de documento. O nível exigido deverá ser configurado após análise jurídica e conforme legislação, regimento, atos normativos internos e o nível de confiança necessário.

Níveis a suportar no desenho:

- **Assinatura simples:** para operações de menor impacto em que o órgão aceite esse nível e a norma aplicável permita.
- **Assinatura avançada:** com evidências de identidade, vínculo do signatário, controle do ato e detecção de alteração, quando admitida e formalmente aceita pela Câmara.
- **Assinatura qualificada ICP-Brasil:** por certificado digital, para atos em que seja exigida ou escolhida por segurança jurídica. A Lei nº 14.063/2020 prevê hipóteses de uso obrigatório de assinatura qualificada, incluindo certos atos assinados por titulares de Poder/órgão; a aplicação exata à autoridade e ao documento deve ser validada juridicamente.

**Não desenvolver criptografia ou uma infraestrutura de certificados própria.** Integrar uma solução tecnicamente validada e/ou usar padrões compatíveis com a ICP-Brasil. Priorizar documentos PDF com assinatura e validação compatíveis com o serviço VALIDAR/ITI, quando aplicável, e preservar o arquivo original assinado.

### 21.2 Fluxo de assinatura

1. Documento é criado a partir do processo oficial e recebe identificador e versão.
2. Revisores autorizados conferem o conteúdo e os anexos.
3. O sistema seleciona o nível e a sequência de assinaturas pela política do documento.
4. Cada signatário autentica-se pelo método habilitado e manifesta a vontade de assinar explicitamente.
5. O serviço registra identidade, papel institucional, método, data/hora, versão/hash do arquivo, certificado/validação e resultado técnico disponível.
6. O documento é selado/assinado no padrão apropriado; edição posterior invalida a versão assinada e exige nova versão/assinaturas.
7. O processo armazena recibos e trilha de auditoria e, quando público, segue ao fluxo de publicação.
8. A página de verificação permite consultar documento, situação da assinatura e evidências permitidas sem expor dados internos ou pessoais indevidos.

Requisitos: múltiplos signatários; ordem de assinatura; recusa/cancelamento; revogação quando aplicável; expiração de convite; reenvio; auditoria; validação do certificado; preservação de evidências; acessibilidade; política de retenção; fluxo de correção sem apagar versão anterior.

### 21.3 Como aplicar a assinatura às rotinas legislativas

- **Proposições e emendas:** assinar a versão que será protocolada; registrar autores/coautores e a manifestação de cada um conforme regra local. Não confundir autenticação de acesso com assinatura do texto.
- **Pareceres:** vincular assinatura ao relator/comissão e à versão exata do parecer.
- **Atas:** capturar presença, ocorrências, versão aprovada e os signatários requeridos pelo Regimento Interno ou ato aplicável.
- **Votos:** registrar eleitor autenticado, item deliberado, modalidade, escolha, horário, resultado e correções formalmente permitidas. Não permitir que a biometria, isoladamente, determine ou altere o voto.
- **Leis, resoluções, decretos legislativos e atos da Mesa:** manter a trilha entre texto aprovado, sanção/promulgação quando pertinente, assinatura pela autoridade competente e publicação oficial. A necessidade de assinatura qualificada deve ser avaliada conforme o ato e a autoridade.
- **Atos administrativos e RH:** reutilizar o mesmo serviço, mas com política própria por tipo de documento e alçada.

A configuração precisa refletir a Lei Orgânica, o Regimento Interno, normas municipais de processo eletrônico e política de assinatura aprovada pela Câmara. Não presumir que todos os vereadores precisam assinar todos os documentos ou que todas as atas seguem uma única regra nacional.

### 21.4 Biometria facial: recurso opcional, não assinatura jurídica

Reconhecimento facial e prova de vida podem ser avaliados como **fator auxiliar de autenticação ou confirmação de presença**, mas não devem ser tratados automaticamente como assinatura eletrônica, certificado digital ou prova suficiente de um voto.

Como dados biométricos vinculados a uma pessoa são dados pessoais sensíveis segundo a LGPD, a funcionalidade só deve avançar após análise de finalidade, base legal, necessidade, proporcionalidade, riscos, transparência e medidas de segurança.

Requisitos mínimos para um eventual piloto:

- Fazer avaliação de impacto à proteção de dados e análise jurídica antes de coletar biometria real.
- Documentar finalidade específica; não usar os dados para finalidade secundária incompatível.
- Verificar se há alternativa menos invasiva capaz de atingir a finalidade (certificado digital, chave de segurança, MFA, confirmação pelo dispositivo institucional ou registro presencial).
- Não tornar a biometria obrigatória por padrão; oferecer alternativa acessível e procedimento de contingência.
- Evitar armazenar imagens faciais brutas ou modelos biométricos no banco principal; se tecnicamente inevitável, aplicar segregação reforçada, criptografia, acesso extremamente limitado, retenção mínima e exclusão segura.
- Exigir prova de vida e mecanismos contra foto/vídeo/replay, avaliar falsos positivos/negativos, viés, iluminação, acessibilidade e condições de câmera.
- Nunca registrar, divulgar ou inferir emoção, opinião política, etnia ou outros atributos a partir do rosto.
- Proibir uso da biometria para alterar voto, tomar decisão legislativa ou bloquear participação sem revisão humana e alternativa de autenticação.
- Manter logs da decisão de autenticação, sem gravar templates ou imagens nos logs.
- Contratar fornecedor apenas com garantias claras sobre suboperadores, localização, segurança, incidentes, descarte, auditoria e proibição de reutilização para treino.

**Decisão recomendada:** não incluir captura facial obrigatória no pacote inicial. Primeiro lançar assinatura com níveis adequados, autenticação multifator e trilha de auditoria; avaliar biometria somente em caso de uso definido e aprovado pela Câmara.

---

## 22. Portal público, solicitações e publicações oficiais

### 22.1 O portal do cidadão deve fazer parte do pacote inicial

O pacote inicial precisa permitir que a população consulte informações e envie demandas sem pagar por um módulo premium para exercer direitos básicos de informação. Incluir:

- Portal público responsivo, acessível e com busca.
- Consulta de vereadores, mandatos, comissões, calendário, pautas, sessões e matérias legislativas.
- Acesso às proposições, emendas, pareceres publicáveis, votações, atas e normas já publicadas.
- Página de transparência para os dados que a Câmara deve divulgar, com fonte, última atualização e exportação quando exigível.
- Formulário de contato/protocolo, confirmação de recebimento e número rastreável.
- SIC/pedidos LAI com controle de prazo, distribuição, decisão, resposta e recurso conforme a legislação e a regulamentação aplicáveis.
- Ouvidoria para manifestações, com classificação, encaminhamento, sigilo quando cabível e resposta.
- Acompanhamento pelo cidadão sem expor protocolos ou informações de terceiros.
- Canal de acessibilidade, privacidade e identificação dos responsáveis pelo portal.

A LAI exige que órgãos e entidades viabilizem a apresentação de pedidos por meio de seus sítios oficiais e prevê prazo geral de até 20 dias para resposta quando o acesso imediato não for possível, prorrogável por mais 10 dias mediante justificativa. A aplicação e regulamentação municipal precisam ser configuradas e validadas.

A plataforma Fala.BR pode ser avaliada como alternativa/integração: a adesão é voluntária e está disponível também a órgãos do Legislativo municipal. Não assumir que seja obrigatória nem que exista API de integração sem documentação e adesão confirmadas.

### 22.2 Fluxo para proposições, atos, sessões e publicações

O produto deve distinguir claramente quatro estados documentais:

1. **Documento protocolado/em tramitação:** está disponível conforme regras de publicidade e eventuais restrições legítimas.
2. **Matéria deliberada/aprovada:** há registro do resultado da sessão e da versão aprovada; aprovação não significa necessariamente publicação final como norma.
3. **Ato concluído para publicação:** texto segue para a etapa prevista — sanção, promulgação, assinatura, revisão formal ou outro procedimento, conforme o tipo do ato e a legislação local.
4. **Publicação oficial comprovada:** guardar edição/URL/identificador do diário oficial, data, arquivo publicado, versão correspondente e evidência da publicação.

Para moções, requerimentos, indicações e projetos, a visibilidade e os passos finais variam conforme a natureza da matéria e o Regimento Interno. O sistema deve publicar automaticamente somente depois da regra de autorização correta, nunca confundir envio para publicação com publicação efetiva.

### 22.3 Integrações de publicação

- Criar um módulo de publicação com filas, revisão, metadados, classificação de acesso, data prevista, responsável e evidência.
- Suportar portal próprio, link para Diário Oficial, upload de comprovante/arquivo publicado e, futuramente, APIs ou integrações confirmadas com o veículo oficial utilizado pela Câmara.
- Manter a versão publicada imutável e corrigir por novo ato/versão, conforme o procedimento oficial.
- Oferecer página pública de validação com identificador, metadados, hash do arquivo, assinatura validável e link para a publicação oficial quando disponível.
- Registrar erros de envio e impedir status “publicado” sem evidência verificável ou confirmação explicitamente registrada por usuário autorizado.
- Integrar gravação/transmissão da sessão quando houver fornecedor e condições técnicas, conectando vídeo à pauta, aos tempos de fala, à votação e à ata; toda transcrição automática deve ser marcada como rascunho até revisão.

### 22.4 Regras de privacidade no portal

- Publicar apenas os dados necessários e legalmente divulgáveis.
- Separar transparência ativa de pedidos LAI, ouvidoria e documentos com restrição de acesso.
- Aplicar tratamento diferenciado a denúncias, dados de saúde, documentos pessoais e informações de menores.
- Não indexar arquivos internos, rascunhos, anexos sigilosos ou documentos não liberados para publicação.
- Criar checklist de publicação por categoria e registrar quem autorizou a divulgação.

---

## 23. Pacotes comerciais e limites entre módulos

O modelo comercial deve combinar uma base institucional obrigatória para o funcionamento e módulos de gestão avançada. A Câmara pode contratar um pacote completo ou ativar módulos adicionais, mas **não deve ser necessário pagar um extra apenas para publicar as informações públicas exigidas ou permitir pedidos de acesso à informação**.

### 23.1 Pacote Inicial — Legislativo e Cidadão

Este é o produto de entrada e precisa entregar valor de ponta a ponta, não apenas cadastro de proposições:

- Cadastro institucional, legislaturas, vereadores, Mesa Diretora, gabinetes e comissões.
- Usuários, papéis, permissões, configurações da Câmara e trilha de auditoria.
- Proposições, emendas, requerimentos, moções e fluxos de tramitação configuráveis.
- Pautas, sessões, presença, quórum, votação, ata e histórico de deliberação, conforme Regimento validado.
- Consulta de leis/normas e portal legislativo público.
- Portal do cidadão com protocolo, acompanhamento, SIC/LAI e ouvidoria, com regras de sigilo e prazo.
- Publicação de matérias e atos, workflow de revisão e evidência da publicação oficial.
- Assinatura eletrônica básica integrada ao fluxo, com política por tipo de documento; assinatura qualificada via provedor/certificado e seus custos técnicos claramente informados.
- Portal responsivo/PWA, acessibilidade, busca pública e notificações básicas.
- IA inicial: busca assistida em documentos publicáveis, resumos de proposições/sessões e ajuda para localizar matéria, sempre citando os documentos-fonte e marcando conteúdo gerado como assistivo.
- Segurança, auditoria, backups, controle de tenant e logs como itens transversais, nunca como adicionais pagos.

### 23.2 Módulo Administração e Documentos

- Protocolo interno, processos administrativos, memorandos e workflows.
- Gestão de documentos institucionais, modelos e assinaturas por política.
- Agenda institucional, tarefas, reuniões, reservas e solicitações internas.
- Gestão de setores, centro de responsabilidade e indicadores administrativos.

### 23.3 Módulo Compras, Contratos, Estoque e Patrimônio

- Planejamento de compras, demandas, fornecedores e documentos preparatórios.
- Processos de contratação, contratos, aditivos, fiscalização e alertas.
- Integração com PNCP quando a Câmara contratar/usar esse fluxo e concluir credenciamento e testes oficiais.
- Almoxarifado, inventário, bens, movimentações e manutenção.
- IA para classificação de itens, alertas de reposição, busca contratual e identificação de vencimentos, sujeita a revisão humana.

### 23.4 Módulo RH e Conformidade

- Cadastro funcional, cargos, vínculos, lotações, atos, férias, licenças, afastamentos e portal do servidor.
- Integração com folha de terceiros ou motor de folha próprio somente após especificação e testes contábeis/jurídicos.
- Conectores eSocial, SST quando aplicável, DCTFWeb/EFD-Reinf/FGTS Digital e TCE-MS conforme obrigação, regime e documentação técnica disponível.
- Painel de competência, prazos, rejeições, recibos, certificados e conciliações.
- Controle de acesso reforçado a dados pessoais, funcionais e de saúde.

### 23.5 Módulo Financeiro e Controle

- Acompanhamento de orçamento, despesas, pagamentos, retenções, diárias, prestação de contas e conciliação.
- Integração com sistema contábil oficial e remessas do órgão de controle segundo formato/versão confirmados.
- Controle interno, matriz de riscos, plano de ação e evidências.
- Painel de conformidade e gestão de pendências.

### 23.6 Módulo IA Avançada

- Pesquisa semântica sobre documentos autorizados, com citações e links de origem.
- Comparação de versões de projetos, normas e contratos.
- Transcrição e resumo de sessão com revisão antes de publicação.
- Minutas assistidas de parecer, resposta, despacho e relatório, sempre com aprovação humana.
- Triagem de solicitações, sugestão de encaminhamento e detecção de prazos.
- Indicadores de estoque/contratos e detecção explicável de possíveis inconsistências.
- Administração de fontes, limites, custos, avaliação e observabilidade do modelo.

### 23.7 Como precificar sem fragmentar o produto

- Definir mensalidade base por Câmara/tenant e módulos contratados.
- Definir limites claros para armazenamento, volume de documentos, processamento de IA, assinaturas qualificadas e integrações de terceiros.
- Informar custos variáveis de certificados/provedores e processamento de IA sem bloquear direitos de acesso público.
- Permitir expansão e redução de módulos com exportação dos dados e sem apagar histórico legal.
- Oferecer implantação, migração, treinamento e suporte em escopo contratual separado, quando necessário.
- Manter funções de segurança, auditoria, retenção e publicação legalmente obrigatória dentro dos pacotes aplicáveis.
- Não prometer economia, conformidade ou integração automática sem provar capacidade operacional.

### 23.8 Prioridade de desenvolvimento

**P0 — Base e pacote inicial:** multi-tenant, usuários/permissões, auditoria, cadastro institucional, processo legislativo, pauta/sessão/votação/ata, publicação, assinatura integrada, portal público, SIC/ouvidoria, busca e IA legislativa assistiva.

**P1 — Administração conectada:** protocolo interno, documentos, agenda, tarefas, comunicação e workflows.

**P2 — Operação administrativa:** compras/contratos/estoque/patrimônio, inicialmente sem prometer integração oficial que não esteja homologada.

**P3 — RH e compliance:** especificação por regime, integração de folha e eventos eSocial, painel de obrigações, conciliação, conectores fiscais e TCE-MS.

**P4 — Financeiro avançado e IA:** integração contábil, dados de controle interno, análise de risco, IA expandida e aplicativo nativo se houver justificativa.

---

## 24. Critérios de aceite específicos para essas integrações

### 24.1 eSocial e sistemas fiscais

- [ ] A Câmara e o vínculo estão classificados por regime/categoria, com validação por responsável autorizado.
- [ ] Cada evento utiliza leiaute e versão identificados e vigentes para o ambiente selecionado.
- [ ] O sistema guarda XML/dados enviados conforme política de retenção, protocolo, recibo e retorno, sem registrar segredos.
- [ ] Rejeições aparecem com código/mensagem, vínculo à origem e instrução de correção; reenvio não duplica registros.
- [ ] Fechamento/reabertura e retificação são ações explícitas, autorizadas e auditadas.
- [ ] Dados da folha, remuneração, pagamentos e retenções são conciliáveis entre origem e declarações derivadas.
- [ ] O calendário de obrigações registra a fonte, a versão, a data de validação e o responsável por confirmar prazos.
- [ ] A integração de produção só é ativada após homologação, procuração/certificado e autorização do cliente.

### 24.2 Assinatura eletrônica

- [ ] A política identifica, por tipo de documento, o nível de assinatura permitido/exigido e o responsável pela validação jurídica.
- [ ] O documento assinado pode ser validado externamente quando o padrão e o provedor permitirem.
- [ ] O arquivo assinado não é sobrescrito; alterações criam nova versão e nova trilha.
- [ ] A confirmação de assinatura fica vinculada à versão/hash exato.
- [ ] Há fluxo de revogação/cancelamento e tratamento de certificado expirado ou comprometido.
- [ ] A assinatura é distinguida de login, autenticação, reconhecimento facial e presença em sessão.

### 24.3 Biometria

- [ ] Nenhuma captura biométrica é ativada por padrão sem análise de necessidade, base legal, risco e alternativa menos invasiva.
- [ ] O uso passa por avaliação de impacto/risco e revisão jurídica e de segurança.
- [ ] Existe alternativa acessível, fluxo de contestação e procedimento manual em falha de identificação.
- [ ] O armazenamento, retenção, acesso, descarte e tratamento por fornecedores estão documentados e testados.
- [ ] A biometria nunca substitui isoladamente a assinatura exigida por lei nem decide ou altera resultado de votação.

### 24.4 Portal e publicações

- [ ] Pedido de informação gera protocolo e controle de prazo configurado conforme a norma aplicável.
- [ ] O cidadão só visualiza seus próprios expedientes restritos e dados que sejam públicos.
- [ ] A publicação oficial guarda a versão exata, autorização, data e evidência do canal oficial.
- [ ] Atos aprovados não são marcados como publicados sem evidência.
- [ ] Conteúdo da IA é sinalizado como assistivo e não substitui o texto oficial.
- [ ] Uma remoção/correção pública mantém registro do motivo, da autorização e do histórico pertinente.

### 24.5 Referências oficiais para implementação

- [Documentação técnica do eSocial — leiautes, XSD e Manual de Orientação](https://www.gov.br/esocial/pt-br/documentacao-tecnica)
- [Manual WEB Geral do eSocial](https://www.gov.br/esocial/pt-br/empresas/manual-web-geral)
- [Receita Federal — DCTFWeb](https://www.gov.br/pt-br/servicos/declarar-debitos-e-creditos-tributarios-federais)
- [Receita Federal — FAQ EFD-Reinf](https://www.gov.br/receitafederal/pt-br/acesso-a-informacao/perguntas-frequentes/sped/efd-reinf/efdr)
- [TCE-MS — manuais e-Sfinge](https://tce.ms.gov.br/portaljurisdicionado/conteudos/lista/4/22)
- [Lei nº 14.063/2020 — assinaturas eletrônicas](https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2020/lei/l14063.htm)
- [ITI — serviço VALIDAR](https://www.gov.br/pt-br/servicos/realizar-validacao-de-assinaturas-eletronicas-validar)
- [Guia do desenvolvedor do VALIDAR/ITI](https://h-validar.iti.gov.br/guia-desenvolvedor.html)
- [LGPD — Lei nº 13.709/2018](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm)
- [Lei nº 12.527/2011 — Lei de Acesso à Informação](https://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm)
- [CGU — adesão à plataforma Fala.BR](https://www.gov.br/ouvidorias/pt-br/ouvidorias/rede-de-ouvidorias/adesao-e-cadastros/adesao-fala.br)
- [PNCP — integração de plataformas](https://www.gov.br/pncp/pt-br/pncp/integre-se-ao-pncp)

## Definição de pronto para o produto

O Câmara Digital estará pronto para ser considerado uma plataforma operacional em produção apenas quando os módulos liberados tiverem sido validados em ambiente de uso real, com permissões e isolamento testados, dados conciliados, trilhas de auditoria, backups restauráveis, documentação operacional, suporte definido e critérios de publicação cumpridos.

A existência de um menu, formulário ou protótipo visual não significa que a funcionalidade esteja implementada. Cada módulo deve ter fluxo funcional, regras de negócio, persistência correta, autorização, tratamento de falhas e testes demonstráveis.

**Próxima ação recomendada:** executar a auditoria descrita na seção 17 usando o prompt da seção 18.2. Depois da auditoria, ajustar este roadmap à realidade do código e começar pelo primeiro lote de tarefas P0.

---

## 25. Matriz normativa nacional e municipal

> **Finalidade desta seção:** traduzir fontes normativas em requisitos de software que possam ser implementados e testados. Esta matriz é uma base de descoberta, não um parecer jurídico nem uma declaração de conformidade. A obrigação concreta pode depender do porte do município, do tipo de ato, do regime funcional, da legislação municipal, de decisões judiciais e de normas atualizadas dos órgãos de controle.

### 25.1 Normas nacionais de referência

| Fonte | Aplicação ao produto | Requisitos de software a derivar | Validação necessária |
|---|---|---|---|
| Constituição Federal, arts. 5º, XXXIII, e 37 | Acesso à informação e princípios da Administração Pública | Transparência, classificação de acesso, publicidade dos atos, trilha de auditoria e processos impessoais | Confirmar regras específicas do ato e legislação local |
| Lei nº 12.527/2011 (LAI) | Transparência ativa e pedidos de informação | Conteúdo público, busca, download, SIC, protocolo, prazos, resposta, negativa fundamentada, recurso e classificação de acesso | Verificar regulamentação municipal, procedimentos e exceções aplicáveis |
| LC nº 101/2000 (LRF), especialmente arts. 48 e 48-A, e LC nº 131/2009 | Transparência fiscal e divulgação de execução orçamentária/financeira | Exibir dados de receitas e despesas conforme aplicabilidade, atualização, pesquisa, histórico, exportação e proveniência | Alinhar campos e periodicidade com o sistema contábil e exigências de controle |
| Lei nº 14.133/2021 | Contratações administrativas | Processo de contratação, documentos, contratos/aditivos, prazos e integração/publicação no PNCP quando aplicável | Validar procedimentos adotados pela Câmara, hipóteses legais e versão vigente do manual PNCP |
| Lei nº 13.460/2017 | Direitos dos usuários de serviços públicos e ouvidoria | Recepção e encaminhamento de manifestações, controle de prazo, resposta, estatísticas e relatório de gestão | Validar regulamentação e estrutura da ouvidoria local |
| Lei nº 13.709/2018 (LGPD) | Tratamento de dados pessoais, inclusive dados sensíveis | Minimização, finalidade, acesso restrito, retenção, segurança, registros, gestão de titulares e incidentes | Definir papéis de controlador/operador e base legal conforme operação real |
| Lei nº 13.146/2015, art. 63 | Acessibilidade em sites governamentais | Navegação por teclado, suporte a leitor de tela, contraste, foco, estrutura semântica, textos alternativos e testes | Validar com testes automatizados e manuais; não se limitar a selo visual |
| Lei nº 15.263/2025 | Linguagem simples na comunicação com a população | Conteúdo explicativo claro, frases diretas, termos técnicos explicados, teste com usuários e acessibilidade comunicacional | Não alterar o texto oficial da lei; apresentar explicações como material auxiliar |
| Lei nº 14.063/2020 | Assinaturas eletrônicas em interações com entes públicos, nos termos da lei | Política de assinatura por tipo documental e nível de assinatura; evidência técnica e validação do arquivo | Parecer jurídico por ato, signatário, política local e solução técnica escolhida |
| Lei nº 4.320/1964 e normas de contabilidade pública aplicáveis | Orçamento, execução e demonstrações públicas | Estrutura de dados e integração com sistema contábil, relatórios conciliáveis e histórico | Regras e leiautes contábeis devem ser validados por contador e órgãos competentes |
| Lei nº 8.159/1991 e normas arquivísticas aplicáveis | Gestão de documentos públicos e arquivos | Classificação documental, temporalidade, preservação, destinação e trilha de movimentação | Elaborar política arquivística conforme órgão e regras aplicáveis; não apagar documentos por regra genérica |

### 25.2 Normas e parâmetros que variam por Câmara

O onboarding de cada cliente deve levantar e versionar, no mínimo:

- Lei Orgânica Municipal vigente e emendas relevantes.
- Regimento Interno da Câmara e suas alterações.
- Normas sobre processo eletrônico, assinatura, autenticação e uso de certificado digital.
- Tipo de Diário Oficial/veículo oficial, regras de publicação, comprovação e republicação.
- Tipos de proposição, regras de protocolo, tramitação, comissões, pauta, quórum, votação, ata, sanção/promulgação quando cabível.
- Regras de presença, votação presencial/remota/híbrida, contingência e registros oficiais, se admitidas.
- Regulamento local da LAI, SIC, ouvidoria, classificação de informação e recursos.
- Estatuto/regime de pessoal, legislação de cargos e salários, previdência, jornada, benefícios e atos de nomeação/exoneração.
- Políticas de compras, contratos, fiscalização, delegação de competência e segregação de funções.
- Regras contábeis, orçamento, prestação de contas e manuais vigentes do TCE-MS ou tribunal de contas correspondente.
- Política de privacidade, segurança da informação, retenção documental e gestão de incidentes.

### 25.3 Campos obrigatórios para cada requisito legal

Cada requisito deverá existir no registro interno de conformidade com os seguintes campos:

| Campo | Conteúdo |
|---|---|
| `requirement_id` | Identificador estável do requisito |
| Fonte normativa | Lei, artigo, regulamento, manual ou critério de avaliação |
| Jurisdição | Nacional, estadual, municipal ou regra interna |
| Aplicabilidade | Obrigatório, condicional, recomendado, não aplicável ou aguardando parecer |
| Condição de aplicabilidade | Porte, regime, tipo de ato, vínculo, fornecedor, município, exercício ou outra condição |
| Módulo e funcionalidade | Módulo/endpoint/fluxo que implementa o requisito |
| Responsável pela validação | Jurídico, RH, contabilidade, controle interno, TI, ouvidoria ou outro responsável |
| Evidência exigida | Registro, documento, recibo, tela pública, arquivo, log ou teste |
| Prazo/frequência | Evento disparador e periodicidade quando determinada pela fonte |
| Versão/fonte consultada | Link, data de consulta, versão do manual/leiaute |
| Estado | Não analisado, em análise, implementado, testado, validado, não aplicável ou pendente |
| Testes relacionados | IDs de testes automatizados/manuais e resultado |
| Histórico | Alterações normativas, decisão de aplicabilidade e justificativa |

Não usar um único booleano `compliant=true` para representar conformidade completa. Um requisito pode estar parcialmente implementado, depender de integração externa ou aguardar validação do órgão responsável.

---

## 26. Checklist de transparência alinhado ao PNTP 2026

### 26.1 Como usar o PNTP

O PNTP é conduzido pela Atricon em parceria com os Tribunais de Contas. A página oficial do programa descreve a matriz do ciclo 2026 como composta por **181 critérios em 22 dimensões**. Os aspectos avaliados incluem disponibilidade (30%), atualidade (30%), série histórica (20%), gravação de relatórios (10%) e filtro de pesquisa (10%). A matriz distingue 110 critérios obrigatórios, 59 recomendados e 12 essenciais. Isso é metodologia de avaliação do portal; não significa que todos os critérios sejam automaticamente aplicáveis a cada órgão sem análise de escopo.

A equipe deve baixar a matriz e a cartilha oficiais do ciclo aplicável, armazenar a versão consultada e marcar cada critério como aplicável, condicional ou não aplicável, com justificativa. Não copiar critérios de outro Poder para uma Câmara sem checar a dimensão correta.

### 26.2 Dimensões a considerar na matriz do produto

A página do PNTP 2026 lista as dimensões abaixo. O sistema de conformidade deve registrar aplicabilidade e evidência por dimensão; algumas podem não corresponder às competências de uma Câmara Municipal e exigem análise antes de serem incluídas no escopo do cliente.

1. Informações prioritárias.
2. Informações institucionais.
3. Receita.
4. Despesa.
5. Convênios e transferências.
6. Recursos humanos.
7. Diárias.
8. Licitações.
9. Contratos.
10. Obras.
11. Planejamento e prestação de contas.
12. Serviço de Informação ao Cidadão (SIC).
13. Acessibilidade.
14. Ouvidorias.
15. LGPD e Governo Digital.
16. Renúncias de receitas.
17. Emendas parlamentares.
18. Saúde.
19. Educação e assistência social.
20. Atividades finalísticas — Poder Legislativo.
21. Atividades finalísticas — Poder Judiciário.
22. Atividades finalísticas.

**Nota de escopo:** o sistema deve permitir a marcação de itens não aplicáveis ao órgão, com justificativa e validação responsável. Não apresentar como falha da Câmara uma dimensão cuja competência seja exclusiva de outro Poder, mas tampouco marcar como não aplicável sem justificativa.

### 26.3 Checklist funcional do portal público

#### Institucional e organização

- [ ] Estrutura organizacional, competências, atribuições, endereços, telefone, e-mail institucional, horário e formas de contato.
- [ ] Vereadores e Mesa Diretora, com mandato/vigência, comissões e informações institucionais publicáveis.
- [ ] Organograma e identificação de responsáveis/setores quando exigível.
- [ ] Carta de Serviços ao Usuário, com descrição, etapas, requisitos, canais e prazos de cada serviço aplicável.
- [ ] Política de privacidade, informações sobre tratamento de dados e canal para contato relativo à privacidade.

#### Atividade legislativa

- [ ] Consulta de leis, resoluções, decretos legislativos e demais normas da Câmara, com número, ano, data, ementa, texto e referência oficial.
- [ ] Projetos, moções, indicações, requerimentos, emendas e outras proposições, conforme os tipos de matéria locais.
- [ ] Autores/coautores, data, ementa, anexos publicáveis, situação, tramitação e histórico de movimentações.
- [ ] Comissões, composição, pautas, reuniões, pareceres publicáveis e relatorias.
- [ ] Pautas das sessões e ordens do dia, incluindo data, tipo e matérias previstas.
- [ ] Atas, listas de presença, resultados de deliberação e votações divulgáveis conforme as regras aplicáveis.
- [ ] Informações e documentos de sessões/audiências públicas, consultas e outras formas de participação, quando realizadas e exigidas pela matriz aplicável.
- [ ] Julgamento/apreciação de contas do Executivo e documentos relacionados, quando no âmbito de competência da Câmara.
- [ ] Pesquisa por número, ano, tipo, autor, assunto, situação e período.

#### Gestão fiscal e administrativa

- [ ] Receitas e despesas sob responsabilidade da Câmara, com origem e atualização a partir de fonte contábil confiável.
- [ ] Dados de despesas com identificação do processo, objeto, favorecido, datas e demais campos aplicáveis à regra vigente.
- [ ] Informações de empenho, liquidação e pagamento conforme a origem, sem confundir status gerencial com contabilização oficial.
- [ ] Diárias e viagens, com beneficiário, finalidade, período, valor e prestação de contas quando exigidos.
- [ ] Quadro de pessoal e remuneração publicável, com filtros e proteção de informações pessoais não necessárias.
- [ ] Licitações, contratações diretas, editais, resultados, contratos, aditivos e dados de fiscalização exigidos.
- [ ] Planejamento, relatórios fiscais/contábeis e prestações de contas que devam ser divulgados pelo órgão.
- [ ] Inventário e patrimônio quando houver critério legal ou de avaliação aplicável; não expor detalhes que criem risco de segurança sem necessidade.

#### Ferramentas e qualidade de dados

- [ ] Portal acessível por dispositivos móveis e navegação por teclado.
- [ ] Pesquisa que realmente filtre os registros, sem ser apenas uma caixa decorativa.
- [ ] Exportação/baixar relatórios nos formatos exigidos ou adequados; priorizar dados abertos e legíveis por máquina quando aplicável.
- [ ] Histórico de exercícios e série histórica conforme a exigência aplicável.
- [ ] Data da última atualização, fonte do dado e status de integração.
- [ ] PDFs pesquisáveis e metadados descritivos, sempre que possível.
- [ ] Links e arquivos testados; falhas e desatualizações geram tarefa para responsável.
- [ ] Não exigir login para informações que devem ser públicas; não expor dados restritos.
- [ ] Evidências do critério armazenadas para revisão interna e avaliação do Tribunal de Contas.

### 26.4 Painel de preparação para avaliação

O painel interno de transparência deverá mostrar, por critério:

- aplicabilidade e justificativa;
- estado de implementação;
- fonte de dados e responsável;
- URL ou caminho público para verificação;
- data da última atualização;
- teste de disponibilidade, pesquisa, exportação e série histórica;
- evidência anexada e data da validação humana;
- pendência, severidade, prazo interno e histórico de correção.

O painel deve se chamar **“Preparação/Checklist PNTP”** ou expressão semelhante até haver avaliação externa oficial. Não usar selo, nota ou declaração que dê a entender certificação oficial inexistente.

---

## 27. Especificação de publicação e processo legislativo

### 27.1 Catálogo de documentos e respectivos estados

Cada tipo documental deve declarar um workflow próprio ou herdar de um workflow comum parametrizado. Exemplos: projeto de lei, emenda, moção, requerimento, parecer, pauta, ata, resolução, decreto legislativo, ato da Mesa e ato administrativo.

Estados genéricos possíveis (a configuração define quais se aplicam):

`Rascunho → Em revisão → Protocolado → Em tramitação → Pautado → Deliberado → Aprovado/Rejeitado/Retirado/Arquivado → Assinaturas pendentes → Pronto para publicação → Publicado oficialmente → Retificado/Revogado/Consolidado`.

Este diagrama é uma referência de modelagem, não uma sequência legal obrigatória para todas as espécies. O workflow real deve ser validado pelo Regimento Interno e pela legislação local. Alguns atos não passam por todas as etapas; outros possuem procedimentos adicionais, como sanção, promulgação, veto, retorno ao plenário, publicação de emenda, republicação ou consolidação.

### 27.2 Registro mínimo de uma proposição

- Identificador interno estável, número/ano público e tipo.
- Autor(es), vínculo institucional e papel na autoria.
- Data/hora de apresentação e recibo/protocolo.
- Ementa, texto integral, justificativa, anexos e versão.
- Matérias relacionadas, assunto/tema e classificação publicável.
- Etapa atual, data da etapa, setor/comissão responsável, relator e prazo.
- Movimentações cronológicas, despacho, parecer, resultado e anexos públicos.
- Sessões/pautas em que foi apreciada, resultado e votos quando aplicável.
- Estado de aprovação e próximos procedimentos cabíveis.
- Identificação do ato final resultante, se houver, e relação com a norma publicada.
- URLs oficiais, data e evidência da publicação correspondente.

### 27.3 Registro mínimo de sessão

- Identificador, tipo, data/hora, local/modalidade e situação.
- Pauta/ordem do dia com ordem dos itens e documentos vinculados.
- Presenças, ausências e justificativas conforme regra local.
- Quórum calculado a partir de regras configuradas e validadas.
- Ocorrências de abertura, suspensão, encerramento e outros atos relevantes.
- Deliberação por item; modalidade e resultado da votação; registro nominal se aplicável.
- Ata e suas versões: rascunho, revisão, aprovação, assinaturas e publicação.
- Gravação, transmissão ou transcrição vinculada, quando disponível.
- Publicação da pauta/ata e evidências correspondentes.

### 27.4 Regras de integridade da deliberação

- O sistema não pode recalcular ou alterar um resultado final sem uma ação autorizada, motivo e trilha completa.
- As regras de quórum, impedimento, modalidade e contagem devem ter fonte normativa, versão, vigência e aprovador.
- Em caso de falha técnica, deve existir procedimento de contingência aprovado pela Câmara; a correção posterior precisa preservar o ocorrido e o motivo.
- Não aceitar voto duplicado para o mesmo participante/item/sessão, salvo regra formal que permita correção/retificação documentada.
- Presença digital, login ou biometria não substituem automaticamente a forma de registro oficial da sessão.
- Toda modificação administrativa de pauta, presença, votação e ata após o encerramento deve gerar evento auditável e, quando necessário, versão corrigida.

### 27.5 Publicação oficial: separar canal público de veículo oficial

O portal do Câmara Digital só deve ser tratado como **veículo oficial** se a legislação e os atos competentes da Câmara lhe atribuirem essa função. Caso a publicação oficial ocorra em Diário Oficial, diário de associação, portal de publicações oficial ou outro meio designado, o sistema deverá armazenar:

- veículo/canal oficial e base normativa que o identifica;
- identificador da edição/volume/número ou URL permanente;
- data de publicação e arquivo final correspondente;
- responsável que confirmou a publicação e evidência de origem;
- hash ou mecanismo de integridade do arquivo quando cabível;
- vínculo com a versão assinada e com o processo de origem;
- histórico de errata, retificação, republicação, revogação ou consolidação.

Um clique em “Enviar para publicação” significa **enviado**, não **publicado**. O estado “publicado oficialmente” só pode ser atribuído com confirmação do canal oficial, recibo de integração ou validação documental por usuário autorizado.

---

## 28. SIC, ouvidoria, linguagem simples e acessibilidade

### 28.1 Separar os tipos de entrada do cidadão

O portal deve apresentar opções claras e fluxos separados para:

- Pedido de acesso à informação (SIC/LAI).
- Reclamação, sugestão, elogio ou outra manifestação de ouvidoria.
- Denúncia, com tratamento de acesso restrito conforme as regras aplicáveis.
- Protocolo geral ou encaminhamento administrativo.
- Participação em consulta pública, audiência ou agenda institucional.
- Pedido de correção de dado pessoal, quando cabível e devidamente identificado.

Não direcionar automaticamente toda mensagem de cidadão para a ouvidoria. Cada categoria tem finalidade, tratamento, prazo e critérios de divulgação diferentes.

### 28.2 Prazos e controles

- **LAI:** o acesso deve ser imediato quando disponível; quando não for possível, a lei prevê resposta em até 20 dias, prorrogável por mais 10 mediante justificativa expressa, observadas as regras aplicáveis. O sistema deve registrar a data de início do prazo, o responsável, a prorrogação e a justificativa.
- **Ouvidoria:** a Lei nº 13.460/2017 prevê decisão administrativa final em até 30 dias, prorrogável uma vez por igual período mediante justificativa expressa. O sistema deve contar o prazo e registrar os atos.
- **Outros protocolos:** prazo e regra vêm de norma/processo específico; não reutilizar automaticamente o prazo da LAI ou da ouvidoria.

O calendário de prazo deve ser parametrizado com a regra validada, feriados aplicáveis e o evento que inicia a contagem. O sistema deve mostrar alerta antes do vencimento e registrar quando a contagem for suspensa ou prorrogada, desde que exista base para isso.

### 28.3 Privacidade, anonimato e publicidade

- O cidadão deve receber protocolo e um meio seguro de acompanhar a própria solicitação.
- A área pública não deve revelar identidade de manifestante, documentos internos, denúncias ou anexos restritos.
- A divulgação de respostas e relatórios deve seguir classificação de acesso e minimização de dados pessoais.
- Pedidos anônimos, identificados ou sigilosos precisam ser tratados conforme a categoria e a regulamentação; não presumir que todas as formas são admitidas para todos os serviços.
- Recusas, negativas parciais, recursos e decisões precisam manter fundamento, autoridade e prazo aplicável.
- Relatórios estatísticos devem remover ou agregar dados quando necessário para evitar identificação indevida.

### 28.4 Linguagem simples e acessibilidade como requisito funcional

A Lei nº 15.263/2025 institui a Política Nacional de Linguagem Simples na comunicação com a população pelos órgãos e entidades da Administração Pública de todos os Poderes, Estados, Distrito Federal e Municípios. O produto deverá:

- priorizar frases diretas, termos comuns e explicações de jargões;
- colocar a informação principal primeiro;
- usar listas/tabelas quando facilitarem a compreensão;
- apresentar o nome completo antes de siglas em conteúdo dirigido ao público;
- testar conteúdos e formulários com usuários representativos;
- permitir que o texto jurídico oficial permaneça intacto e seja exibido ao lado de explicação em linguagem simples;
- identificar resumos de IA como explicativos, sem atribuir valor normativo;
- verificar acessibilidade semântica, contraste, teclado, foco, leitores de tela e documentos anexados.

### 28.5 Carta de Serviços

O portal deve apoiar a publicação de Carta de Serviços ao Usuário, contendo, para cada serviço público da Câmara aplicável: serviço oferecido, público-alvo, etapas, requisitos, documentos necessários, canais de atendimento, formas de acesso, previsão de prazo e formas de acompanhamento. Deve existir responsável por revisão periódica e data de atualização.

---

## 29. Integrações oficiais: versões e operação

### 29.1 Controle de versão e monitoramento

As integrações públicas evoluem em versões próprias. A documentação consultada em **26/10/2026** indicava os seguintes pontos de referência; confirmar novamente antes de desenvolver ou publicar uma integração:

| Sistema | Referência consultada | Implicação para o projeto |
|---|---|---|
| eSocial | Portal oficial lista leiautes S-1.3 e Nota Técnica 07/2026 revisada em 20/10/2026, além de pacotes de esquemas com datas de produção distintas | Salvar versão do leiaute por transmissão; não misturar ambiente, esquema futuro e produção; acompanhar notas técnicas |
| PNCP | Manual de Integração identificado como versão 2.6, datado de 31/08/2026 | Implementar conforme endpoints e validações atuais; manter histórico de versão e testes de regressão |
| TCE-MS/e-Sfinge | Manual de versão 2.0, de 26/01/2026, exercício 2026, para integrações aplicáveis aos entes/municípios de MS | Fazer análise de campos, módulos, remessas e cronogramas exigidos no exercício; revisar no próximo exercício |
| DCTFWeb/EFD-Reinf/FGTS Digital | Manuais e regras operacionais próprios, sujeitos a atualização | Confirmar versão, prazo, enquadramento e mecanismo oficial de acesso antes da implementação |
| Assinatura eletrônica/ICP-Brasil | Lei, normas técnicas e provedor escolhidos | Testar validade do documento final e preservação de evidências com ferramentas de verificação compatíveis |

### 29.2 Arquitetura recomendada de conectores

Cada integração deve estar isolada em um adaptador de domínio que traduza os dados internos para o formato externo e trate o retorno. Não espalhar campos de um leiaute governamental por todos os módulos internos.

O conector deve oferecer:

- **Versão explícita:** API/leiaute, ambiente e data de vigência.
- **Validação prévia:** campos obrigatórios, formato, consistência e regras conhecidas.
- **Idempotência:** reenvio seguro sem duplicar a operação.
- **Fila de processamento:** estados pendente, enviado, aceito, rejeitado, retificar e reprocessar.
- **Evidências:** recibo, protocolo, resposta, horário, usuário/serviço e referência ao registro de origem.
- **Tratamento de falhas:** timeouts, limites, tentativas, fila de erros e reprocessamento autorizado.
- **Reconciliação:** comparação dos dados enviados e retornados contra o sistema de origem.
- **Observabilidade:** indicadores de sucesso, latência, rejeições e falhas persistentes.
- **Segurança:** credenciais em cofre de segredos, rotação e nunca registradas em logs.
- **Testes:** ambiente de homologação quando houver, casos positivos, rejeições, duplicidade e regressão de versão.

### 29.3 Princípio de não simular integrações

Nunca declarar “integração oficial concluída” apenas porque foi gerado um arquivo ou aberta uma página do sistema externo. Para considerar a integração pronta, deve haver documentação atual, autenticação autorizada, teste de ponta a ponta, retorno verificável e procedimento de operação definido. Quando a API ou o canal oficial não estiver acessível, entregar importação/exportação segura e documentada, identificada como alternativa, até a integração ser validada.

---

## 30. Requisitos de IA responsável para o setor público

### 30.1 Casos de uso priorizados

**Pacote inicial:**

- pesquisa em proposições, leis e documentos publicáveis com links para a fonte;
- resumo de matéria e explicação de tramitação em linguagem simples;
- perguntas frequentes baseadas no portal institucional;
- ajuda a localizar protocolos, pautas e documentos;
- classificação sugerida de solicitações recebidas, com revisão do servidor.

**Etapas posteriores:**

- transcrição e sumário de sessões, com marcação de tempo e estado de revisão;
- comparação de versões de projeto/lei e indicação de diferenças textuais;
- minuta assistida de ofício, relatório, parecer ou resposta administrativa;
- apoio à pesquisa de preço, contratos, itens de estoque e prazos;
- alertas de inconsistência em dados estruturados, com explicação e evidência para revisão humana.

### 30.2 Salvaguardas obrigatórias

- Recuperar conteúdo somente de fontes às quais o usuário/tenant tem autorização.
- Anexar fontes ou referências às respostas factuais; exibir claramente quando não houver suporte suficiente.
- Não apresentar resumo ou conteúdo gerado como lei, ata, parecer ou ato oficial.
- Não publicar automaticamente texto gerado sem revisão e autorização humana.
- Não permitir que prompts incluídos em documentos recuperados alterem permissões, políticas de segurança ou instruções de sistema (defesa contra prompt injection).
- Tratar documentos e páginas recuperadas como conteúdo não confiável.
- Evitar enviar dados pessoais/sensíveis ao provedor sem necessidade, base legal, proteção contratual e configuração apropriada.
- Não usar dados confidenciais de uma Câmara para responder a outra, treinar modelos externos ou criar índices compartilhados sem base jurídica e autorização clara.
- Registrar modelo/versão, fonte de recuperação, horário, tenant, categoria de uso e feedback, minimizando a retenção de prompts que contenham dados pessoais.
- Medir qualidade, taxa de citação correta, alucinações, latência, custo e frequência de revisão/correção.
- Manter procedimento manual quando a IA estiver indisponível ou não tiver confiança suficiente.

### 30.3 Decisões que devem permanecer humanas

A IA pode preparar minuta e evidência, mas não deve decidir autonomamente: resultado legislativo, voto, validade jurídica final, sanção, publicação oficial, negativa de acesso à informação, processo disciplinar, concessão/negação de direito de servidor, pagamento, classificação definitiva de sigilo ou responsabilidade de fornecedor.

### 30.4 Linguagem simples sem alteração do original

Quando a IA resumir projeto ou lei, mostrar pelo menos:

- o título e a identificação do documento oficial;
- a data/versão da fonte usada;
- um resumo marcado como explicativo;
- os pontos do texto que sustentam cada informação, com links;
- situação atual e data da última atualização;
- ressalva de que o texto oficial é a fonte prevalente.

A IA deve dizer que não sabe quando a fonte não dá suporte suficiente e nunca inventar número de projeto, voto, prazo ou citação normativa.

---

## 31. Configuração jurídica por Câmara e registro de obrigações

### 31.1 Perfil de conformidade por tenant

Cada Câmara deve ter um perfil de conformidade que armazene, com vigência e versionamento:

- município/UF e órgãos de controle competentes;
- Lei Orgânica, Regimento Interno e respectivas versões;
- tipos de proposição e workflows aprovados;
- calendário legislativo, sessões, regras de quórum/votação e contingência;
- canal oficial de publicação e forma de comprovar edição/URL;
- regulamentos de SIC/ouvidoria e prazos locais;
- regimes de pessoal/previdência e fornecedores responsáveis por folha;
- sistema contábil e fornecedor de compras/contratos;
- credenciais, procurações e conectores autorizados, mantidos em cofre seguro;
- matriz PNTP aplicável e critérios não aplicáveis justificados;
- responsáveis jurídicos, de RH, contabilidade, TI, controle interno e portal público.

### 31.2 Controle de vigência

Toda norma ou configuração que altere o fluxo deve conter `valid_from`, opcionalmente `valid_to`, fonte, aprovador e registro de mudança. Não editar retroativamente a regra vigente para fazer uma sessão antiga parecer que seguiu uma regra posterior. Os documentos históricos devem continuar interpretáveis conforme a versão da configuração aplicável naquele momento.

### 31.3 Revisão periódica

- Revisar normas locais antes de cada legislatura e quando houver alteração publicada.
- Revisar manual e-Sfinge e obrigações do TCE-MS por exercício.
- Monitorar versões do eSocial, PNCP, DCTFWeb, EFD-Reinf e ferramentas de assinatura.
- Manter responsáveis por receber comunicados oficiais e avaliar impacto técnico.
- Registrar se a atualização exige migração, revisão de campos, alteração de workflow ou novo teste.
- Não ativar comportamento normativo novo em produção sem aprovação e plano de migração.

---

## 32. Backlog regulatório adicional e testes de conformidade

Estes épicos complementam o backlog da seção 14; devem ser decompostos em tarefas pequenas depois da auditoria do repositório.

| ID sugerido | Entrega | Prioridade | Critério de saída |
|---|---|---|---|
| CD-REG-001 | Registro de obrigações legais e fontes versionadas | P0 | Cada requisito prioritário tem fonte, aplicabilidade, responsável, evidência e teste |
| CD-REG-002 | Checklist PNTP 2026 por tenant | P0 | Critérios relevantes são mapeados; não aplicáveis têm justificativa; pendências aparecem em painel |
| CD-REG-003 | Catálogo de dados públicos e atualização | P0 | Cada conjunto publicado tem origem, periodicidade, data da última carga e responsável |
| CD-REG-004 | Fluxo oficial de publicação legislativa | P0 | “Enviado” é distinto de “publicado”; existe evidência do veículo oficial |
| CD-REG-005 | Matriz de regras por Lei Orgânica/Regimento | P0 | Workflows/parametrizações aprovados e versionados por Câmara |
| CD-REG-006 | SIC/LAI e prazos | P0 | Protocolo, prazo, prorrogação, resposta, negativa e recurso testados |
| CD-REG-007 | Ouvidoria e relatório de gestão | P1 | Fluxos e relatório anual/estatísticas seguem configuração validada |
| CD-REG-008 | Checklist de acessibilidade | P0 | Testes técnicos e manuais passam nos fluxos críticos públicos |
| CD-REG-009 | Linguagem simples no portal | P1 | Templates e conteúdo passaram por revisão; explicações de IA estão separadas do texto oficial |
| CD-REG-010 | Evidências para auditoria PNTP | P1 | Link, captura ou relatório verificável e data de checagem por critério |
| CD-REG-011 | Matriz de assinatura por espécie documental | P0 | Tipo de documento, nível, signatários e norma validados pela Câmara |
| CD-REG-012 | Integração PNCP monitorada | P1 | Manual/API versionados, homologação, recibo, retentativa e conciliação comprovados |
| CD-REG-013 | Matriz de eSocial por vínculo/regime | P1 | Evento aplicável, prazo, leiaute, recibo e rejeição testados com especialista |
| CD-REG-014 | Mapeamento e-Sfinge por exercício | P1 | Remessas requeridas para o órgão/ano são identificadas e validadas com o TCE-MS |
| CD-REG-015 | Validação IA com fontes e controle por tenant | P1 | Testes de vazamento, fonte, prompt injection e falha de recuperação passam |

### 32.1 Casos de teste transversais obrigatórios

- **Isolamento:** um usuário da Câmara A não pode consultar documento, relatório, índice semântico ou arquivo da Câmara B, nem inferir sua existência por mensagens de erro.
- **Publicidade:** usuário anônimo consegue consultar os conjuntos que devem ser públicos; não acessa rascunhos e anexos restritos.
- **Atualização:** falha na importação de dados gera alerta e não apresenta silenciosamente dados antigos como atuais.
- **Publicação:** uma submissão sem confirmação não muda o status para “publicado oficialmente”.
- **Versão documental:** alterar um arquivo assinado cria nova versão e preserva a anterior; a assinatura anterior não é apresentada como assinatura da versão nova.
- **Prazos:** cálculo dos prazos usa a regra e calendário vigentes, registra prorrogações autorizadas e não aplica a regra da LAI aos demais tipos de demanda por padrão.
- **Dados pessoais:** relatórios públicos removem os campos não divulgáveis; permissões de RH e ouvidoria são testadas por perfil.
- **IA sem fonte:** o sistema sinaliza insuficiência de fonte em vez de criar resposta normativa.
- **Prompt injection:** documento que contém instruções maliciosas não consegue alterar permissões, revelar dados ou contornar políticas.
- **Integração duplicada:** reenviar um lote/evento não cria registros ou pagamentos duplicados.
- **Mudança de regra:** alteração de Regimento/norma cria nova configuração com vigência e não modifica a interpretação dos registros anteriores.
- **Backup/restauração:** restaurar base e arquivos produz estado coerente e é testado periodicamente.

---

## 33. Fontes normativas adicionais e data de verificação

**Data de verificação documental desta revisão: 26 de outubro de 2026.** Os links abaixo são as fontes de referência para a etapa de especificação. Antes de cada integração ou release, verificar se houve alteração de lei, manual, leiaute, API ou critério de avaliação.

### Normas gerais

- [Constituição Federal — texto compilado](https://www.planalto.gov.br/ccivil_03/constituicao/constituicaocompilado.htm)
- [Lei nº 12.527/2011 — LAI](https://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm)
- [LC nº 101/2000 — Lei de Responsabilidade Fiscal](https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp101.htm)
- [LC nº 131/2009 — transparência fiscal](https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp131.htm)
- [Lei nº 14.133/2021 — Licitações e Contratos](https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2021/lei/l14133.htm)
- [Lei nº 13.460/2017 — usuários de serviços públicos e ouvidoria](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2017/lei/l13460.htm)
- [Lei nº 13.709/2018 — LGPD](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm)
- [Lei nº 13.146/2015 — Lei Brasileira de Inclusão](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2015/lei/l13146.htm)
- [Lei nº 15.263/2025 — Política Nacional de Linguagem Simples](https://www.planalto.gov.br/ccivil_03/_ato2023-2026/2025/lei/l15263.htm)
- [Lei nº 14.063/2020 — assinaturas eletrônicas](https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2020/lei/l14063.htm)
- [Lei nº 4.320/1964 — normas gerais de direito financeiro](https://www.planalto.gov.br/ccivil_03/leis/l4320.htm)
- [Lei nº 8.159/1991 — política nacional de arquivos públicos e privados](https://www.planalto.gov.br/ccivil_03/leis/l8159.htm)

### Transparência pública

- [PNTP — programa e metodologia 2026](https://pntp.atricon.org.br/o-programa/)
- [Cartilha PNTP 2026](https://radardatransparencia.atricon.org.br/pdf/Cartilha-PNTP-2026.pdf)
- [Matriz de critérios PNTP 2026](https://radardatransparencia.atricon.org.br/)
- [STF — Tema 483, divulgação de nomes e remuneração de servidores públicos](https://portal.stf.jus.br/jurisprudenciarepercussao/verAndamentoProcesso.asp?classeProcesso=ARE&incidente=4121428&numeroProcesso=652777&numeroTema=483)

### Integrações oficiais

- [eSocial — documentação técnica (leiautes, XSD, notas técnicas e manuais)](https://www.gov.br/esocial/pt-br/documentacao-tecnica)
- [Receita Federal — DCTFWeb](https://www.gov.br/pt-br/servicos/declarar-debitos-e-creditos-tributarios-federais)
- [Receita Federal — EFD-Reinf, perguntas frequentes](https://www.gov.br/receitafederal/pt-br/acesso-a-informacao/perguntas-frequentes/sped/efd-reinf/efdr)
- [TCE-MS — manuais e-Sfinge](https://tce.ms.gov.br/portaljurisdicionado/conteudos/lista/4/22)
- [TCE-MS — Manual e-Sfinge v2.0, exercício 2026](https://www.tce.ms.gov.br/portaljurisdicionado/files/conteudos/arquivo/1052/e153e977b513820b55c02680fea30a033ea14e51d53f245a2b6231d112730000.pdf)
- [PNCP — portal oficial](https://www.gov.br/pncp/pt-br)
- [PNCP — Manual de Integração, versão 2.6](https://pncp.gov.br/manual/pt-br/latest/singlehtml/index.html)
- [ITI — serviço VALIDAR](https://www.gov.br/pt-br/servicos/realizar-validacao-de-assinaturas-eletronicas-validar)
- [CGU — adesão ao Fala.BR](https://www.gov.br/ouvidorias/pt-br/ouvidorias/rede-de-ouvidorias/adesao-e-cadastros/adesao-fala.br)

### Notas de interpretação

1. A inclusão de uma lei nesta lista não significa que cada artigo seja aplicável a todo recurso do sistema; a matriz deve documentar escopo e condições.
2. PNTP é metodologia de avaliação e não substitui legislação. Seus critérios devem ser lidos na matriz oficial do ciclo e aplicados segundo as competências da Câmara.
3. Prazos, eventos, formatos, APIs, assinaturas e requisitos de publicação devem ser confirmados na fonte oficial vigente no momento da implantação.
4. Normas locais não podem ser inferidas a partir de uma Câmara modelo. O onboarding de cada cliente deve recolher e validar os documentos institucionais aplicáveis.
5. O produto pode apoiar a gestão de conformidade, mas não deve declarar automaticamente que a Câmara está “100% em conformidade” sem avaliação competente e evidências atualizadas.


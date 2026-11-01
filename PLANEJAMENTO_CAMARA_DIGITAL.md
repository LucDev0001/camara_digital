# Câmara Digital — Planejamento Mestre do Produto

**Versão:** 1.0  
**Data:** 26 de outubro de 2026  
**Status:** Documento de planejamento; requisitos e arquitetura ainda precisam ser confrontados com o código existente.  
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

### Uso dessas referências

- As referências orientam a descoberta dos requisitos, mas não substituem parecer jurídico, validação contábil, análise de segurança ou consulta ao órgão responsável.
- Regras que mudam por exercício, versão de leiaute, regulamento municipal ou Regimento Interno devem ser registradas com fonte, data de consulta e responsável pela validação.
- Registrar a versão do manual utilizada em cada integração e manter um processo de atualização.

---

## Definição de pronto para o produto

O Câmara Digital estará pronto para ser considerado uma plataforma operacional em produção apenas quando os módulos liberados tiverem sido validados em ambiente de uso real, com permissões e isolamento testados, dados conciliados, trilhas de auditoria, backups restauráveis, documentação operacional, suporte definido e critérios de publicação cumpridos.

A existência de um menu, formulário ou protótipo visual não significa que a funcionalidade esteja implementada. Cada módulo deve ter fluxo funcional, regras de negócio, persistência correta, autorização, tratamento de falhas e testes demonstráveis.

**Próxima ação recomendada:** executar a auditoria descrita na seção 17 usando o prompt da seção 18.2. Depois da auditoria, ajustar este roadmap à realidade do código e começar pelo primeiro lote de tarefas P0.

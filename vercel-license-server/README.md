# 🛡️ Câmara Digital Guard — Servidor de Licenciamento & Kill-Switch (Vercel)

Este é o sistema paralelo que roda na **Vercel** para gerenciar as assinaturas das Câmaras Municipais clientes, bloquear o acesso em caso de inadimplência (**Kill-Switch remoto**) e impedir cópias piratas do código-fonte por meio de **Hardware/Server Fingerprint Binding + Assinatura Criptográfica HMAC-SHA256**.

---

## 🚀 Como publicar na Vercel

1. Acesse a pasta do servidor de licenças:
   ```bash
   cd vercel-license-server
   ```
2. Faça o deploy na sua conta Vercel:
   ```bash
   npx vercel --prod
   ```
3. Configure (opcionalmente) as variáveis de ambiente no painel da Vercel:
   - `DEV_MASTER_SECRET`: Segredo criptográfico compartilhado com `includes/LicenseGuard.php` (Padrão: `CAMARA_DEV_MASTER_SECRET_2026`)
   - `DEV_ADMIN_PIN`: Senha do painel web do desenvolvedor (Padrão: `admin2026`)

---

## 🔐 Como funciona a Proteção Anti-Cópia e o Bloqueio por Inadimplência

1. **Fingerprint Único do Servidor (`LicenseGuard::getServerFingerprint()`)**:
   - Cada instalação do **Câmara Digital** calcula um hash SHA-256 combinando o hostname do servidor físico, o diretório raiz de instalação e o CNPJ da Câmara.
   - Se alguém copiar a pasta do sistema e o banco de dados para outro servidor/computador sem autorização, o Fingerprint muda e o sistema entra imediatamente em modo **`blocked_copy` (Cópia Não Autorizada Detectada)**.
2. **Heartbeat Automático com a Vercel (`/api/verify`)**:
   - O PHP consulta silenciosamente a API na Vercel (`vercel-license-server`) e valida a assinatura HMAC-SHA256.
   - Caso o desenvolvedor clique em **"Travar Sistema"** no painel da Vercel por inadimplência, a instância da Câmara bloqueia o painel administrativo e exibe a tela de suspensão contratual (`licenca_bloqueada.php`).
3. **Desbloqueio / Bloqueio por Token Assinado (Mesmo Offline)**:
   - No painel da Vercel, o desenvolvedor também pode gerar um **Token Criptográfico Base64 assinado com HMAC-SHA256** vinculado ao Fingerprint do servidor do cliente para ativar, renovar ou suspender a licença mesmo em servidores sem acesso externo.


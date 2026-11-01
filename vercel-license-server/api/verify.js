const { getLicenses, signLicensePayload } = require('./_store');

/**
 * Endpoint Serverless Vercel: /api/verify
 * Chamado periodicamente pelo ERP Câmara Digital (PHP) para validar:
 * 1. Se a assinatura está em dia (ou suspensa por inadimplência pelo desenvolvedor)
 * 2. Se o código-fonte está rodando no servidor/hardware autorizado (Trava Anti-Cópia)
 */
module.exports = async (req, res) => {
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  const body = req.method === 'POST' ? (req.body || {}) : (req.query || {});
  const { license_key, cnpj, domain, server_fingerprint } = body;

  if (!license_key && !cnpj) {
    return res.status(400).json({
      valid: false,
      status: 'suspended',
      error: 'Chave de licença ou CNPJ não informado na requisição.'
    });
  }

  const licenses = getLicenses();
  const cleanCnpj = String(cnpj || '').replace(/\D/g, '');

  let lic = licenses.find(
    l => l.license_key === license_key || (cleanCnpj && l.cnpj.replace(/\D/g, '') === cleanCnpj)
  );

  if (!lic) {
    return res.status(404).json({
      valid: false,
      status: 'suspended',
      block_reason: 'Licença não localizada na base central do desenvolvedor.',
      signature_hmac: ''
    });
  }

  // Atualiza telemetria do heartbeat
  lic.last_heartbeat_at = new Date().toISOString();
  lic.last_ip = req.headers['x-forwarded-for'] || req.socket?.remoteAddress || 'unknown';

  // Trava Anti-Cópia (Hardware / Server Fingerprint Binding)
  // No primeiro acesso, vincula o fingerprint do servidor; se mudar sem autorização do Dev, bloqueia por cópia pirata!
  if (server_fingerprint) {
    if (!lic.server_fingerprint) {
      lic.server_fingerprint = server_fingerprint;
    } else if (lic.server_fingerprint !== server_fingerprint) {
      lic.status = 'blocked_copy';
      lic.block_reason = `Tentativa de cópia detectada! O sistema foi movido para um servidor não autorizado (Fingerprint divergente: ${server_fingerprint.substring(0, 12)}...).`;
    }
  }

  // Verifica expiração da assinatura
  const today = new Date().toISOString().split('T')[0];
  if (lic.expires_at < today && lic.status === 'active') {
    lic.status = 'suspended';
    lic.block_reason = `Assinatura vencida em ${lic.expires_at}. Regularize a fatura junto ao desenvolvedor.`;
  }

  const effectiveFingerprint = lic.server_fingerprint || server_fingerprint || 'UNBOUND';
  const hmac = signLicensePayload(lic.license_key, lic.status, lic.expires_at, effectiveFingerprint);

  return res.status(200).json({
    valid: lic.status === 'active' || lic.status === 'grace_period',
    municipality: lic.municipality,
    license_key: lic.license_key,
    plan_name: lic.plan_name,
    status: lic.status,
    expires_at: lic.expires_at,
    server_fingerprint: effectiveFingerprint,
    block_reason: lic.block_reason,
    signature_hmac: hmac,
    checked_at: lic.last_heartbeat_at
  });
};


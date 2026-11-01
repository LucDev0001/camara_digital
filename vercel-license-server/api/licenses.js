const { getLicenses, DEV_ADMIN_PIN, signLicensePayload, generateUnlockToken } = require('./_store');

/**
 * Endpoint Serverless Vercel: /api/licenses
 * Gerencia todas as Câmaras licenciadas pelo Painel do Desenvolvedor:
 * - Listar clientes e status de pagamento
 * - Suspender / Bloquear imediatamente por inadimplência (Kill-switch)
 * - Liberar / Renovar assinatura
 * - Resetar vínculo de Hardware Fingerprint (autorizar migração de servidor)
 * - Gerar Token Criptográfico Assinado (HMAC-SHA256) para aplicação no cliente
 */
module.exports = async (req, res) => {
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  const licenses = getLicenses();

  if (req.method === 'GET') {
    const enriched = licenses.map(l => {
      const fp = l.server_fingerprint || 'AGUARDANDO_1O_ACESSO';
      return {
        ...l,
        signature_hmac: signLicensePayload(l.license_key, l.status, l.expires_at, fp),
        signed_token: generateUnlockToken(l.license_key, l.status, l.expires_at, fp, l.block_reason || '')
      };
    });
    return res.status(200).json({ status: 'ok', licenses: enriched });
  }

  if (req.method === 'POST') {
    const body = req.body || {};
    const pin = req.headers['x-dev-master-key'] || body.pin;

    if (pin !== DEV_ADMIN_PIN) {
      return res.status(401).json({ error: 'PIN de Desenvolvedor inválido. Padrão: admin2026' });
    }

    const { action, id } = body;

    if (action === 'create') {
      const newId = licenses.length ? Math.max(...licenses.map(x => x.id)) + 1 : 1;
      const randomHex = Math.random().toString(16).substring(2, 10).toUpperCase();
      const newLic = {
        id: newId,
        municipality: body.municipality || 'Nova Câmara Municipal',
        cnpj: body.cnpj || '00.000.000/0001-00',
        license_key: body.license_key || `LIC-${randomHex}-2026-PRO`,
        plan_name: body.plan_name || 'Assinatura Anual Enterprise + IA',
        monthly_fee: parseFloat(body.monthly_fee || 2500),
        status: 'active',
        allowed_domain: body.allowed_domain || 'localhost',
        server_fingerprint: body.server_fingerprint || null,
        expires_at: body.expires_at || '2027-12-31',
        block_reason: null,
        last_heartbeat_at: null,
        last_ip: null
      };
      licenses.push(newLic);
      return res.status(201).json({ status: 'created', license: newLic });
    }

    const lic = licenses.find(l => Number(l.id) === Number(id));
    if (!lic) {
      return res.status(404).json({ error: 'Licença não encontrada.' });
    }

    if (action === 'toggle_status') {
      lic.status = body.new_status || (lic.status === 'active' ? 'suspended' : 'active');
      lic.block_reason = lic.status === 'active'
        ? null
        : (body.block_reason || 'Suspensão administrativa por pendência financeira / inadimplência contratual.');
    } else if (action === 'reset_fingerprint') {
      lic.server_fingerprint = body.new_fingerprint || null;
      if (lic.status === 'blocked_copy') {
        lic.status = 'active';
        lic.block_reason = null;
      }
    } else if (action === 'extend_expiry') {
      lic.expires_at = body.expires_at || '2027-12-31';
      lic.status = 'active';
      lic.block_reason = null;
    }

    const fp = lic.server_fingerprint || body.server_fingerprint || 'UNBOUND';
    return res.status(200).json({
      status: 'updated',
      license: {
        ...lic,
        signature_hmac: signLicensePayload(lic.license_key, lic.status, lic.expires_at, fp),
        signed_token: generateUnlockToken(lic.license_key, lic.status, lic.expires_at, fp, lic.block_reason || '')
      }
    });
  }

  return res.status(405).json({ error: 'Method not allowed' });
};


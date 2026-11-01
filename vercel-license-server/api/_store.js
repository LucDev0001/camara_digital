const crypto = require('crypto');

const DEV_MASTER_SECRET = process.env.DEV_MASTER_SECRET || 'CAMARA_DEV_MASTER_SECRET_2026';
const DEV_ADMIN_PIN = process.env.DEV_ADMIN_PIN || 'admin2026';

// Registro inicial de licenças das Câmaras Municipais
// Em produção na Vercel, pode ser conectado ao Vercel KV / Upstash Redis ou Neon Postgres via variáveis de ambiente.
global.__CAMARA_LICENSES = global.__CAMARA_LICENSES || [
  {
    id: 1,
    municipality: 'Câmara Municipal de Campo Grande - MS',
    cnpj: '03.243.511/0001-80',
    license_key: 'LIC-60F99A8D-2026-PRO',
    plan_name: 'Assinatura Anual Enterprise + IA',
    monthly_fee: 2800.00,
    status: 'active', // 'active' | 'grace_period' | 'suspended' | 'blocked_copy'
    allowed_domain: 'localhost',
    server_fingerprint: null, // Vincula automaticamente no 1º heartbeat (Trust On First Use)
    expires_at: '2027-10-30',
    block_reason: null,
    last_heartbeat_at: null,
    last_ip: null
  },
  {
    id: 2,
    municipality: 'Câmara Municipal de Bonito - MS',
    cnpj: '01.554.212/0001-30',
    license_key: 'LIC-9D31B42A-2026-PRO',
    plan_name: 'Assinatura Anual Enterprise + IA',
    monthly_fee: 1950.00,
    status: 'active',
    allowed_domain: 'localhost',
    server_fingerprint: null,
    expires_at: '2027-10-30',
    block_reason: null,
    last_heartbeat_at: null,
    last_ip: null
  }
];

function signLicensePayload(licenseKey, status, expiresAt, fingerprint) {
  const payload = `${licenseKey}|${status}|${expiresAt}|${fingerprint}`;
  return crypto.createHmac('sha256', DEV_MASTER_SECRET).update(payload).digest('hex');
}

function generateUnlockToken(licenseKey, status, expiresAt, fingerprint, blockReason = '') {
  const signature = signLicensePayload(licenseKey, status, expiresAt, fingerprint);
  const tokenObj = {
    license_key: licenseKey,
    status,
    expires_at: expiresAt,
    server_fingerprint: fingerprint,
    block_reason: blockReason,
    signature_hmac: signature,
    issued_at: new Date().toISOString()
  };
  return Buffer.from(JSON.stringify(tokenObj)).toString('base64');
}

module.exports = {
  DEV_MASTER_SECRET,
  DEV_ADMIN_PIN,
  getLicenses: () => global.__CAMARA_LICENSES,
  signLicensePayload,
  generateUnlockToken
};


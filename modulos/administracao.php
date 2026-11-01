<?php
/**
 * Câmara Digital - Módulo 1: Administração, Controle de Acesso (RBAC) e Licenciamento Vercel
 * Conforme Seção 4.1 do Planejamento Mestre e Decisões da Reunião de 30/10/2026
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/LicenseGuard.php';

$pageTitle = 'Administração, Permissões RBAC e Licenciamento';
$tenantId = Tenant::id();
$db = Database::getConnection();

// 1. Processa atualização dos dados institucionais
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tenant'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token de autenticação inválido.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $cnpj = trim($_POST['cnpj'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $stmt = $db->prepare("UPDATE tenants SET name = ?, cnpj = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $cnpj, $email, $phone, $address, $tenantId]);
        Audit::log('ATUALIZAR_INSTITUICAO', 'tenants', $tenantId, "Atualização cadastral da instituição {$name}");
        setFlash('success', 'Dados institucionais atualizados com sucesso!');
        header("Location: " . url('modulos/administracao.php'));
        exit;
    }
}

// 2. Processa Matriz de Permissões de Módulos por Tipo de Usuário (Administrador Geral da Câmara)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_rbac_matrix'])) {
    if (!Auth::hasRole('admin')) {
        setFlash('error', 'Apenas o Administrador Geral da Câmara pode alterar os níveis de acesso.');
    } elseif (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token CSRF inválido.');
    } else {
        $matrix = $_POST['perm'] ?? []; // perm[role_id][module_slug] = 1
        $rolesList = $db->query("SELECT id, slug FROM roles WHERE slug != 'admin'")->fetchAll();
        $allMods = ['administracao', 'legislativo', 'rh', 'almoxarifado', 'compras', 'patrimonio', 'financeiro', 'transparencia', 'ouvidoria', 'protocolo', 'controle_interno', 'ia_legislativa', 'noticias', 'backup'];

        $stmtUpsert = $db->prepare("
            INSERT INTO role_module_permissions (tenant_id, role_id, module_slug, can_access)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE can_access = VALUES(can_access)
        ");

        foreach ($rolesList as $r) {
            $rId = (int)$r['id'];
            foreach ($allMods as $mod) {
                $can = !empty($matrix[$rId][$mod]) ? 1 : 0;
                $stmtUpsert->execute([$tenantId, $rId, $mod, $can]);
            }
        }

        Audit::log('ATUALIZAR_RBAC', 'role_module_permissions', $tenantId, 'Matriz de acesso aos módulos por tipo de usuário atualizada pelo Administrador Geral.');
        setFlash('success', 'Permissões de acesso aos módulos por tipo de usuário salvas com sucesso!');
        header("Location: " . url('modulos/administracao.php#rbac-matrix'));
        exit;
    }
}

// 3. Processa Criação ou Atualização de Usuário e Vínculo Parlamentar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    if (!Auth::hasRole('admin')) {
        setFlash('error', 'Apenas o Administrador Geral pode gerenciar usuários.');
    } elseif (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $uName = trim($_POST['user_name'] ?? '');
        $uEmail = trim($_POST['user_email'] ?? '');
        $uRoleId = (int)($_POST['role_id'] ?? 3);
        $uJob = trim($_POST['job_title'] ?? '');
        $uDept = trim($_POST['department'] ?? '');
        $uPass = trim($_POST['password'] ?? '123456');
        $councilorId = !empty($_POST['councilor_id']) ? (int)$_POST['councilor_id'] : null;

        if (!empty($uName) && !empty($uEmail)) {
            $hash = password_hash($uPass ?: '123456', PASSWORD_BCRYPT);
            $stmt = $db->prepare("
                INSERT INTO users (tenant_id, role_id, name, email, password_hash, job_title, department, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())
            ");
            $stmt->execute([$tenantId, $uRoleId, $uName, $uEmail, $hash, $uJob, $uDept]);
            $newUserId = (int)$db->lastInsertId();

            if ($councilorId) {
                $db->prepare("UPDATE councilors SET user_id = ? WHERE id = ? AND tenant_id = ?")
                   ->execute([$newUserId, $councilorId, $tenantId]);
            }

            Audit::log('CRIAR_USUARIO', 'users', $newUserId, "Usuário '{$uName}' cadastrado com role_id={$uRoleId}");
            setFlash('success', "Usuário '{$uName}' criado e configurado com sucesso!");
            header("Location: " . url('modulos/administracao.php#users-section'));
            exit;
        }
    }
}

// 4. Processa Alteração Rápida de Perfil de Usuário Existente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_user_role'])) {
    if (Auth::hasRole('admin') && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $targetUserId = (int)($_POST['user_id'] ?? 0);
        $newRoleId = (int)($_POST['role_id'] ?? 0);
        if ($targetUserId && $newRoleId) {
            $db->prepare("UPDATE users SET role_id = ? WHERE id = ? AND tenant_id = ?")
               ->execute([$newRoleId, $targetUserId, $tenantId]);
            Audit::log('ALTERAR_PERFIL_USUARIO', 'users', $targetUserId, "Perfil do usuário ID {$targetUserId} alterado para role_id {$newRoleId}");
            setFlash('success', 'Tipo de usuário atualizado com sucesso!');
            header("Location: " . url('modulos/administracao.php#users-section'));
            exit;
        }
    }
}

// 5. Processa Ações de Licenciamento e Trava do Desenvolvedor (Vercel Guard)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['license_action'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $subAction = $_POST['license_action'];
        if ($subAction === 'sync_vercel') {
            $urlVercel = trim($_POST['vercel_api_url'] ?? '');
            if (!empty($urlVercel)) {
                $db->prepare("UPDATE system_licenses SET vercel_api_url = ? WHERE tenant_id = ?")->execute([$urlVercel, $tenantId]);
            }
            $res = LicenseGuard::syncWithVercel($tenantId);
            setFlash($res['synced'] ? 'success' : 'info', $res['message']);
        } elseif ($subAction === 'apply_token') {
            $token = trim($_POST['signed_token'] ?? '');
            $res = LicenseGuard::applySignedToken($token, $tenantId);
            if ($res['success']) {
                setFlash('success', 'Token criptográfico Vercel validado e aplicado com sucesso!');
            } else {
                setFlash('error', $res['error']);
            }
        } elseif ($subAction === 'dev_simulate') {
            $pin = trim($_POST['dev_pin'] ?? '');
            $simStatus = $_POST['sim_status'] ?? 'active';
            $reason = trim($_POST['block_reason'] ?? '');
            $res = LicenseGuard::devOverride($pin, $simStatus, $reason ?: null, null, $tenantId);
            if ($res['success']) {
                setFlash('warning', "Status da trava alterado para [{$simStatus}].");
            } else {
                setFlash('error', $res['error']);
            }
        }
        header("Location: " . url('modulos/administracao.php#license-guard'));
        exit;
    }
}

// 6. Processa novo departamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_department'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $deptName = trim($_POST['dept_name'] ?? '');
        $deptCode = trim($_POST['dept_code'] ?? '');
        $deptManager = trim($_POST['dept_manager'] ?? '');
        $deptEmail = trim($_POST['dept_email'] ?? '');

        if (!empty($deptName)) {
            $stmt = $db->prepare("INSERT INTO departments (tenant_id, name, code, manager_name, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tenantId, $deptName, $deptCode, $deptManager, $deptEmail]);
            Audit::log('CRIAR_DEPARTAMENTO', 'departments', (int)$db->lastInsertId(), "Departamento criado: {$deptName}");
            setFlash('success', "Departamento '{$deptName}' cadastrado com sucesso.");
            header("Location: " . url('modulos/administracao.php#depts'));
            exit;
        }
    }
}

// 7. Processa atualização do Perfil de Conformidade Jurídica (Seção 31 do Planejamento v1.2)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_compliance'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token de autenticação inválido.');
    } else {
        $loNum = trim($_POST['lei_organica_num'] ?? '');
        $loAno = (int)($_POST['lei_organica_ano'] ?? 1990);
        $riVersao = trim($_POST['regimento_interno_versao'] ?? '');
        $domTipo = trim($_POST['diario_oficial_tipo'] ?? '');
        $domNome = trim($_POST['diario_oficial_nome'] ?? '');
        $domUrl = trim($_POST['diario_oficial_url'] ?? '');
        $decLai = trim($_POST['decreto_lai_num'] ?? '');
        $respJur = trim($_POST['responsavel_juridico'] ?? '');
        $respCi = trim($_POST['responsavel_controle_interno'] ?? '');

        $stmt = $db->prepare("
            INSERT INTO tenant_compliance_profiles 
            (tenant_id, lei_organica_num, lei_organica_ano, regimento_interno_versao, diario_oficial_tipo, diario_oficial_nome, diario_oficial_url, decreto_lai_num, responsavel_juridico, responsavel_controle_interno)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                lei_organica_num = VALUES(lei_organica_num),
                lei_organica_ano = VALUES(lei_organica_ano),
                regimento_interno_versao = VALUES(regimento_interno_versao),
                diario_oficial_tipo = VALUES(diario_oficial_tipo),
                diario_oficial_nome = VALUES(diario_oficial_nome),
                diario_oficial_url = VALUES(diario_oficial_url),
                decreto_lai_num = VALUES(decreto_lai_num),
                responsavel_juridico = VALUES(responsavel_juridico),
                responsavel_controle_interno = VALUES(responsavel_controle_interno)
        ");
        $stmt->execute([$tenantId, $loNum, $loAno, $riVersao, $domTipo, $domNome, $domUrl, $decLai, $respJur, $respCi]);

        Audit::log('ATUALIZAR_CONFORMIDADE', 'tenant_compliance_profiles', $tenantId, "Perfil de conformidade jurídica atualizado conforme Planejamento v1.2");
        setFlash('success', 'Perfil de Conformidade Jurídica do Município atualizado com sucesso!');
        header("Location: " . url('modulos/administracao.php#compliance'));
        exit;
    }
}

$tenant = Tenant::current();
$departments = $db->query("SELECT * FROM departments WHERE tenant_id = {$tenantId} ORDER BY name ASC")->fetchAll();
$councilors = $db->query("SELECT * FROM councilors WHERE tenant_id = {$tenantId} ORDER BY board_role DESC, full_name ASC")->fetchAll();
$committees = $db->query("SELECT * FROM committees WHERE tenant_id = {$tenantId} ORDER BY name ASC")->fetchAll();
$allRoles = $db->query("SELECT * FROM roles ORDER BY level DESC, id ASC")->fetchAll();
$users = $db->query("
    SELECT u.*, r.name as role_name, r.slug as role_slug, c.full_name as councilor_linked_name
    FROM users u 
    INNER JOIN roles r ON u.role_id = r.id 
    LEFT JOIN councilors c ON c.user_id = u.id AND c.tenant_id = u.tenant_id
    WHERE u.tenant_id = {$tenantId} 
    ORDER BY r.level DESC, u.name ASC
")->fetchAll();

// Carrega Matriz RBAC atual
$permRows = $db->query("SELECT role_id, module_slug, can_access FROM role_module_permissions WHERE tenant_id = {$tenantId}")->fetchAll();
$permMap = [];
foreach ($permRows as $pr) {
    $permMap[(int)$pr['role_id']][$pr['module_slug']] = (int)$pr['can_access'];
}

$moduleLabels = [
    'administracao'    => '1. Administração',
    'legislativo'      => '2. Legislativo & Votação',
    'rh'               => '3. Recursos Humanos',
    'almoxarifado'     => '4. Almoxarifado',
    'compras'          => '5. Compras & PNCP',
    'patrimonio'       => '6. Patrimônio & Frota',
    'financeiro'       => '7. Financeiro',
    'transparencia'    => '8. Transparência',
    'ouvidoria'        => '9. Ouvidoria & SIC',
    'protocolo'        => '10. Protocolo Geral',
    'controle_interno' => '11. Controle Interno',
    'ia_legislativa'   => '12. IA Legislativa',
    'noticias'         => '13. Notícias (CMS)',
    'backup'           => '14. Backup & DR'
];

$licenseInfo = LicenseGuard::check($tenantId);
$lic = $licenseInfo['license'];
$serverFp = $licenseInfo['current_fingerprint'];

$complianceProfile = $db->query("SELECT * FROM tenant_compliance_profiles WHERE tenant_id = {$tenantId}")->fetch() ?: [
    'lei_organica_num' => 'Lei Orgânica Municipal nº 01/1990',
    'lei_organica_ano' => 1990,
    'regimento_interno_versao' => 'Resolução nº 04/2012 e alterações',
    'diario_oficial_tipo' => 'Diário Oficial dos Municípios',
    'diario_oficial_nome' => 'DOM / Diário da ASSOMASUL',
    'diario_oficial_url' => 'https://www.diariomunicipal.com.br/',
    'decreto_lai_num' => 'Decreto Municipal nº 1.250/2013',
    'responsavel_juridico' => 'Procuradoria Jurídica da Câmara',
    'responsavel_controle_interno' => 'Unidade de Controle Interno (UCI)'
];

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 1</span>
                    <span>•</span>
                    <span>Gestão Organizacional, RBAC & Licenciamento</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Administração Geral & Controle de Acesso</h1>
                <p class="text-xs sm:text-sm text-slate-500">Configuração institucional, limitação de módulos por tipo de usuário (Administrador Geral) e proteção de licença Vercel.</p>
            </div>
            <div class="flex items-center gap-2">
                <?php if (Auth::hasRole('admin')): ?>
                <button onclick="openModal('new-user-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Novo Usuário / Vereador</span>
                </button>
                <?php endif; ?>
                <button onclick="openModal('new-department-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    <span>Novo Setor / Gabinete</span>
                </button>
            </div>
        </div>

        <?php if (Auth::hasRole('admin')): ?>
        <!-- SEÇÃO ESPECIAL 1: MATRIZ DE PERMISSÕES POR TIPO DE USUÁRIO (ADMINISTRADOR GERAL DA CÂMARA) -->
        <div id="rbac-matrix" class="bg-white rounded-2xl border-2 border-indigo-200 shadow-sm overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-300 text-lg">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-indigo-500/30 text-indigo-200">Exclusivo do Administrador Geral da Câmara</span>
                        </div>
                        <h2 class="text-base font-bold text-white mt-0.5">Controle de Níveis de Acesso: Limitar Módulos por Tipo de Usuário</h2>
                        <p class="text-xs text-slate-300">Marque ou desmarque quais módulos do sistema cada categoria de usuário da Câmara tem permissão para acessar.</p>
                    </div>
                </div>
            </div>

            <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="p-4 sm:p-6 space-y-4">
                <?= csrfField() ?>
                <input type="hidden" name="save_rbac_matrix" value="1">

                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-[11px] text-slate-700">
                        <thead class="bg-slate-100 text-[10px] uppercase font-bold text-slate-600 border-b border-slate-200">
                            <tr>
                                <th class="p-2.5 sticky left-0 bg-slate-100 z-10 min-w-[170px]">Tipo de Usuário (Cargo)</th>
                                <?php foreach ($moduleLabels as $mSlug => $mLabel): ?>
                                    <th class="p-2 text-center whitespace-nowrap"><?= htmlspecialchars($mLabel) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($allRoles as $role): ?>
                                <?php if ($role['slug'] === 'admin') continue; ?>
                                <tr class="hover:bg-indigo-50/40">
                                    <td class="p-2.5 font-bold text-slate-900 sticky left-0 bg-white z-10 border-r border-slate-100">
                                        <?= htmlspecialchars($role['name']) ?>
                                        <span class="block text-[9px] font-normal text-slate-400">Nível <?= (int)$role['level'] ?></span>
                                    </td>
                                    <?php foreach ($moduleLabels as $mSlug => $mLabel): ?>
                                        <?php $checked = !empty($permMap[(int)$role['id']][$mSlug]); ?>
                                        <td class="p-2 text-center">
                                            <input type="checkbox" 
                                                   name="perm[<?= (int)$role['id'] ?>][<?= $mSlug ?>]" 
                                                   value="1" 
                                                   <?= $checked ? 'checked' : '' ?>
                                                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                    <p class="text-xs text-slate-500">
                        <i class="fa-solid fa-circle-info text-indigo-600 mr-1"></i>
                        O perfil <strong>Administrador Geral</strong> possui acesso total permanente a todos os módulos. As alterações refletem imediatamente no menu lateral e nas travas de rota de cada usuário.
                    </p>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Salvar Matriz de Permissões
                    </button>
                </div>
            </form>
        </div>

        <!-- SEÇÃO ESPECIAL 2: LICENCIAMENTO VERCEL, CONTROLE DE ASSINATURA & TRAVA ANTI-CÓPIA DO DESENVOLVEDOR -->
        <div id="license-guard" class="bg-slate-900 rounded-2xl border border-slate-800 text-slate-100 p-6 shadow-xl space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Proteção Anti-Cópia & Assinatura Ativa
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">HMAC-SHA256</span>
                        </div>
                        <h2 class="text-base font-bold text-white mt-1">Gerenciador de Licença & Sincronização com Servidor Vercel</h2>
                        <p class="text-xs text-slate-400">Vincula o código-fonte ao hardware deste servidor e sincroniza o status da assinatura com o sistema paralelo na Vercel.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?= url('vercel-license-server/public/index.html') ?>" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow transition">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Abrir Console Master Vercel (Dev)</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Chave de Licença da Câmara</span>
                    <p class="font-mono font-bold text-sky-400 text-sm mt-1"><?= htmlspecialchars($lic['license_key']) ?></p>
                    <span class="text-[11px] text-slate-400 mt-1 block">Plano: <?= htmlspecialchars($lic['plan_name']) ?></span>
                </div>
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Validade & Heartbeat</span>
                    <p class="font-mono font-bold text-emerald-400 text-sm mt-1">Vence em <?= formatDate($lic['expires_at']) ?></p>
                    <span class="text-[11px] text-slate-400 mt-1 block">Última verificação: <?= formatDate($lic['last_heartbeat_at'], true) ?></span>
                </div>
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-slate-500">Fingerprint Físico do Servidor (Anti-Cópia)</span>
                        <button type="button" onclick="navigator.clipboard.writeText('<?= $serverFp ?>'); showToast('success', 'Fingerprint copiado!');" class="text-[10px] text-sky-400 hover:underline">Copiar</button>
                    </div>
                    <p class="font-mono text-[11px] text-slate-300 mt-1 truncate" title="<?= htmlspecialchars($serverFp) ?>"><?= htmlspecialchars($serverFp) ?></p>
                    <span class="text-[10px] text-emerald-400 mt-1 block"><i class="fa-solid fa-lock mr-1"></i> Vinculado a <?= htmlspecialchars(php_uname('n')) ?></span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2">
                <!-- Formulário de Sincronização com API Vercel / Token -->
                <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 space-y-3 text-xs">
                    <?= csrfField() ?>
                    <input type="hidden" name="license_action" value="sync_vercel">
                    <label class="block font-bold text-slate-300">Endpoint da API de Licenças na Vercel:</label>
                    <div class="flex gap-2">
                        <input type="url" name="vercel_api_url" value="<?= htmlspecialchars($lic['vercel_api_url']) ?>"
                               class="flex-1 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white font-mono text-xs">
                        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-lg whitespace-nowrap">
                            <i class="fa-solid fa-rotate mr-1"></i> Sincronizar Agora
                        </button>
                    </div>
                </form>

                <!-- Simulador de Trava do Desenvolvedor (Para testar inadimplência ou cópia pirata) -->
                <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="p-4 rounded-xl bg-slate-950/80 border border-rose-500/30 space-y-3 text-xs">
                    <?= csrfField() ?>
                    <input type="hidden" name="license_action" value="dev_simulate">
                    <label class="block font-bold text-rose-300">
                        <i class="fa-solid fa-bolt mr-1"></i> Testar Trava de Segurança / Kill-Switch (Exige PIN do Dev):
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <select name="sim_status" class="px-2.5 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs">
                            <option value="suspended">Simular Inadimplência (Travar)</option>
                            <option value="blocked_copy">Simular Cópia Pirata (Travar)</option>
                            <option value="active">Manter Ativo / Liberado</option>
                        </select>
                        <input type="password" name="dev_pin" placeholder="PIN (admin2026)" required
                               class="px-2.5 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white font-mono text-xs">
                        <button type="submit" class="px-3 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-lg">
                            Acionar Trava
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Section 1: Dados Institucionais -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2 mb-4">
                <i class="fa-solid fa-landmark text-sky-600"></i>
                <span>Informações Cadastrais da Câmara Municipal</span>
            </h2>
            <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?= csrfField() ?>
                <input type="hidden" name="update_tenant" value="1">
                
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-700">Razão Social / Nome Oficial</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($tenant['name']) ?>" required
                           class="mt-1 w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700">CNPJ Oficial</label>
                    <input type="text" name="cnpj" value="<?= htmlspecialchars($tenant['cnpj']) ?>" required
                           class="mt-1 w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700">E-mail Institucional</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($tenant['email'] ?? '') ?>"
                           class="mt-1 w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700">Telefone / PABX</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($tenant['phone'] ?? '') ?>"
                           class="mt-1 w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-700">Endereço da Sede do Legislativo</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($tenant['address'] ?? '') ?>"
                           class="mt-1 w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>

                <div class="sm:col-span-3 flex justify-end pt-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                        Salvar Informações Básicas
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 5: Gestão de Usuários e Perfis -->
        <div id="users-section" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-user-lock text-purple-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Usuários do Sistema e Atribuição de Tipo de Acesso (RBAC)</h2>
                </div>
                <span class="text-xs text-slate-500"><?= count($users) ?> usuários cadastrados</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Nome</th>
                            <th class="p-3">E-mail (Login)</th>
                            <th class="p-3">Tipo de Usuário / Perfil</th>
                            <th class="p-3">Cargo / Vínculo Parlamentar</th>
                            <th class="p-3">Último Acesso</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-semibold text-slate-900"><?= htmlspecialchars($u['name']) ?></td>
                                <td class="p-3 font-mono text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="p-3">
                                    <?php if (Auth::hasRole('admin') && (int)$u['id'] !== Auth::id()): ?>
                                        <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="inline-flex items-center gap-1">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="change_user_role" value="1">
                                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                            <select name="role_id" onchange="this.form.submit()" class="px-2 py-1 text-[11px] font-bold bg-sky-50 text-sky-900 border border-sky-200 rounded-lg cursor-pointer">
                                                <?php foreach ($allRoles as $r): ?>
                                                    <option value="<?= (int)$r['id'] ?>" <?= (int)$r['id'] === (int)$u['role_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($r['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">
                                            <?= htmlspecialchars($u['role_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-slate-700">
                                    <?= htmlspecialchars($u['job_title'] ?: '-') ?> (<?= htmlspecialchars($u['department'] ?: '-') ?>)
                                    <?php if (!empty($u['councilor_linked_name'])): ?>
                                        <span class="ml-1 px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                            <i class="fa-solid fa-check-to-slot"></i> Voto Vinculado: <?= htmlspecialchars($u['councilor_linked_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 font-mono text-[11px] text-slate-500"><?= formatDate($u['last_login'], true) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1.5: Perfil de Conformidade Jurídica e Versionamento Normativo (Seção 31 do Planejamento v1.2) -->
        <div id="compliance" class="bg-white rounded-xl border border-indigo-200 shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <span>Perfil de Conformidade Jurídica & Regimento Interno</span>
                            <span class="text-[10px] bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded font-bold">Planejamento v1.2 • Seção 31</span>
                        </h2>
                        <p class="text-xs text-slate-500">Parametrização das fontes normativas locais e regras de publicação oficial.</p>
                    </div>
                </div>
            </div>

            <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <?= csrfField() ?>
                <input type="hidden" name="update_compliance" value="1">

                <div>
                    <label class="block font-medium text-slate-700">Lei Orgânica Municipal (LOM)</label>
                    <input type="text" name="lei_organica_num" value="<?= htmlspecialchars($complianceProfile['lei_organica_num']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Ano de Promulgação da LOM</label>
                    <input type="number" name="lei_organica_ano" value="<?= (int)$complianceProfile['lei_organica_ano'] ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Versão Vigente do Regimento Interno</label>
                    <input type="text" name="regimento_interno_versao" value="<?= htmlspecialchars($complianceProfile['regimento_interno_versao']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Tipo do Veículo Oficial de Publicação</label>
                    <input type="text" name="diario_oficial_tipo" value="<?= htmlspecialchars($complianceProfile['diario_oficial_tipo']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Nome Oficial do Diário</label>
                    <input type="text" name="diario_oficial_nome" value="<?= htmlspecialchars($complianceProfile['diario_oficial_nome']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">URL / Link Permanente do Diário Oficial</label>
                    <input type="url" name="diario_oficial_url" value="<?= htmlspecialchars($complianceProfile['diario_oficial_url']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Decreto Regulamentador da LAI (SIC)</label>
                    <input type="text" name="decreto_lai_num" value="<?= htmlspecialchars($complianceProfile['decreto_lai_num']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Responsável Jurídico pela Validação</label>
                    <input type="text" name="responsavel_juridico" value="<?= htmlspecialchars($complianceProfile['responsavel_juridico']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Responsável pelo Controle Interno (UCI)</label>
                    <input type="text" name="responsavel_controle_interno" value="<?= htmlspecialchars($complianceProfile['responsavel_controle_interno']) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="sm:col-span-3 flex justify-end pt-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-700 hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-sm transition-colors">
                        Atualizar Perfil de Conformidade Jurídica
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 2: Mesa Diretora e Vereadores (Grid) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-users-viewfinder text-amber-600"></i>
                    <span>Mesa Diretora e Composição Parlamentar (11ª Legislatura)</span>
                </h2>
                <span class="text-xs text-slate-500"><?= count($councilors) ?> Vereadores Registrados</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($councilors as $c): ?>
                    <div class="p-4 rounded-xl border border-slate-200 hover:border-sky-300 bg-slate-50/50 flex items-start gap-3 transition-colors">
                        <img src="<?= htmlspecialchars($c['photo_url'] ?: 'https://via.placeholder.com/150') ?>" 
                             alt="<?= htmlspecialchars($c['full_name']) ?>" 
                             class="w-12 h-12 rounded-xl object-cover border border-slate-300">
                        <div class="flex-1 overflow-hidden">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider <?= $c['board_role'] !== 'Membro' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700' ?>">
                                    <?= htmlspecialchars($c['board_role']) ?>
                                </span>
                                <span class="text-[10px] font-semibold text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-100"><?= htmlspecialchars($c['political_party']) ?></span>
                            </div>
                            <h3 class="font-bold text-xs text-slate-900 mt-1 truncate"><?= htmlspecialchars($c['full_name']) ?></h3>
                            <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($c['email'] ?? 'gabinete@camara.ms.gov.br') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Section 3: Comissões Permanentes -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2 mb-4">
                <i class="fa-solid fa-scale-balanced text-indigo-600"></i>
                <span>Comissões Permanentes da Câmara</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach ($committees as $com): ?>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <h3 class="font-bold text-xs text-indigo-950"><?= htmlspecialchars($com['name']) ?></h3>
                        <p class="text-[11px] text-slate-600 mt-1"><?= htmlspecialchars($com['description']) ?></p>
                        <div class="mt-3 pt-2 border-t border-slate-200 text-[11px] space-y-1">
                            <p><span class="text-slate-500">Presidente:</span> <strong class="text-slate-800"><?= htmlspecialchars($com['president_name']) ?></strong></p>
                            <p><span class="text-slate-500">Relator:</span> <strong class="text-slate-800"><?= htmlspecialchars($com['rapporteur_name']) ?></strong></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Section 4: Departamentos e Setores -->
        <div id="depts" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-sitemap text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Estrutura Organizacional e Setores</h2>
                </div>
                <span class="text-xs text-slate-500"><?= count($departments) ?> setores ativos</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Código</th>
                            <th class="p-3">Nome do Setor / Unidade</th>
                            <th class="p-3">Responsável</th>
                            <th class="p-3">E-mail de Contato</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($departments as $d): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($d['code'] ?: '-') ?></td>
                                <td class="p-3 font-semibold text-slate-900"><?= htmlspecialchars($d['name']) ?></td>
                                <td class="p-3 text-slate-700"><?= htmlspecialchars($d['manager_name'] ?: 'Não atribuído') ?></td>
                                <td class="p-3 text-slate-500"><?= htmlspecialchars($d['email'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Novo Usuário -->
<div id="new-user-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Cadastrar Novo Usuário e Nível de Acesso</h3>
            <button onclick="closeModal('new-user-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="p-5 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="save_user" value="1">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Nome Completo</label>
                    <input type="text" name="user_name" required placeholder="Ex: Ver. Rodrigo Mendes"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">E-mail de Login</label>
                    <input type="email" name="user_email" required placeholder="rodrigo@camara.ms.gov.br"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Tipo de Usuário (Perfil RBAC)</label>
                    <select name="role_id" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <?php foreach ($allRoles as $r): ?>
                            <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Vincular a Vereador (Para Votação)</label>
                    <select name="councilor_id" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Não é Vereador (Servidor Administrativo)</option>
                        <?php foreach ($councilors as $c): ?>
                            <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['political_party']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Cargo Funcional</label>
                    <input type="text" name="job_title" placeholder="Ex: Vereador / Analista"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Setor / Lotação</label>
                    <input type="text" name="department" placeholder="Ex: Gabinete 03"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Senha Inicial</label>
                    <input type="text" name="password" value="123456" required
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-user-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg shadow-sm">
                    Criar Usuário
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Novo Departamento -->
<div id="new-department-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Cadastrar Novo Setor / Gabinete</h3>
            <button onclick="closeModal('new-department-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/administracao.php') ?>" method="POST" class="p-5 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_department" value="1">
            
            <div>
                <label class="block font-medium text-slate-700">Nome do Setor / Unidade</label>
                <input type="text" name="dept_name" required placeholder="Ex: Gabinete de Apoio Jurídico"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Código / Sigla</label>
                <input type="text" name="dept_code" placeholder="Ex: GABJUR"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">Responsável pelo Setor</label>
                <input type="text" name="dept_manager" placeholder="Ex: Dr. Roberto Alencar"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700">E-mail Institucional</label>
                <input type="email" name="dept_email" placeholder="juridico@camara.ms.gov.br"
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="pt-3 flex justify-end gap-2">
                <button type="button" onclick="closeModal('new-department-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Setor
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

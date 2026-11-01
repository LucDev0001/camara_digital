<?php
/**
 * Câmara Digital - Módulo 3: Recursos Humanos e Gestão de Pessoas
 * Conforme Seção 4.3 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';

$pageTitle = 'Recursos Humanos e Gestão Funcional';
$tenantId = Tenant::id();
$db = Database::getConnection();

// Processa novo servidor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_employee'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $fullName = trim($_POST['full_name'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $regNumber = trim($_POST['registration_number'] ?? '');
        $empType = trim($_POST['employment_type'] ?? 'Efetivo');
        $jobTitle = trim($_POST['job_title'] ?? '');
        $dept = trim($_POST['department'] ?? 'Secretaria Geral');
        $salary = (float)str_replace(['.', ','], ['', '.'], $_POST['salary'] ?? '0');
        $admDate = $_POST['admission_date'] ?? date('Y-m-d');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!empty($fullName) && !empty($cpf)) {
            $stmt = $db->prepare("
                INSERT INTO employees (tenant_id, full_name, cpf, registration_number, employment_type, job_title, department, salary, admission_date, status, email, phone, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Ativo', ?, ?, NOW())
            ");
            $stmt->execute([$tenantId, $fullName, $cpf, $regNumber, $empType, $jobTitle, $dept, $salary, $admDate, $email, $phone]);
            Audit::log('CRIAR_SERVIDOR', 'employees', (int)$db->lastInsertId(), "Servidor(a) {$fullName} matriculado com matrícula {$regNumber}");
            setFlash('success', "Servidor(a) '{$fullName}' cadastrado(a) com sucesso!");
            header("Location: " . url('modulos/rh.php'));
            exit;
        }
    }
}

// Processa Férias/Afastamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_leave'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $empId = (int)($_POST['employee_id'] ?? 0);
        $leaveType = trim($_POST['leave_type'] ?? 'Férias Regulamentares');
        $startDate = $_POST['start_date'] ?? date('Y-m-d');
        $endDate = $_POST['end_date'] ?? date('Y-m-d');
        $notes = trim($_POST['notes'] ?? '');

        if ($empId) {
            $stmt = $db->prepare("
                INSERT INTO leaves_vacations (employee_id, leave_type, start_date, end_date, status, notes, created_at)
                VALUES (?, ?, ?, ?, 'Aprovado', ?, NOW())
            ");
            $stmt->execute([$empId, $leaveType, $startDate, $endDate, $notes]);
            Audit::log('CONCEDER_FERIAS', 'leaves_vacations', (int)$db->lastInsertId(), "Afastamento/Férias deferido para o servidor ID {$empId}");
            setFlash('success', "Período de {$leaveType} lançado com sucesso!");
            header("Location: " . url('modulos/rh.php#leaves'));
            exit;
        }
    }
}

$employees = $db->query("SELECT * FROM employees WHERE tenant_id = {$tenantId} ORDER BY full_name ASC")->fetchAll();
$leaves = $db->query("
    SELECT lv.*, e.full_name, e.job_title, e.registration_number 
    FROM leaves_vacations lv 
    INNER JOIN employees e ON lv.employee_id = e.id 
    WHERE e.tenant_id = {$tenantId} 
    ORDER BY lv.start_date DESC
")->fetchAll();

$attendances = $db->query("
    SELECT ta.*, e.full_name, e.registration_number, e.department 
    FROM time_attendances ta 
    INNER JOIN employees e ON ta.employee_id = e.id 
    WHERE e.tenant_id = {$tenantId} 
    ORDER BY ta.work_date DESC, ta.clock_in DESC 
    LIMIT 10
")->fetchAll();

$departments = $db->query("SELECT name FROM departments WHERE tenant_id = {$tenantId} ORDER BY name ASC")->fetchAll();

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-sky-700 uppercase tracking-wider">
                    <span>Área 3</span>
                    <span>•</span>
                    <span>Gestão Funcional e Pessoal</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">Recursos Humanos & Quadro de Pessoal</h1>
                <p class="text-xs sm:text-sm text-slate-500">Gestão do corpo funcional, controle de vínculos, concessão de férias e frequência regulamentar.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('new-leave-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Conceder Férias / Afastamento</span>
                </button>
                <button onclick="openModal('new-employee-modal')" class="inline-flex items-center gap-2 px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Admitir Novo Servidor</span>
                </button>
            </div>
        </div>

        <!-- Seção 1: Quadro de Servidores -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-users text-sky-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Servidores e Colaboradores</h2>
                    <span class="text-[11px] text-slate-500">(<?= count($employees) ?> registrados)</span>
                </div>
                <div class="w-full sm:w-64">
                    <input type="text" id="emp-filter" placeholder="Filtrar por nome, matrícula ou cargo..."
                           onkeyup="filterTable('emp-filter', 'emp-table')"
                           class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600" id="emp-table">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Matrícula</th>
                            <th class="p-3">Servidor</th>
                            <th class="p-3">Vínculo</th>
                            <th class="p-3">Cargo / Função</th>
                            <th class="p-3">Lotação</th>
                            <th class="p-3">Remuneração Base</th>
                            <th class="p-3">Admissão</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($employees as $e): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono font-bold text-sky-700"><?= htmlspecialchars($e['registration_number']) ?></td>
                                <td class="p-3">
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($e['full_name']) ?></p>
                                    <p class="text-[11px] text-slate-400 font-mono">CPF: <?= formatCpf($e['cpf']) ?></p>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $e['employment_type'] === 'Efetivo' ? 'bg-indigo-100 text-indigo-800' : ($e['employment_type'] === 'Comissionado' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') ?>">
                                        <?= htmlspecialchars($e['employment_type']) ?>
                                    </span>
                                </td>
                                <td class="p-3 font-medium text-slate-700"><?= htmlspecialchars($e['job_title']) ?></td>
                                <td class="p-3 text-slate-600"><?= htmlspecialchars($e['department']) ?></td>
                                <td class="p-3 font-bold text-slate-900 font-mono"><?= formatCurrency($e['salary']) ?></td>
                                <td class="p-3 font-mono text-slate-500"><?= formatDate($e['admission_date']) ?></td>
                                <td class="p-3">
                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> <?= htmlspecialchars($e['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Seção 2: Férias e Afastamentos & Espelho de Ponto (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="leaves">
            <!-- Coluna 1: Férias e Licenças -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-umbrella-beach text-sky-600"></i>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Férias e Licenças Concedidas</h2>
                    </div>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($leaves as $l): ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900"><?= htmlspecialchars($l['full_name']) ?></span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                    <?= htmlspecialchars($l['status']) ?>
                                </span>
                            </div>
                            <p class="text-[11px] text-sky-700 font-semibold mt-0.5"><?= htmlspecialchars($l['leave_type']) ?></p>
                            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500">
                                <span><i class="fa-regular fa-calendar mr-1"></i> <?= formatDate($l['start_date']) ?> até <?= formatDate($l['end_date']) ?></span>
                                <span class="text-slate-400 font-mono"><?= htmlspecialchars($l['registration_number']) ?></span>
                            </div>
                            <?php if ($l['notes']): ?>
                                <p class="text-[11px] text-slate-600 mt-1 italic"><?= htmlspecialchars($l['notes']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Coluna 2: Espelho de Frequência / Ponto Eletrônico -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-indigo-600"></i>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Registro de Frequência / Ponto</h2>
                    </div>
                    <span class="text-[11px] text-slate-500">Hoje: <?= date('d/m/Y') ?></span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    <?php foreach ($attendances as $att): ?>
                        <div class="p-3.5 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($att['full_name']) ?></span>
                                <span class="font-mono text-[11px] font-bold text-emerald-600"><?= $att['total_hours'] ?>h computadas</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($att['department']) ?></p>
                            <div class="mt-2 grid grid-cols-4 gap-1 text-[10px] text-center font-mono">
                                <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                    <span class="text-slate-400 block">Entrada</span>
                                    <strong><?= $att['clock_in'] ?></strong>
                                </div>
                                <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                    <span class="text-slate-400 block">Almoço Saída</span>
                                    <strong><?= $att['lunch_out'] ?: '-' ?></strong>
                                </div>
                                <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                    <span class="text-slate-400 block">Almoço Volta</span>
                                    <strong><?= $att['lunch_in'] ?: '-' ?></strong>
                                </div>
                                <div class="bg-slate-50 p-1 rounded border border-slate-200">
                                    <span class="text-slate-400 block">Saída</span>
                                    <strong><?= $att['clock_out'] ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Novo Servidor -->
<div id="new-employee-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Admissão de Servidor / Colaborador</h3>
            <button onclick="closeModal('new-employee-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/rh.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_employee" value="1">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block font-medium text-slate-700">Nome Completo</label>
                    <input type="text" name="full_name" required placeholder="Ex: Ana Maria Silveira"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">CPF</label>
                    <input type="text" name="cpf" required placeholder="000.000.000-00"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Número de Matrícula</label>
                    <input type="text" name="registration_number" required placeholder="MAT-1250"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Tipo de Vínculo</label>
                    <select name="employment_type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="Efetivo">Efetivo (Concurso Público)</option>
                        <option value="Comissionado">Comissionado</option>
                        <option value="Estagiário">Estagiário</option>
                        <option value="Cedido">Cedido de outro órgão</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Cargo / Função</label>
                    <input type="text" name="job_title" required placeholder="Ex: Assistente Administrativo"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Lotação / Setor</label>
                    <select name="department" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= htmlspecialchars($d['name']) ?>"><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Salário / Vencimento Base (R$)</label>
                    <input type="text" name="salary" placeholder="4.500,00"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-employee-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Salvar Servidor
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Férias / Afastamento -->
<div id="new-leave-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Conceder Férias ou Afastamento</h3>
            <button onclick="closeModal('new-leave-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/rh.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="new_leave" value="1">

            <div>
                <label class="block font-medium text-slate-700">Servidor</label>
                <select name="employee_id" required class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <?php foreach ($employees as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['full_name']) ?> (<?= htmlspecialchars($e['registration_number']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Tipo de Afastamento</label>
                <select name="leave_type" class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="Férias Regulamentares">Férias Regulamentares</option>
                    <option value="Licença Médica">Licença Médica</option>
                    <option value="Licença Maternidade / Paternidade">Licença Maternidade / Paternidade</option>
                    <option value="Licença Prêmio / Capacitação">Licença Prêmio / Capacitação</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700">Data de Início</label>
                    <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">Data de Retorno</label>
                    <input type="date" name="end_date" required value="<?= date('Y-m-d', strtotime('+15 days')) ?>"
                           class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700">Observações / Processo SEI</label>
                <textarea name="notes" rows="2" placeholder="Portaria nº 48/2026..."
                          class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('new-leave-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg shadow-sm">
                    Lançar Afastamento
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

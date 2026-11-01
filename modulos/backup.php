<?php
/**
 * Câmara Digital - Módulo de Backup e Recuperação de Desastres
 * Conforme Seção 7.6, 9.1 e 15.5 do Planejamento Mestre
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/Backup.php';

Auth::requireRole('admin');
$pageTitle = 'Sistema de Backup & Restauração';
$user = Auth::user();
$tenantId = Tenant::id();

// Processa Ações de Backup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token de autenticação CSRF inválido.');
        header('Location: ' . url('modulos/backup.php'));
        exit;
    }

    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $notes = trim($_POST['notes'] ?? 'Backup manual administrativo');
            $res = Backup::createBackup($notes);
            setFlash('success', "Backup '{$res['filename']}' gerado com sucesso! Tamanho: " . number_format($res['size'] / 1024, 1) . " KB (SHA-256 auditado).");
        } elseif ($action === 'restore') {
            $filename = trim($_POST['filename'] ?? '');
            Backup::restoreBackup($filename);
            setFlash('success', "Banco de dados restaurado com sucesso a partir do arquivo '{$filename}'!");
        } elseif ($action === 'delete') {
            $filename = trim($_POST['filename'] ?? '');
            if (Backup::deleteBackup($filename)) {
                setFlash('success', "Arquivo de backup '{$filename}' removido.");
            } else {
                setFlash('error', "Falha ao remover arquivo de backup.");
            }
        }
    } catch (Exception $e) {
        setFlash('error', $e->getMessage());
    }

    header('Location: ' . url('modulos/backup.php'));
    exit;
}

// Download de Backup
if (isset($_GET['download'])) {
    Backup::downloadBackup($_GET['download']);
}

$backups = Backup::listBackups();
$totalSize = array_sum(array_column($backups, 'size_bytes'));
$lastBackup = !empty($backups) ? $backups[0]['created_at'] : 'Nenhum backup realizado';

require_once __DIR__ . '/../includes/Header.php';
require_once __DIR__ . '/../includes/Sidebar.php';
?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Top Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-rose-700 uppercase tracking-wider">
                    <span>Segurança Operacional</span>
                    <span>•</span>
                    <span>Recuperação de Desastres (DR)</span>
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1 flex items-center gap-2">
                    <i class="fa-solid fa-database text-rose-600"></i>
                    <span>Sistema de Backup & Restauração</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">Geração de cópias de segurança do banco MySQL, verificação de integridade criptográfica SHA-256 e restauração de dados.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('create-backup-modal')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs rounded-xl shadow-md shadow-rose-600/30 transition-all">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    <span>Criar Backup Agora</span>
                </button>
            </div>
        </div>

        <!-- KPI Cards de Backup -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Backups em Armazenamento</span>
                <p class="text-2xl font-bold text-slate-900 mt-2"><?= count($backups) ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Arquivos SQL íntegros</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Espaço Consumido em Disco</span>
                <p class="text-2xl font-bold text-sky-700 font-mono mt-2"><?= number_format($totalSize / 1024, 1) ?> KB</p>
                <span class="text-[11px] text-slate-500 mt-1 block">Diretório isolado e protegido</span>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm sm:col-span-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Último Backup Realizado</span>
                <p class="text-lg font-bold text-emerald-600 font-mono mt-2"><?= formatDate($lastBackup, true) ?></p>
                <span class="text-[11px] text-slate-500 mt-1 block">Restauração testável com 1 clique</span>
            </div>
        </div>

        <!-- Alerta de Segurança e Boas Práticas -->
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-950 text-xs flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5"></i>
            <div class="space-y-1">
                <p class="font-bold">Regra de Segurança e Política de Contingência:</p>
                <p class="text-[11px] text-amber-900 leading-relaxed">
                    A restauração de um backup substitui o estado atual de todas as 34 tabelas do banco de dados pelo conteúdo do arquivo selecionado. Sempre gere um backup de segurança imediatamente antes de executar qualquer restauração ou migração crítica.
                </p>
            </div>
        </div>

        <!-- Tabela de Backups -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-slate-600"></i>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Histórico de Arquivos de Backup (Dumps)</h2>
                </div>
                <span class="text-xs text-slate-500">Diretório: <code>camara_digital/backups/</code></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-3">Arquivo de Backup</th>
                            <th class="p-3">Data e Hora</th>
                            <th class="p-3">Tamanho</th>
                            <th class="p-3">Método</th>
                            <th class="p-3">Checksum SHA-256</th>
                            <th class="p-3">Observações</th>
                            <th class="p-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($backups)): ?>
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    Nenhum backup encontrado. Clique no botão acima para criar o primeiro backup do sistema.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($backups as $b): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                                        <i class="fa-solid fa-file-code text-rose-500 mr-1.5"></i>
                                        <?= htmlspecialchars($b['filename']) ?>
                                    </td>
                                    <td class="p-3 font-mono text-slate-500 whitespace-nowrap"><?= formatDate($b['created_at'], true) ?></td>
                                    <td class="p-3 font-mono font-bold text-slate-700 whitespace-nowrap"><?= $b['size_formatted'] ?></td>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                            <?= htmlspecialchars($b['method']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                        <span title="<?= htmlspecialchars($b['sha256']) ?>" class="cursor-help bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                            <?= substr($b['sha256'], 0, 16) ?>...
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-600"><?= htmlspecialchars($b['notes']) ?></td>
                                    <td class="p-3 text-right whitespace-nowrap space-x-1">
                                        <!-- Download -->
                                        <a href="<?= url('modulos/backup.php?download=' . urlencode($b['filename'])) ?>" 
                                           class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 rounded font-semibold text-[11px] transition-colors inline-flex items-center gap-1" title="Baixar arquivo SQL">
                                            <i class="fa-solid fa-download"></i>
                                            <span>Baixar</span>
                                        </a>

                                        <!-- Restaurar -->
                                        <button onclick="openRestoreModal('<?= htmlspecialchars(addslashes($b['filename'])) ?>')" 
                                                class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded font-semibold text-[11px] transition-colors inline-flex items-center gap-1" title="Restaurar este backup">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                            <span>Restaurar</span>
                                        </button>

                                        <!-- Excluir -->
                                        <button onclick="openDeleteModal('<?= htmlspecialchars(addslashes($b['filename'])) ?>')" 
                                                class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded font-semibold text-[11px] transition-colors" title="Excluir arquivo">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Criar Backup -->
<div id="create-backup-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Gerar Novo Backup do Banco de Dados</h3>
            <button onclick="closeModal('create-backup-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/backup.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="create">

            <div>
                <label class="block font-medium text-slate-700">Descrição / Motivo do Backup</label>
                <input type="text" name="notes" placeholder="Ex: Backup preventivo antes da sessão plenária..."
                       class="mt-1 w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>

            <p class="text-[11px] text-slate-500 leading-relaxed">
                O backup incluirá a estrutura DDL completa e os dados de todas as 34 tabelas do banco <code>camara_digital_db</code> com integridade referencial.
            </p>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('create-backup-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-lg shadow-sm">
                    Iniciar Dump Agora
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Confirmar Restauração -->
<div id="restore-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-rose-100 flex items-center justify-between bg-rose-50">
            <h3 class="text-sm font-bold text-rose-800 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Confirmar Restauração de Dados</span>
            </h3>
            <button onclick="closeModal('restore-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/backup.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="restore">
            <input type="hidden" name="filename" id="restore-filename" value="">

            <p class="text-slate-700 leading-relaxed">
                Você está prestes a restaurar o banco de dados a partir do arquivo:
                <code id="restore-filename-display" class="block font-bold text-rose-700 bg-rose-50 p-2 rounded mt-1 break-all"></code>
            </p>

            <p class="text-rose-600 font-bold text-[11px]">
                Atenção: Os dados atuais serão substituídos integralmente pela cópia contida neste backup. Deseja prosseguir?
            </p>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('restore-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-lg shadow-sm">
                    Sim, Restaurar Banco
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Confirmar Exclusão -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Excluir Arquivo de Backup</h3>
            <button onclick="closeModal('delete-modal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?= url('modulos/backup.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="filename" id="delete-filename" value="">

            <p class="text-slate-700">
                Tem certeza de que deseja remover permanentemente o arquivo:
                <code id="delete-filename-display" class="block font-bold text-slate-900 bg-slate-100 p-2 rounded mt-1 break-all"></code>
            </p>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('delete-modal')" class="px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-lg shadow-sm">
                    Excluir Arquivo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestoreModal(filename) {
    document.getElementById('restore-filename').value = filename;
    document.getElementById('restore-filename-display').textContent = filename;
    openModal('restore-modal');
}

function openDeleteModal(filename) {
    document.getElementById('delete-filename').value = filename;
    document.getElementById('delete-filename-display').textContent = filename;
    openModal('delete-modal');
}
</script>

<?php require_once __DIR__ . '/../includes/Footer.php'; ?>

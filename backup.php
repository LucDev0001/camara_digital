<?php
/**
 * Câmara Digital - Utilitário CLI para Gerenciamento de Backups
 * Execução via Linha de Comando (CLI) para rotinas agendadas (cron / Windows Task Scheduler)
 * 
 * Exemplos de Uso:
 *   php backup.php --create "Backup automatizado diário"
 *   php backup.php --list
 *   php backup.php --restore backup_camara_2026-10-26_21-00-00_a1b2c3.sql
 *   php backup.php --help
 */

if (php_sapi_name() !== 'cli') {
    die("Este script deve ser executado exclusivamente via linha de comando (CLI).\n");
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Backup.php';

function printHeader(): void {
    echo "\n";
    echo "=================================================================\n";
    echo "  CÂMARA DIGITAL - GESTOR DE BACKUP & RECUPERAÇÃO (CLI)\n";
    echo "=================================================================\n\n";
}

function printUsage(): void {
    printHeader();
    echo "Uso:\n";
    echo "  php backup.php --create [\"Observação opcional\"]\n";
    echo "  php backup.php --list\n";
    echo "  php backup.php --restore <nome_do_arquivo.sql>\n";
    echo "  php backup.php --delete <nome_do_arquivo.sql>\n";
    echo "  php backup.php --help\n\n";
}

$argv = $_SERVER['argv'];
$action = $argv[1] ?? '--help';

switch ($action) {
    case '--create':
        printHeader();
        $notes = $argv[2] ?? 'Backup CLI automático';
        echo "[*] Iniciando geração de backup do banco de dados...\n";
        echo "[*] Observação: {$notes}\n";

        try {
            $start = microtime(true);
            $res = Backup::createBackup($notes);
            $elapsed = round(microtime(true) - $start, 2);

            echo "\n[OK] Backup criado com SUCESSO!\n";
            echo "-----------------------------------------------------------------\n";
            echo " Arquivo:   " . $res['filename'] . "\n";
            echo " Tamanho:   " . number_format($res['size'] / 1024, 2) . " KB\n";
            echo " Método:    " . $res['method'] . "\n";
            echo " SHA-256:   " . $res['sha256'] . "\n";
            echo " Duração:   " . $elapsed . " segundos\n";
            echo " Local:     " . $res['filepath'] . "\n";
            echo "-----------------------------------------------------------------\n\n";
            exit(0);
        } catch (Exception $e) {
            echo "\n[ERRO] Falha ao gerar backup: " . $e->getMessage() . "\n\n";
            exit(1);
        }

    case '--list':
        printHeader();
        $backups = Backup::listBackups();
        if (empty($backups)) {
            echo "Nenhum arquivo de backup encontrado no diretório.\n\n";
            exit(0);
        }

        echo sprintf("%-42s | %-19s | %-10s | %-7s\n", "Arquivo", "Data/Hora", "Tamanho", "Método");
        echo str_repeat("-", 85) . "\n";

        foreach ($backups as $b) {
            echo sprintf(
                "%-42s | %-19s | %-10s | %-7s\n",
                $b['filename'],
                $b['created_at'],
                $b['size_formatted'],
                $b['method']
            );
            echo "  └─ SHA-256: " . $b['sha256'] . "\n";
        }
        echo "\nTotal de backups: " . count($backups) . "\n\n";
        exit(0);

    case '--restore':
        printHeader();
        $filename = $argv[2] ?? '';
        if (empty($filename)) {
            echo "[ERRO] Especifique o nome do arquivo a restaurar.\n";
            echo "Exemplo: php backup.php --restore backup_camara_exemplo.sql\n\n";
            exit(1);
        }

        echo "[ATENÇÃO] Esta ação irá SOBRESCREVER todos os dados atuais do banco de dados!\n";
        echo "[*] Restaurando a partir de: {$filename}...\n";

        try {
            $start = microtime(true);
            Backup::restoreBackup($filename);
            $elapsed = round(microtime(true) - $start, 2);

            echo "\n[OK] Banco de dados restaurado com SUCESSO!\n";
            echo "Tempo de restauração: {$elapsed} segundos.\n\n";
            exit(0);
        } catch (Exception $e) {
            echo "\n[ERRO] Falha na restauração: " . $e->getMessage() . "\n\n";
            exit(1);
        }

    case '--delete':
        printHeader();
        $filename = $argv[2] ?? '';
        if (empty($filename)) {
            echo "[ERRO] Especifique o nome do arquivo a excluir.\n\n";
            exit(1);
        }

        if (Backup::deleteBackup($filename)) {
            echo "[OK] Arquivo '{$filename}' removido com sucesso.\n\n";
            exit(0);
        } else {
            echo "[ERRO] Arquivo não encontrado ou não pôde ser removido.\n\n";
            exit(1);
        }

    case '--help':
    default:
        printUsage();
        exit(0);
}

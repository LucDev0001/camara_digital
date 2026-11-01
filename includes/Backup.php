<?php
/**
 * Câmara Digital - Sistema de Backup e Restauração de Banco de Dados
 * Suporta mysqldump e fallback nativo em PHP PDO com integridade SHA-256
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Audit.php';

class Backup {
    private const BACKUP_DIR = __DIR__ . '/../backups';
    private const MYSQLDUMP_PATH = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

    public static function getBackupDir(): string {
        if (!is_dir(self::BACKUP_DIR)) {
            mkdir(self::BACKUP_DIR, 0755, true);
            // Protege contra acesso HTTP direto
            file_put_contents(self::BACKUP_DIR . '/.htaccess', "Deny from all\n");
            file_put_contents(self::BACKUP_DIR . '/index.html', "");
        }
        return self::BACKUP_DIR;
    }

    /**
     * Cria um backup completo do banco de dados MySQL
     */
    public static function createBackup(string $notes = 'Backup manual'): array {
        self::getBackupDir();
        $dateStr = date('Y-m-d_H-i-s');
        $randomHash = substr(bin2hex(random_bytes(4)), 0, 6);
        $filename = "backup_camara_{$dateStr}_{$randomHash}.sql";
        $filepath = self::BACKUP_DIR . '/' . $filename;

        $dbName = 'camara_digital_db';
        $dbHost = '127.0.0.1';
        $dbUser = 'root';
        $dbPass = '';

        $success = false;
        $method = 'mysqldump';

        // 1. Tenta mysqldump se executável existir
        if (file_exists(self::MYSQLDUMP_PATH)) {
            $cmd = sprintf(
                '"%s" --host=%s --user=%s %s --routines --triggers --single-transaction --default-character-set=utf8mb4 %s > "%s"',
                self::MYSQLDUMP_PATH,
                $dbHost,
                $dbUser,
                !empty($dbPass) ? "--password=" . escapeshellarg($dbPass) : "",
                $dbName,
                $filepath
            );

            exec($cmd, $output, $returnVar);
            if ($returnVar === 0 && file_exists($filepath) && filesize($filepath) > 0) {
                $success = true;
            }
        }

        // 2. Fallback: Dumper nativo via PDO PHP caso mysqldump falhe
        if (!$success) {
            $method = 'php_pdo_native';
            $success = self::dumpViaPdo($filepath, $dbName, $notes);
        }

        if ($success) {
            $fileSize = filesize($filepath);
            $sha256 = hash_file('sha256', $filepath);

            // Grava metadados do backup
            $meta = [
                'filename' => $filename,
                'created_at' => date('Y-m-d H:i:s'),
                'size_bytes' => $fileSize,
                'sha256' => $sha256,
                'notes' => $notes,
                'method' => $method
            ];
            file_put_contents(self::BACKUP_DIR . '/' . $filename . '.json', json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            Audit::log('BACKUP_CRIADO', 'backup', null, "Backup criado: {$filename} ({$fileSize} bytes) via {$method}");

            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => $fileSize,
                'sha256' => $sha256,
                'method' => $method
            ];
        }

        throw new Exception("Falha ao gerar o arquivo de backup do banco de dados.");
    }

    /**
     * Dumper nativo em PHP PDO com compatibilidade total
     */
    private static function dumpViaPdo(string $filepath, string $dbName, string $notes): bool {
        $pdo = Database::getConnection();
        $handle = fopen($filepath, 'w');
        if (!$handle) return false;

        fwrite($handle, "-- ==============================================================\n");
        fwrite($handle, "-- CÂMARA DIGITAL - BACKUP AUTOMATIZADO DO BANCO DE DADOS\n");
        fwrite($handle, "-- Data de Geração: " . date('d/m/Y H:i:s') . "\n");
        fwrite($handle, "-- Observação: " . addslashes($notes) . "\n");
        fwrite($handle, "-- ==============================================================\n\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            fwrite($handle, "\n-- Estrutura da tabela: `{$table}`\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
            fwrite($handle, $createTable['Create Table'] . ";\n\n");

            // Exporta registros
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                fwrite($handle, "-- Dados da tabela: `{$table}` (" . count($rows) . " registros)\n");
                $columns = array_keys($rows[0]);
                $colNames = implode('`, `', $columns);

                foreach (array_chunk($rows, 100) as $chunk) {
                    $insertValues = [];
                    foreach ($chunk as $row) {
                        $escapedValues = array_map(function ($val) use ($pdo) {
                            if ($val === null) return "NULL";
                            return $pdo->quote($val);
                        }, array_values($row));
                        $insertValues[] = "(" . implode(', ', $escapedValues) . ")";
                    }
                    fwrite($handle, "INSERT INTO `{$table}` (`{$colNames}`) VALUES \n" . implode(",\n", $insertValues) . ";\n");
                }
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
        fwrite($handle, "-- Fim do Backup\n");
        fclose($handle);

        return true;
    }

    /**
     * Lista todos os backups armazenados
     */
    public static function listBackups(): array {
        self::getBackupDir();
        $files = glob(self::BACKUP_DIR . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $size = filesize($file);
            $mtime = filemtime($file);
            $metaFile = self::BACKUP_DIR . '/' . $filename . '.json';
            $meta = [];

            if (file_exists($metaFile)) {
                $meta = json_decode(file_get_contents($metaFile), true) ?: [];
            }

            $backups[] = [
                'filename' => $filename,
                'path' => $file,
                'size_bytes' => $size,
                'size_formatted' => self::formatBytes($size),
                'created_at' => $meta['created_at'] ?? date('Y-m-d H:i:s', $mtime),
                'sha256' => $meta['sha256'] ?? hash_file('sha256', $file),
                'notes' => $meta['notes'] ?? 'Backup do sistema',
                'method' => $meta['method'] ?? 'manual'
            ];
        }

        // Ordena pelo mais recente
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return $backups;
    }

    /**
     * Restaura o banco de dados a partir de um arquivo .sql
     */
    public static function restoreBackup(string $filename): bool {
        // Validação estrita para evitar directory traversal
        $cleanFilename = basename($filename);
        $filepath = self::BACKUP_DIR . '/' . $cleanFilename;

        if (!file_exists($filepath) || pathinfo($filepath, PATHINFO_EXTENSION) !== 'sql') {
            throw new Exception("Arquivo de backup inválido ou não encontrado: {$cleanFilename}");
        }

        $pdo = Database::getConnection();

        // Desabilita checagens de FK durante restauração
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;");

        // Executa restauração
        $sqlContent = file_get_contents($filepath);
        if (empty($sqlContent)) {
            throw new Exception("O arquivo de backup está vazio.");
        }

        // Limpa tabelas existentes antes
        $existing = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($existing as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `{$table}`;");
        }

        // Executa arquivo SQL
        $pdo->exec($sqlContent);
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        Audit::log('BACKUP_RESTAURADO', 'backup', null, "Banco de dados restaurado a partir de: {$cleanFilename}");
        return true;
    }

    /**
     * Exclui um arquivo de backup
     */
    public static function deleteBackup(string $filename): bool {
        $cleanFilename = basename($filename);
        $filepath = self::BACKUP_DIR . '/' . $cleanFilename;
        $metaFile = self::BACKUP_DIR . '/' . $cleanFilename . '.json';

        if (file_exists($filepath) && pathinfo($filepath, PATHINFO_EXTENSION) === 'sql') {
            unlink($filepath);
            if (file_exists($metaFile)) {
                unlink($metaFile);
            }
            Audit::log('BACKUP_EXCLUIDO', 'backup', null, "Arquivo de backup excluído: {$cleanFilename}");
            return true;
        }
        return false;
    }

    /**
     * Download seguro do arquivo de backup
     */
    public static function downloadBackup(string $filename): void {
        $cleanFilename = basename($filename);
        $filepath = self::BACKUP_DIR . '/' . $cleanFilename;

        if (!file_exists($filepath) || pathinfo($filepath, PATHINFO_EXTENSION) !== 'sql') {
            http_response_code(404);
            die("Arquivo de backup não encontrado.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    private static function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

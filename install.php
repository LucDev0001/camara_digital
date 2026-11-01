<?php
/**
 * Câmara Digital - Instalador e Migrador Automatizado
 */

$isCli = (php_sapi_name() === 'cli');

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbName = 'camara_digital_db';

$errors = [];
$successMessages = [];

try {
    // 1. Conexão no MySQL
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 2. Cria banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$dbName}`;");
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // Limpa tabelas antigas se solicitado ou se CLI
    if ($isCli || isset($_GET['fresh'])) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $existing = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($existing as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `{$table}`;");
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }
    $successMessages[] = "Banco de dados '{$dbName}' conectado com sucesso.";

    // 3. Executa Schema
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $schemaSql = file_get_contents($schemaFile);
        $pdo->exec($schemaSql);
        $successMessages[] = "Esquema relacional de tabelas (schema.sql) aplicado.";
    } else {
        $errors[] = "Arquivo schema.sql não localizado em {$schemaFile}.";
    }

    // 4. Executa Sementes / Seed
    $seedFile = __DIR__ . '/database/seed.sql';
    if (file_exists($seedFile)) {
        $seedSql = file_get_contents($seedFile);
        $pdo->exec($seedSql);
        $successMessages[] = "Dados de demonstração (seed.sql) carregados com sucesso.";
    }

    // 5. Verificação de Integridade
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $mattersCount = $pdo->query("SELECT COUNT(*) FROM legislative_matters")->fetchColumn();

} catch (Exception $e) {
    $errors[] = "Erro durante instalação: " . $e->getMessage();
}

if ($isCli) {
    echo "========================================\n";
    echo "  CÂMARA DIGITAL - INSTALADOR CONCLUÍDO \n";
    echo "========================================\n";
    foreach ($successMessages as $msg) echo "[OK] {$msg}\n";
    foreach ($errors as $err) echo "[ERRO] {$err}\n";
    if (empty($errors)) {
        echo "Tabelas no banco: " . count($tables) . "\n";
        echo "Usuários ativos: {$usersCount}\n";
        echo "Proposições cadastradas: {$mattersCount}\n";
        echo "Instalação concluída com 100% de sucesso!\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Instalação Concluída — Câmara Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-slate-800 rounded-2xl p-8 border border-slate-700 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto text-2xl font-bold border border-emerald-500/30">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="text-2xl font-bold text-white font-heading">Instalação Concluída!</h1>
            <p class="text-xs text-slate-400">Banco de dados MySQL configurado e populado com sucesso.</p>
        </div>

        <?php if (!empty($successMessages)): ?>
            <div class="space-y-2 text-xs">
                <?php foreach ($successMessages as $msg): ?>
                    <div class="p-3 rounded-lg bg-emerald-950/60 border border-emerald-800 text-emerald-300 flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400"></i>
                        <span><?= htmlspecialchars($msg) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="space-y-2 text-xs">
                <?php foreach ($errors as $err): ?>
                    <div class="p-3 rounded-lg bg-rose-950/60 border border-rose-800 text-rose-300 flex items-center gap-2">
                        <i class="fa-solid fa-xmark text-rose-400"></i>
                        <span><?= htmlspecialchars($err) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="pt-4 flex flex-col sm:flex-row gap-3">
            <a href="portal/index.php" class="flex-1 py-2.5 px-4 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-xl text-center shadow-md transition-colors">
                Abrir Portal do Cidadão
            </a>
            <a href="login.php" class="flex-1 py-2.5 px-4 bg-slate-700 hover:bg-slate-600 text-white font-semibold text-xs rounded-xl text-center shadow-md transition-colors">
                Área Restrita (Login)
            </a>
        </div>
    </div>
</body>
</html>

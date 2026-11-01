<?php
/**
 * Câmara Digital - Ponto de Entrada Principal
 * Redireciona para o Portal Público Oficial do Cidadão
 */
require_once __DIR__ . '/config/app.php';

header('Location: ' . url('portal/index.php'));
exit;

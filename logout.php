<?php
/**
 * Câmara Digital - Logout
 */
require_once __DIR__ . '/config/app.php';

Auth::logout();
setFlash('info', 'Você encerrou sua sessão com segurança.');
header('Location: ' . url('login.php'));
exit;

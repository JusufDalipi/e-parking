<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

if (isAjaxRequest()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// Direct browser visit: clear JWT and redirect to login
echo '<!DOCTYPE html><html><head><title>Duke dalë...</title></head><body>';
echo '<script>';
echo 'sessionStorage.removeItem("jwt");';
echo 'localStorage.removeItem("jwt");';
echo 'window.location.replace("/Websherbimeprojekti/login.php");';
echo '</script>';
echo '</body></html>';
exit;

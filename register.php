<?php
declare(strict_types=1);

require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/common.php';

$error = flash('error');
$success = flash('success');
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regjistrohu - E-Parking – Platforma Digjitale e Parkingut</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">
<div class="login-card">
    <div class="login-header">
        <div class="login-icon" style="background: none; box-shadow: none; font-size: 0;">
            <img src="assets/icons/users.svg" alt="User Icon" style="width: 64px; height: 64px; filter: invert(36%) sepia(35%) saturate(1478%) hue-rotate(152deg) brightness(91%) contrast(87%) drop-shadow(0 4px 6px rgba(31, 111, 139, 0.3));">
        </div>
        <h1>Krijo Llogari</h1>
        <p class="subtitle">Regjistrohuni si Shofer i ri</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success" style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px;border-radius:8px;margin-bottom:12px;">
            <?= e($success) ?>
        </div>
    <?php endif; ?>
    
    <form id="registerForm" method="post" action="php/register_process.php" class="grid-form js-validate">
        <div>
            <label>Emri i Plotë</label>
            <input type="text" name="full_name" required placeholder="Shembull: Agim Berisha">
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" required placeholder="Shkruani email-in tuaj...">
        </div>

        <div>
            <label>Fjalëkalimi</label>
            <input type="password" name="password" required minlength="8" placeholder="Të paktën 8 karaktere">
        </div>

        <div>
            <label>Konfirmo Fjalëkalimin</label>
            <input type="password" name="password_confirm" required minlength="8" placeholder="Përsëritni fjalëkalimin">
        </div>

        <button type="submit">Krijo Llogarinë</button>
    </form>
    
    <div style="text-align: center; margin-top: 16px; font-size: 0.9rem;">
        Keni tashmë një llogari? <a href="login.php" data-no-spa="true" style="font-weight: 700; color: var(--primary);">Kyçu këtu</a>
    </div>
</div>
<script src="js/app.js?v=2"></script>
</body>
</html>

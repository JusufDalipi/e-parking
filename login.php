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
    <title>Login - E-Parking – Platforma Digjitale e Parkingut</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body class="login-body">
<div class="login-card">
    <div class="login-header">
        <div class="login-icon" style="background: none; box-shadow: none; font-size: 0;">
            <img src="assets/icons/logo.svg" alt="E-Parking Logo" style="width: 64px; height: 64px; filter: drop-shadow(0 4px 6px rgba(14, 165, 233, 0.3));">
        </div>
        <h1>Mirësevini</h1>
        <p class="subtitle">Sistemi i Menaxhimit të Parkimit</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success" style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px;border-radius:8px;margin-bottom:12px;">
            <?= e($success) ?>
        </div>
    <?php endif; ?>
    
    <form id="loginForm" method="post" action="php/login_process.php" class="grid-form js-validate">
        <div>
            <label>Email</label>
            <input type="email" name="email" required placeholder="Shkruani email-in...">
        </div>

        <div>
            <label>Fjalëkalimi</label>
            <input type="password" name="password" required minlength="8" placeholder="••••••••">
        </div>

        <button type="submit">Kyçu në Sistem</button>
    </form>
    
    <div style="text-align: center; margin-top: 16px; font-size: 0.9rem;">
        Nuk keni një llogari? <a href="register.php" data-no-spa="true" style="font-weight: 700; color: var(--primary);">Regjistrohu këtu</a>
    </div>

    <div style="text-align: center; margin-top: 12px; font-size: 0.85rem; color: var(--muted);">
        💡 Keni harruar fjalëkalimin? <a href="#" onclick="requestPasswordReset(event)" style="font-weight: 700; color: var(--primary); text-decoration: none; cursor: pointer;">Kontaktoni Administratorin</a> për ta rikuperuar.
    </div>

    <script>
    function requestPasswordReset(e) {
        e.preventDefault();
        const email = prompt('Shkruani email-in tuaj për të njoftuar administratorin:');
        if (email && email.trim() !== '') {
            const formData = new FormData();
            formData.append('email', email.trim());
            fetch('php/request_reset.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
            })
            .catch(err => {
                alert('Ndodhi një gabim gjatë dërgimit të kërkesës.');
            });
        }
    }
    </script>

    <p class="hint">Demo: shofer@parking.local / Shofer123!</p>
</div>
<script>
    (function() {
        var token = sessionStorage.getItem('jwt');
        if (token) {
            // Validate the token is still good before redirecting
            fetch('/Websherbimeprojekti/index.php', {
                headers: { 'Authorization': 'Bearer ' + token, 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function(res) {
                if (res.ok) {
                    window.location.replace('/Websherbimeprojekti/index.php');
                } else {
                    sessionStorage.removeItem('jwt');
                }
            }).catch(function() {
                sessionStorage.removeItem('jwt');
            });
        }
    })();
</script>
<script src="js/app.js?v=2"></script>
</body>
</html>

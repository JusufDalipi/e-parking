<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/common.php';
$error = flash('error');
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Parking – Platforma Digjitale e Parkingut</title>
    <link rel="stylesheet" href="/Websherbimeprojekti/css/style.css?v=<?= time() ?>">
</head>
<body>
<header class="topbar">
    <button id="nav-toggle" class="nav-toggle" aria-label="Toggle Navigation">☰</button>
    <a href="/Websherbimeprojekti/index.php" class="brand">E-Parking – Platforma Digjitale e Parkingut</a>
</header>

<aside id="sidebar" class="sidebar">

    <!-- Sidebar Brand Header (click to close) -->
    <div class="sidebar-header" onclick="document.getElementById('sidebar').classList.remove('active');document.getElementById('overlay').classList.remove('active');">
        <div class="sidebar-brand">
            <img src="/Websherbimeprojekti/assets/icons/logo.svg" alt="Logo" style="width: 32px; height: 32px; filter: drop-shadow(0 2px 4px rgba(14, 165, 233, 0.4)); margin-right: 12px; color: var(--primary);">
            <div>
                <div class="sidebar-brand-name">ParkManager</div>
                <div class="sidebar-brand-sub">Sistemi i Parkingut</div>
            </div>
        </div>
        <span class="sidebar-close-hint">✕</span>
    </div>

    <?php if (isLoggedIn()): ?>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="sidebar-avatar" style="padding: 0; background: none; border: none; box-shadow: none;">
            <?php if (checkRole('Admin')): ?>
                <img src="/Websherbimeprojekti/assets/icons/avatar-admin.svg" alt="Admin Avatar" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #a5b4fc; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);">
            <?php elseif (checkRole('Roje')): ?>
                <img src="/Websherbimeprojekti/assets/icons/avatar-guard.svg" alt="Guard Avatar" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fcd34d; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);">
            <?php else: ?>
                <img src="/Websherbimeprojekti/assets/icons/avatar-driver.svg" alt="Driver Avatar" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #6ee7b7; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
            <?php endif; ?>
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name"><?= e(currentUser()['full_name']) ?></div>
            <div class="sidebar-user-role">
                <?php if (checkRole('Admin')): ?>
                    <span class="role-badge role-admin">Admin</span>
                <?php elseif (checkRole('Roje')): ?>
                    <span class="role-badge role-guard">Roje</span>
                <?php else: ?>
                    <span class="role-badge role-driver">Shofer</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="/Websherbimeprojekti/index.php" class="nav-item">
            <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/dashboard.svg" style="width:20px;height:20px;"></span>
            <span class="nav-label">Dashboard</span>
        </a>

        <?php if (checkRole('Admin')): ?>
            <div class="nav-section-label">Administrimi</div>

            <a href="/Websherbimeprojekti/users.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/users.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Përdoruesit</span>
            </a>
            <a href="/Websherbimeprojekti/roles.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/settings.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Rolet</span>
            </a>
            <a href="/Websherbimeprojekti/slots.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/parking.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Vendet</span>
            </a>
            <a href="/Websherbimeprojekti/reservations.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/clipboard.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Rezervimet</span>
            </a>
            <a href="/Websherbimeprojekti/entries.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/car.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Hyrje-Dalje</span>
            </a>
            <a href="/Websherbimeprojekti/payments.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/money.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Pagesat</span>
            </a>
            <a href="/Websherbimeprojekti/subscriptions.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/star.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Abonimet</span>
            </a>
            <a href="/Websherbimeprojekti/login_logs.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/chart.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Login Logs</span>
            </a>

            <div class="nav-section-label">XML</div>
            <a href="/Websherbimeprojekti/xml/export_payments.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/export.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">XML Eksport</span>
            </a>
            <a href="/Websherbimeprojekti/xml/import_slots.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/import.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">XML Import</span>
            </a>

        <?php elseif (checkRole('Roje')): ?>
            <div class="nav-section-label">Paneli</div>
            <a href="/Websherbimeprojekti/slots.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/parking.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Vendet</span>
            </a>
            <a href="/Websherbimeprojekti/reservations.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/clipboard.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Rezervimet</span>
            </a>
            <a href="/Websherbimeprojekti/entries.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/car.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Hyrje-Dalje</span>
            </a>

        <?php elseif (checkRole('Shofer')): ?>
            <div class="nav-section-label">Paneli Im</div>
            <a href="/Websherbimeprojekti/slots.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/parking.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Vendet</span>
            </a>
            <a href="/Websherbimeprojekti/reservations.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/clipboard.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Rezervimet e Mia</span>
            </a>
            <a href="/Websherbimeprojekti/payments.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/credit-card.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Pagesat e Mia</span>
            </a>
            <a href="/Websherbimeprojekti/subscriptions.php" class="nav-item">
                <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/star.svg" style="width:20px;height:20px;"></span>
                <span class="nav-label">Abonimi Im</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Logout at bottom -->
    <div class="sidebar-footer">
        <a href="/Websherbimeprojekti/php/logout.php" class="nav-logout" data-no-spa>
            <span class="nav-icon"><img src="/Websherbimeprojekti/assets/icons/logout.svg" style="width:20px;height:20px;color:#ef4444;"></span>
            <span class="nav-label">Dil nga Sistemi</span>
        </a>
    </div>

    <?php endif; ?>
</aside>

<div id="overlay" class="overlay"></div>

<main class="container">
<?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
<?php endif; ?>

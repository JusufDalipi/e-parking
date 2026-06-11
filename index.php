<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();

$userId = (int) currentUser()['id'];
$isAdmin = checkRole('Admin');
$isGuard = checkRole('Roje');
$isDriver = checkRole('Shofer');

if ($isAdmin) {
    $countUsers = (int) $mysqli->query("SELECT COUNT(*) AS c FROM users WHERE status = 'active'")->fetch_assoc()['c'];
    $countSlots = (int) $mysqli->query("SELECT COUNT(*) AS c FROM parking_slots WHERE status = 'active'")->fetch_assoc()['c'];
    $countReservations = (int) $mysqli->query("SELECT COUNT(*) AS c FROM reservations WHERE status = 'active'")->fetch_assoc()['c'];
    $countPayments = (int) $mysqli->query("SELECT COUNT(*) AS c FROM payments WHERE status = 'active'")->fetch_assoc()['c'];

    $occupiedSlots = (int) $mysqli->query("SELECT COUNT(DISTINCT ve.slot_id) AS c FROM vehicle_entries ve WHERE ve.exit_time IS NULL AND ve.status = 'active'")->fetch_assoc()['c'];
    $freeSlots = $countSlots - $occupiedSlots;

    $todayRevenue = $mysqli->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE DATE(paid_at) = CURDATE() AND status = 'active'")->fetch_assoc()['total'];

    $pendingResetsResult = $mysqli->query("SELECT COUNT(*) AS c, GROUP_CONCAT(email SEPARATOR ', ') as emails FROM password_resets WHERE status = 'pending'")->fetch_assoc();
    $countPendingResets = (int) $pendingResetsResult['c'];
    $pendingResetEmails = $pendingResetsResult['emails'] ?? '';
} elseif ($isGuard) {
    $countUsers = 0;
    $countSlots = (int) $mysqli->query("SELECT COUNT(*) AS c FROM parking_slots WHERE status = 'active'")->fetch_assoc()['c'];
    $occupiedSlots = (int) $mysqli->query("SELECT COUNT(DISTINCT ve.slot_id) AS c FROM vehicle_entries ve WHERE ve.exit_time IS NULL AND ve.status = 'active'")->fetch_assoc()['c'];
    $freeSlots = $countSlots - $occupiedSlots;
    $countReservations = (int) $mysqli->query("SELECT COUNT(*) AS c FROM vehicle_entries WHERE status = 'active' AND exit_time IS NULL")->fetch_assoc()['c'];
    $countPayments = 0;
} elseif ($isDriver) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS c FROM reservations WHERE user_id = ? AND status = 'active'");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $countReservations = (int) $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT COUNT(*) AS c FROM payments WHERE user_id = ? AND status = 'active'");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $countPayments = (int) $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();

    $countUsers = 0;
    $countSlots = 0;
}

$hour = (int) date('H');
if ($hour < 12) {
    $greeting = 'Mirëmëngjesi';
} elseif ($hour < 18) {
    $greeting = 'Mirëdita';
} else {
    $greeting = 'Mirëmbrëma';
}

include __DIR__ . '/php/header.php';
?>

<!-- Dashboard Hero -->
<section class="dash-hero">
    <div class="dash-hero-content">
        <h1 class="dash-greeting"><?= $greeting ?>, <?= e(currentUser()['full_name']) ?> 👋</h1>
        <p class="dash-subtitle">
            <?php if ($isAdmin): ?>
                Paneli i administrimit — kontrollo gjithçka nga një vend.
            <?php elseif ($isGuard): ?>
                Paneli i rojtarit — monitoroni parkingjet në kohë reale.
            <?php else: ?>
                Paneli juaj — menaxhoni rezervimet dhe pagesat.
            <?php endif; ?>
        </p>
        <div class="dash-meta">
            <span>📅 <?= date('d M Y') ?></span>
            <span>🕐 <?= date('H:i') ?></span>
            <span>🔑 <?= e(implode(', ', currentUser()['roles'])) ?></span>
        </div>
    </div>
</section>

<!-- Stats Cards -->
<?php if ($isAdmin && !empty($countPendingResets) && $countPendingResets > 0): ?>
    <section style="margin: 0 1rem 20px 1rem;">
        <div class="alert error" style="background:#fefce8;border:1px solid #fde047;color:#854d0e;padding:15px;border-radius:10px;font-size:1.05rem;box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            ⚠️ <strong>Kujdes:</strong> <?= $countPendingResets ?> përdorues (<?= e($pendingResetEmails) ?>) kanë harruar fjalëkalimin! 
            <a href="/Websherbimeprojekti/users.php" style="color:#a16207;font-weight:700;text-decoration:underline;margin-left:10px;">Shko tek Përdoruesit për t'ia ndryshuar →</a>
        </div>
    </section>
<?php endif; ?>
<section class="stats-grid">
    <?php if ($isAdmin): ?>
        <article class="stat-card stat-users">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/users.svg" alt="Users"></div>
            <div class="stat-info">
                <span class="stat-label">Përdorues Aktivë</span>
                <span class="stat-value" data-count="<?= $countUsers ?>"><?= $countUsers ?></span>
            </div>
            <a href="/Websherbimeprojekti/users.php" class="stat-link">Shiko →</a>
        </article>
        <article class="stat-card stat-slots">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/parking.svg" alt="Parking"></div>
            <div class="stat-info">
                <span class="stat-label">Vende Parkingu</span>
                <span class="stat-value" data-count="<?= $countSlots ?>"><?= $countSlots ?></span>
                <span class="stat-detail">🟢 <?= $freeSlots ?> të lira &nbsp;·&nbsp; 🔴 <?= $occupiedSlots ?> të zëna</span>
            </div>
            <a href="/Websherbimeprojekti/slots.php" class="stat-link">Shiko →</a>
        </article>
        <article class="stat-card stat-reservations">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/clipboard.svg" alt="Reservations"></div>
            <div class="stat-info">
                <span class="stat-label">Rezervime Aktive</span>
                <span class="stat-value" data-count="<?= $countReservations ?>"><?= $countReservations ?></span>
            </div>
            <a href="/Websherbimeprojekti/reservations.php" class="stat-link">Shiko →</a>
        </article>
        <article class="stat-card stat-payments">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/money.svg" alt="Payments"></div>
            <div class="stat-info">
                <span class="stat-label">Pagesa</span>
                <span class="stat-value" data-count="<?= $countPayments ?>"><?= $countPayments ?></span>
                <span class="stat-detail"><img src="/Websherbimeprojekti/assets/icons/chart.svg" style="width:14px;height:14px;vertical-align:middle;filter:opacity(0.7);margin-right:2px;"> Sot: <?= number_format((float) $todayRevenue, 2) ?> €</span>
            </div>
            <a href="/Websherbimeprojekti/payments.php" class="stat-link">Shiko →</a>
        </article>
    <?php elseif ($isGuard): ?>
        <article class="stat-card stat-slots">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/parking.svg" alt="Parking"></div>
            <div class="stat-info">
                <span class="stat-label">Vende të Lira</span>
                <span class="stat-value" data-count="<?= $freeSlots ?>"><?= $freeSlots ?></span>
                <span class="stat-detail">nga <?= $countSlots ?> gjithsej</span>
            </div>
            <a href="/Websherbimeprojekti/slots.php" class="stat-link">Shiko →</a>
        </article>
        <article class="stat-card stat-reservations">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/car.svg" alt="Traffic"></div>
            <div class="stat-info">
                <span class="stat-label">Hyrje Aktive</span>
                <span class="stat-value" data-count="<?= $countReservations ?>"><?= $countReservations ?></span>
            </div>
            <a href="/Websherbimeprojekti/entries.php" class="stat-link">Shiko →</a>
        </article>
    <?php elseif ($isDriver): ?>
        <article class="stat-card stat-reservations">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/clipboard.svg" alt="Reservations"></div>
            <div class="stat-info">
                <span class="stat-label">Rezervimet e Mia</span>
                <span class="stat-value" data-count="<?= $countReservations ?>"><?= $countReservations ?></span>
            </div>
            <a href="/Websherbimeprojekti/reservations.php" class="stat-link">Shiko →</a>
        </article>
        <article class="stat-card stat-payments">
            <div class="stat-icon"><img src="/Websherbimeprojekti/assets/icons/credit-card.svg" alt="Payments"></div>
            <div class="stat-info">
                <span class="stat-label">Pagesat e Mia</span>
                <span class="stat-value" data-count="<?= $countPayments ?>"><?= $countPayments ?></span>
            </div>
            <a href="/Websherbimeprojekti/payments.php" class="stat-link">Shiko →</a>
        </article>
    <?php endif; ?>
</section>

<?php if ($isAdmin): ?>
<!-- Quick Actions -->
<section class="quick-actions">
    <h2 class="section-title">⚡ Veprime të Shpejta</h2>
    <div class="actions-grid">
        <a href="/Websherbimeprojekti/users.php" class="action-btn">
            <span class="action-icon"><img src="/Websherbimeprojekti/assets/icons/users.svg" alt="Users"></span>
            <span>Përdoruesit</span>
        </a>
        <a href="/Websherbimeprojekti/slots.php" class="action-btn">
            <span class="action-icon"><img src="/Websherbimeprojekti/assets/icons/parking.svg" alt="Parking"></span>
            <span>Vendet</span>
        </a>
        <a href="/Websherbimeprojekti/entries.php" class="action-btn">
            <span class="action-icon"><img src="/Websherbimeprojekti/assets/icons/car.svg" alt="Car"></span>
            <span>Hyrje-Dalje</span>
        </a>
        <a href="/Websherbimeprojekti/payments.php" class="action-btn">
            <span class="action-icon"><img src="/Websherbimeprojekti/assets/icons/money.svg" alt="Money"></span>
            <span>Pagesat</span>
        </a>
        <a href="/Websherbimeprojekti/reservations.php" class="action-btn">
            <span class="action-icon"><img src="/Websherbimeprojekti/assets/icons/clipboard.svg" alt="Reservations"></span>
            <span>Rezervimet</span>
        </a>
        <a href="/Websherbimeprojekti/xml/export_payments.php" class="action-btn">
            <span class="action-icon"><img src="/Websherbimeprojekti/assets/icons/export.svg" alt="Export"></span>
            <span>XML Eksport</span>
        </a>
    </div>
</section>
<?php endif; ?>

<script>
// Animated counter
document.querySelectorAll('.stat-value[data-count]').forEach(function(el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (isNaN(target) || target === 0) return;
    var current = 0;
    var duration = 800;
    var step = Math.max(1, Math.floor(target / (duration / 16)));
    var timer = setInterval(function() {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        el.textContent = current;
    }, 16);
});
</script>

<?php include __DIR__ . '/php/footer.php'; ?>

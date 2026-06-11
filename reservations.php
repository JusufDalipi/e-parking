<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin', 'Roje', 'Shofer']);

$userId = (int) currentUser()['id'];
$isAdmin = checkRole('Admin');
$isGuard = checkRole('Roje');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isGuard) {
        denyAccess();
    }
    $id = (int) ($_POST['id'] ?? 0);
    $ownerId = $isAdmin ? (int) ($_POST['user_id'] ?? 0) : $userId;
    $slotId = (int) ($_POST['slot_id'] ?? 0);
    $plate = trim($_POST['vehicle_plate'] ?? '');
    $from = trim($_POST['reserved_from'] ?? '');
    $to = trim($_POST['reserved_to'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    $conflictQuery = 'SELECT COUNT(*) as cnt FROM reservations 
                      WHERE slot_id = ? AND status = ? 
                      AND reserved_from < ? AND reserved_to > ?';
    $params = [$slotId, 'active', $to, $from];
    $types = 'isss';

    if ($id > 0) {
        $conflictQuery .= ' AND id != ?';
        $params[] = $id;
        $types .= 'i';
    }

    $conflict = $mysqli->prepare($conflictQuery);
    $conflict->bind_param($types, ...$params);
    $conflict->execute();
    $conflictRow = $conflict->get_result()->fetch_assoc();
    $conflict->close();

    if ((int) $conflictRow['cnt'] > 0) {
        flash('error', 'Vendi ka tashmë rezervim në këtë periudhë. Zgjedh kohë tjetër ose vend tjetër.');
        header('Location: reservations.php');
        exit;
    }

    if ($id > 0) {
        $stmt = $mysqli->prepare('UPDATE reservations SET user_id = ?, slot_id = ?, vehicle_plate = ?, reserved_from = ?, reserved_to = ?, status = ? WHERE id = ?');
        $stmt->bind_param('iissssi', $ownerId, $slotId, $plate, $from, $to, $status, $id);
    } else {
        $stmt = $mysqli->prepare('INSERT INTO reservations (user_id, slot_id, vehicle_plate, reserved_from, reserved_to, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iissss', $ownerId, $slotId, $plate, $from, $to, $status);
    }
    $stmt->execute();
    $stmt->close();

    header('Location: reservations.php');
    exit;
}

if (isset($_GET['delete'])) {
    if ($isGuard) {
        denyAccess();
    }
    $id = (int) $_GET['delete'];

    if ($isAdmin) {
        $stmt = $mysqli->prepare('DELETE FROM reservations WHERE id = ?');
        $stmt->bind_param('i', $id);
    } else {
        $stmt = $mysqli->prepare('DELETE FROM reservations WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $id, $userId);
    }

    $stmt->execute();
    $stmt->close();
    header('Location: reservations.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    if ($isGuard) {
        denyAccess();
    }
    $id = (int) $_GET['edit'];

    if ($isAdmin) {
        $stmt = $mysqli->prepare('SELECT * FROM reservations WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
    } else {
        $stmt = $mysqli->prepare('SELECT * FROM reservations WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->bind_param('ii', $id, $userId);
    }

    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$users = $mysqli->query("SELECT id, full_name FROM users WHERE status = 'active' ORDER BY full_name");
$slots = $mysqli->query("SELECT id, slot_code FROM parking_slots WHERE status = 'active' ORDER BY slot_code");

if ($isAdmin) {
    $rows = $mysqli->query('SELECT r.*, u.full_name, s.slot_code FROM reservations r JOIN users u ON u.id = r.user_id JOIN parking_slots s ON s.id = r.slot_id ORDER BY r.id DESC');
} elseif ($isGuard) {
    $rows = $mysqli->query('SELECT r.*, u.full_name, s.slot_code FROM reservations r JOIN users u ON u.id = r.user_id JOIN parking_slots s ON s.id = r.slot_id ORDER BY r.id DESC');
} else {
    $stmt = $mysqli->prepare('SELECT r.*, u.full_name, s.slot_code FROM reservations r JOIN users u ON u.id = r.user_id JOIN parking_slots s ON s.id = r.slot_id WHERE r.user_id = ? ORDER BY r.id DESC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $rows = $stmt->get_result();
}

include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/clipboard.svg" alt="Reservations"></span> <?php if ($isGuard): ?>Rezervimet e Shofereve<?php else: ?>Menaxhimi i Rezervimeve<?php endif; ?></h2>
</div>

<?php if (!$isGuard): ?>
<section class="card">
    <form method="post" class="grid-form js-validate">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

        <?php if ($isAdmin): ?>
            <select name="user_id" required>
                <option value="">Zgjidh perdorues</option>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <option value="<?= (int) $u['id'] ?>" <?= ((int) ($edit['user_id'] ?? 0) === (int) $u['id']) ? 'selected' : '' ?>><?= e($u['full_name']) ?></option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>

        <select name="slot_id" required>
            <option value="">Zgjidh vend</option>
            <?php while ($s = $slots->fetch_assoc()): ?>
                <option value="<?= (int) $s['id'] ?>" <?= ((int) ($edit['slot_id'] ?? 0) === (int) $s['id']) ? 'selected' : '' ?>><?= e($s['slot_code']) ?></option>
            <?php endwhile; ?>
        </select>

        <input type="text" name="vehicle_plate" placeholder="Targa" required value="<?= e($edit['vehicle_plate'] ?? '') ?>">
        <?php $fromVal = isset($edit['reserved_from']) ? e(str_replace(' ', 'T', substr($edit['reserved_from'], 0, 16))) : ''; ?>
        <input type="<?= $fromVal ? 'datetime-local' : 'text' ?>" name="reserved_from" required placeholder="Nga" value="<?= $fromVal ?>" onfocus="(this.type='datetime-local')" onblur="if(!this.value) this.type='text'">
        
        <?php $toVal = isset($edit['reserved_to']) ? e(str_replace(' ', 'T', substr($edit['reserved_to'], 0, 16))) : ''; ?>
        <input type="<?= $toVal ? 'datetime-local' : 'text' ?>" name="reserved_to" required placeholder="Deri" value="<?= $toVal ?>" onfocus="(this.type='datetime-local')" onblur="if(!this.value) this.type='text'">
        <select name="status" required>
            <option value="active" <?= (($edit['status'] ?? '') === 'active') ? 'selected' : '' ?>>active</option>
            <option value="cancelled" <?= (($edit['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>cancelled</option>
        </select>
        <button type="submit"><?= $edit ? 'Perditeso' : 'Shto Rezervim' ?></button>
    </form>
</section>
<?php endif; ?>

<section class="card">
    <div class="table-tools">
        <input type="text" placeholder="Filtro rezervimet..." class="js-table-filter" data-target="reservations-table">
    </div>
    <div class="table-responsive">
        <table id="reservations-table">
            <thead><tr><th>ID</th><th>Perdoruesi</th><th>Vendi</th><th>Targa</th><th>Nga</th><th>Deri</th><th>Status</th><th>Veprime</th></tr></thead>
            <tbody>
            <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td><strong><?= e($row['slot_code']) ?></strong></td>
                    <td><span style="background:#e2e8f0;padding:2px 8px;border-radius:4px;font-family:monospace;font-weight:bold;"><?= e($row['vehicle_plate']) ?></span></td>
                    <td><?= e($row['reserved_from']) ?></td>
                    <td><?= e($row['reserved_to']) ?></td>
                    <td><span class="status-badge <?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
                    <td>
                        <?php if ($isAdmin): ?>
                            <div class="action-links">
                                <a href="reservations.php?edit=<?= (int) $row['id'] ?>" class="edit-btn">Edit</a>
                                <a href="reservations.php?delete=<?= (int) $row['id'] ?>" class="delete-btn js-delete">Delete</a>
                            </div>
                        <?php else: ?>
                            <span style="color:var(--muted);font-size:0.85rem;">Vetem shikim</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>

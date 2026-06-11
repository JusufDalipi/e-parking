<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin', 'Roje']);

$isAdmin = checkRole('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $reservationId = $_POST['reservation_id'] !== '' ? (int) $_POST['reservation_id'] : null;
    $userId = (int) ($_POST['user_id'] ?? 0);
    $slotId = (int) ($_POST['slot_id'] ?? 0);
    $plate = trim($_POST['vehicle_plate'] ?? '');
    $entry = trim($_POST['entry_time'] ?? '');
    $exit = trim($_POST['exit_time'] ?? '');
    $exit = $exit !== '' ? $exit : null;
    $status = trim($_POST['status'] ?? 'active');

    if ($id === 0) {
        $conflictQuery = 'SELECT COUNT(*) as cnt FROM vehicle_entries 
                          WHERE slot_id = ? AND status = ? 
                          AND entry_time < ? AND (exit_time IS NULL OR exit_time > ?)';
        $params = [$slotId, 'active', $exit ?? '9999-12-31 23:59:59', $entry];
        $types = 'isss';

        $conflict = $mysqli->prepare($conflictQuery);
        $conflict->bind_param($types, ...$params);
        $conflict->execute();
        $conflictRow = $conflict->get_result()->fetch_assoc();
        $conflict->close();

        if ((int) $conflictRow['cnt'] > 0) {
            flash('error', 'Vendi është i zënë në këtë periudhë. Nuk mund të shtohet hyrje.');
            header('Location: entries.php');
            exit;
        }
    }

    if ($id > 0) {
        $stmt = $mysqli->prepare('UPDATE vehicle_entries SET reservation_id = ?, user_id = ?, slot_id = ?, vehicle_plate = ?, entry_time = ?, exit_time = ?, status = ? WHERE id = ?');
        $stmt->bind_param('iiissssi', $reservationId, $userId, $slotId, $plate, $entry, $exit, $status, $id);
    } else {
        $stmt = $mysqli->prepare('INSERT INTO vehicle_entries (reservation_id, user_id, slot_id, vehicle_plate, entry_time, exit_time, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iiissss', $reservationId, $userId, $slotId, $plate, $entry, $exit, $status);
    }
    $stmt->execute();
    $entryId = $id > 0 ? $id : $stmt->insert_id;
    $stmt->close();

    if ($exit !== null) {
        $payCheck = $mysqli->prepare('SELECT id FROM payments WHERE entry_id = ?');
        $payCheck->bind_param('i', $entryId);
        $payCheck->execute();
        $payExists = $payCheck->get_result()->fetch_assoc();
        $payCheck->close();

        if (!$payExists) {
            $slotQuery = $mysqli->prepare('SELECT slot_type FROM parking_slots WHERE id = ?');
            $slotQuery->bind_param('i', $slotId);
            $slotQuery->execute();
            $slotRow = $slotQuery->get_result()->fetch_assoc();
            $slotQuery->close();
            
            $slotType = strtolower(trim($slotRow['slot_type'] ?? ''));
            
            $entryTime = new DateTime($entry);
            $exitTimeObj = new DateTime($exit);
            if ($exitTimeObj > $entryTime) {
                $interval = $entryTime->diff($exitTimeObj);
                $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                
                $halfHours = ceil($minutes / 30);
                if ($halfHours < 1) $halfHours = 1;
                
                $subCheck = $mysqli->prepare("SELECT id FROM subscriptions WHERE user_id = ? AND status = 'active' LIMIT 1");
                $subCheck->bind_param('i', $userId);
                $subCheck->execute();
                $hasSub = $subCheck->get_result()->fetch_assoc();
                $subCheck->close();
                
                if ($hasSub) {
                    $amount = 0.0;
                } else {
                    if ($slotType === 'vip') {
                        $amount = $halfHours * 5.0;
                    } else {
                        $amount = $halfHours * 0.50;
                    }
                }
                
                $payInsert = $mysqli->prepare('INSERT INTO payments (reservation_id, entry_id, user_id, amount, payment_method, paid_at, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $method = 'cash';
                $paidAt = date('Y-m-d H:i:s');
                $payStatus = 'active';
                $payInsert->bind_param('iiidsss', $reservationId, $entryId, $userId, $amount, $method, $paidAt, $payStatus);
                $payInsert->execute();
                $payInsert->close();
            }
        }
    }

    header('Location: entries.php');
    exit;
}

if (isset($_GET['delete'])) {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) $_GET['delete'];
    $stmt = $mysqli->prepare('DELETE FROM vehicle_entries WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: entries.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $mysqli->prepare('SELECT * FROM vehicle_entries WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$users = $mysqli->query("SELECT u.id, u.full_name, (SELECT COUNT(*) FROM subscriptions s WHERE s.user_id = u.id AND s.status = 'active') as has_sub FROM users u WHERE u.status = 'active' ORDER BY u.full_name");
$slots = $mysqli->query("SELECT id, slot_code FROM parking_slots WHERE status = 'active' ORDER BY slot_code");
$reservations = $mysqli->query("SELECT id FROM reservations WHERE status = 'active' ORDER BY id DESC");
$rows = $mysqli->query("SELECT ve.*, u.full_name, ps.slot_code, (SELECT COUNT(*) FROM subscriptions s WHERE s.user_id = u.id AND s.status = 'active') as has_sub FROM vehicle_entries ve JOIN users u ON u.id = ve.user_id JOIN parking_slots ps ON ps.id = ve.slot_id ORDER BY ve.id DESC");

include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/car.svg" alt="Entries"></span> Hyrje / Dalje Mjetesh</h2>
</div>

<section class="card">
    <form method="post" class="grid-form js-validate">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <select name="reservation_id">
            <option value="">Pa rezervim</option>
            <?php while ($r = $reservations->fetch_assoc()): ?>
                <option value="<?= (int) $r['id'] ?>" <?= ((int) ($edit['reservation_id'] ?? 0) === (int) $r['id']) ? 'selected' : '' ?>>Rezervim #<?= (int) $r['id'] ?></option>
            <?php endwhile; ?>
        </select>
        <select name="user_id" required>
            <option value="">Perdoruesi</option>
            <?php while ($u = $users->fetch_assoc()): ?>
                <option value="<?= (int) $u['id'] ?>" <?= ((int) ($edit['user_id'] ?? 0) === (int) $u['id']) ? 'selected' : '' ?>><?= e($u['full_name']) ?><?= $u['has_sub'] ? ' (⭐ Abonim)' : '' ?></option>
            <?php endwhile; ?>
        </select>
        <select name="slot_id" required>
            <option value="">Vendi</option>
            <?php while ($s = $slots->fetch_assoc()): ?>
                <option value="<?= (int) $s['id'] ?>" <?= ((int) ($edit['slot_id'] ?? 0) === (int) $s['id']) ? 'selected' : '' ?>><?= e($s['slot_code']) ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="vehicle_plate" required placeholder="Targa" value="<?= e($edit['vehicle_plate'] ?? '') ?>">
        <?php $entryVal = isset($edit['entry_time']) ? e(str_replace(' ', 'T', substr($edit['entry_time'], 0, 16))) : ''; ?>
        <input type="<?= $entryVal ? 'datetime-local' : 'text' ?>" name="entry_time" required placeholder="Hyrja" value="<?= $entryVal ?>" onfocus="(this.type='datetime-local')" onblur="if(!this.value) this.type='text'">
        
        <?php $exitVal = isset($edit['exit_time']) && $edit['exit_time'] !== null ? e(str_replace(' ', 'T', substr($edit['exit_time'], 0, 16))) : ''; ?>
        <input type="<?= $exitVal ? 'datetime-local' : 'text' ?>" name="exit_time" placeholder="Dalje" value="<?= $exitVal ?>" onfocus="(this.type='datetime-local')" onblur="if(!this.value) this.type='text'">
        <select name="status" required>
            <option value="active" <?= (($edit['status'] ?? '') === 'active') ? 'selected' : '' ?>>active</option>
            <option value="closed" <?= (($edit['status'] ?? '') === 'closed') ? 'selected' : '' ?>>closed</option>
        </select>
        <button type="submit"><?= $edit ? 'Perditeso' : 'Shto Hyrje' ?></button>
    </form>
</section>

<section class="card">
    <div class="table-tools">
        <input type="text" placeholder="Filtro sipas targës ose përdoruesit..." class="js-table-filter" data-target="entries-table">
    </div>
    <div class="table-responsive">
        <table id="entries-table">
            <thead><tr><th>ID</th><th>Perdoruesi</th><th>Vendi</th><th>Targa</th><th>Hyrja</th><th>Dalja</th><th>Status</th><th>Veprime</th></tr></thead>
            <tbody>
            <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['full_name']) ?> <?= $row['has_sub'] ? '<span style="color:#d97706;font-size:0.8rem;font-weight:bold;">(⭐ Abonim)</span>' : '' ?></td>
                    <td><strong><?= e($row['slot_code']) ?></strong></td>
                    <td><span style="background:#e2e8f0;padding:2px 8px;border-radius:4px;font-family:monospace;font-weight:bold;"><?= e($row['vehicle_plate']) ?></span></td>
                    <td><?= e($row['entry_time']) ?></td>
                    <td><?= e($row['exit_time'] ?? '-') ?></td>
                    <td><span class="status-badge <?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
                    <td>
                        <div class="action-links">
                            <a href="entries.php?edit=<?= (int) $row['id'] ?>" class="edit-btn">Edit</a>
                            <?php if ($isAdmin): ?>
                                <a href="entries.php?delete=<?= (int) $row['id'] ?>" class="delete-btn js-delete">Delete</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>

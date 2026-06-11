<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin', 'Shofer']);

$userId = (int) currentUser()['id'];
$isAdmin = checkRole('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) ($_POST['id'] ?? 0);
    $reservationId = (int) ($_POST['reservation_id'] ?? 0);
    $ownerId = (int) ($_POST['user_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $method = trim($_POST['payment_method'] ?? 'cash');
    $paidAt = trim($_POST['paid_at'] ?? '');
    $reference = trim($_POST['reference_no'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($id > 0) {
        $stmt = $mysqli->prepare('UPDATE payments SET reservation_id = ?, user_id = ?, amount = ?, payment_method = ?, paid_at = ?, reference_no = ?, status = ? WHERE id = ?');
        $stmt->bind_param('iidssssi', $reservationId, $ownerId, $amount, $method, $paidAt, $reference, $status, $id);
    } else {
        $stmt = $mysqli->prepare('INSERT INTO payments (reservation_id, user_id, amount, payment_method, paid_at, reference_no, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iidssss', $reservationId, $ownerId, $amount, $method, $paidAt, $reference, $status);
    }
    $stmt->execute();
    $stmt->close();

    header('Location: payments.php');
    exit;
}

if (isset($_GET['delete'])) {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) $_GET['delete'];
    $stmt = $mysqli->prepare('DELETE FROM payments WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: payments.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) $_GET['edit'];
    $stmt = $mysqli->prepare('SELECT * FROM payments WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($isAdmin) {
    $reservations = $mysqli->query("SELECT id FROM reservations WHERE status = 'active' ORDER BY id DESC");
    $users = $mysqli->query("SELECT id, full_name FROM users WHERE status = 'active' ORDER BY full_name");
    $rows = $mysqli->query('SELECT p.*, u.full_name FROM payments p JOIN users u ON u.id = p.user_id ORDER BY p.id DESC');
} else {
    $rowsStmt = $mysqli->prepare('SELECT p.*, u.full_name FROM payments p JOIN users u ON u.id = p.user_id WHERE p.user_id = ? ORDER BY p.id DESC');
    $rowsStmt->bind_param('i', $userId);
    $rowsStmt->execute();
    $rows = $rowsStmt->get_result();
}

include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/money.svg" alt="Payments"></span> <?php if ($isAdmin): ?>Menaxhimi i Pagesave<?php else: ?>Pagesat e Mia<?php endif; ?></h2>
</div>

<?php if ($isAdmin): ?>
<section class="card">
    <form method="post" class="grid-form js-validate">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <select name="reservation_id" required>
            <option value="">Rezervimi</option>
            <?php while ($r = $reservations->fetch_assoc()): ?>
                <option value="<?= (int) $r['id'] ?>" <?= ((int) ($edit['reservation_id'] ?? 0) === (int) $r['id']) ? 'selected' : '' ?>>Rezervim #<?= (int) $r['id'] ?></option>
            <?php endwhile; ?>
        </select>

        <select name="user_id" required>
            <option value="">Perdoruesi</option>
            <?php while ($u = $users->fetch_assoc()): ?>
                <option value="<?= (int) $u['id'] ?>" <?= ((int) ($edit['user_id'] ?? 0) === (int) $u['id']) ? 'selected' : '' ?>><?= e($u['full_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <input type="number" step="0.01" min="0" name="amount" placeholder="Shuma" required value="<?= e((string) ($edit['amount'] ?? '')) ?>">
        <select name="payment_method" required>
            <option value="cash" <?= (($edit['payment_method'] ?? '') === 'cash') ? 'selected' : '' ?>>cash</option>
            <option value="card" <?= (($edit['payment_method'] ?? '') === 'card') ? 'selected' : '' ?>>card</option>
            <option value="bank" <?= (($edit['payment_method'] ?? '') === 'bank') ? 'selected' : '' ?>>bank</option>
        </select>
        <input type="datetime-local" name="paid_at" required value="<?= isset($edit['paid_at']) ? e(str_replace(' ', 'T', substr($edit['paid_at'], 0, 16))) : '' ?>">
        <input type="text" name="reference_no" placeholder="Reference" value="<?= e($edit['reference_no'] ?? '') ?>">
        <select name="status" required>
            <option value="active" <?= (($edit['status'] ?? '') === 'active') ? 'selected' : '' ?>>active</option>
            <option value="refunded" <?= (($edit['status'] ?? '') === 'refunded') ? 'selected' : '' ?>>refunded</option>
        </select>
        <button type="submit"><?= $edit ? 'Perditeso' : 'Shto Pagese' ?></button>
    </form>
</section>
<?php endif; ?>

<section class="card">
    <div class="table-tools">
        <input type="text" placeholder="Filtro pagesat..." class="js-table-filter" data-target="payments-table">
    </div>
    <div class="table-responsive">
        <table id="payments-table">
            <thead><tr><th>ID</th><th>Perdoruesi</th><th>Rezervimi</th><th>Shuma</th><th>Metoda</th><th>Koha</th><th>Status</th><th>Veprime</th></tr></thead>
            <tbody>
            <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td>#<?= (int) $row['reservation_id'] ?></td>
                    <td><strong><?= number_format((float) $row['amount'], 2) ?> €</strong></td>
                    <td><?= e($row['payment_method']) ?></td>
                    <td><?= e($row['paid_at']) ?></td>
                    <td><span class="status-badge <?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
                    <td>
                        <?php if ($isAdmin): ?>
                            <div class="action-links">
                                <a href="payments.php?edit=<?= (int) $row['id'] ?>" class="edit-btn">Edit</a>
                                <a href="payments.php?delete=<?= (int) $row['id'] ?>" class="delete-btn js-delete">Delete</a>
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

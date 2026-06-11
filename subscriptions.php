<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin', 'Shofer']);

$currentUserId = (int) currentUser()['id'];
$isAdmin = checkRole('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) ($_POST['id'] ?? 0);
    $userId = (int) ($_POST['user_id'] ?? 0);
    $planName = trim($_POST['plan_name'] ?? '');
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $status = trim($_POST['status'] ?? 'active');

    if ($id > 0) {
        $stmt = $mysqli->prepare('UPDATE subscriptions SET user_id = ?, plan_name = ?, start_date = ?, end_date = ?, price = ?, status = ? WHERE id = ?');
        $stmt->bind_param('isssdsi', $userId, $planName, $startDate, $endDate, $price, $status, $id);
    } else {
        $stmt = $mysqli->prepare('INSERT INTO subscriptions (user_id, plan_name, start_date, end_date, price, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssds', $userId, $planName, $startDate, $endDate, $price, $status);
    }
    $stmt->execute();
    $stmt->close();

    header('Location: subscriptions.php');
    exit;
}

if (isset($_GET['delete'])) {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) $_GET['delete'];
    $stmt = $mysqli->prepare('DELETE FROM subscriptions WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: subscriptions.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) $_GET['edit'];
    $stmt = $mysqli->prepare('SELECT * FROM subscriptions WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($isAdmin) {
    $users = $mysqli->query("SELECT id, full_name FROM users WHERE status = 'active' ORDER BY full_name");
    $rows = $mysqli->query('SELECT s.*, u.full_name FROM subscriptions s JOIN users u ON u.id = s.user_id ORDER BY s.id DESC');
} else {
    $rowsStmt = $mysqli->prepare('SELECT s.*, u.full_name FROM subscriptions s JOIN users u ON u.id = s.user_id WHERE s.user_id = ? ORDER BY s.id DESC');
    $rowsStmt->bind_param('i', $currentUserId);
    $rowsStmt->execute();
    $rows = $rowsStmt->get_result();
}


include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/star.svg" alt="Star"></span> <?php if ($isAdmin): ?>Menaxhimi i Abonimeve<?php else: ?>Abonimet e Mia<?php endif; ?></h2>
</div>

<?php if ($isAdmin): ?>
<section class="card">
    <form method="post" class="grid-form js-validate">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <select name="user_id" required>
            <option value="">Perdoruesi</option>
            <?php while ($u = $users->fetch_assoc()): ?>
                <option value="<?= (int) $u['id'] ?>" <?= ((int) ($edit['user_id'] ?? 0) === (int) $u['id']) ? 'selected' : '' ?>><?= e($u['full_name']) ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="plan_name" required placeholder="Plani" value="<?= e($edit['plan_name'] ?? '') ?>">
        <input type="date" name="start_date" required value="<?= e($edit['start_date'] ?? '') ?>">
        <input type="date" name="end_date" required value="<?= e($edit['end_date'] ?? '') ?>">
        <input type="number" step="0.01" min="0" name="price" required placeholder="Cmimi" value="<?= e((string) ($edit['price'] ?? '')) ?>">
        <select name="status" required>
            <option value="active" <?= (($edit['status'] ?? '') === 'active') ? 'selected' : '' ?>>active</option>
            <option value="expired" <?= (($edit['status'] ?? '') === 'expired') ? 'selected' : '' ?>>expired</option>
        </select>
        <button type="submit"><?= $edit ? 'Perditeso' : 'Shto Abonim' ?></button>
    </form>
</section>
<?php endif; ?>

<section class="card">
    <div class="table-tools">
        <input type="text" placeholder="Filtro abonimet..." class="js-table-filter" data-target="subs-table">
    </div>
    <div class="table-responsive">
        <table id="subs-table">
            <thead><tr><th>ID</th><th>Perdoruesi</th><th>Plani</th><th>Fillimi</th><th>Mbarimi</th><th>Cmimi</th><th>Status</th><th>Veprime</th></tr></thead>
            <tbody>
            <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td><strong><?= e($row['plan_name']) ?></strong></td>
                    <td><?= e($row['start_date']) ?></td>
                    <td><?= e($row['end_date']) ?></td>
                    <td><strong><?= number_format((float) $row['price'], 2) ?> €</strong></td>
                    <td><span class="status-badge <?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
                    <td>
                        <?php if ($isAdmin): ?>
                            <div class="action-links">
                                <a href="subscriptions.php?edit=<?= (int) $row['id'] ?>" class="edit-btn">Edit</a>
                                <a href="subscriptions.php?delete=<?= (int) $row['id'] ?>" class="delete-btn js-delete">Delete</a>
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

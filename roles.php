<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($name !== '') {
        if ($id > 0) {
            $stmt = $mysqli->prepare('UPDATE roles SET name = ?, status = ? WHERE id = ?');
            $stmt->bind_param('ssi', $name, $status, $id);
        } else {
            $stmt = $mysqli->prepare('INSERT INTO roles (name, status) VALUES (?, ?)');
            $stmt->bind_param('ss', $name, $status);
        }
        $stmt->execute();
        $stmt->close();
    }

    header('Location: roles.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $mysqli->prepare('UPDATE roles SET status = ? WHERE id = ?');
    $inactive = 'inactive';
    $stmt->bind_param('si', $inactive, $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare('UPDATE user_roles SET status = ? WHERE role_id = ?');
    $stmt->bind_param('si', $inactive, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: roles.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $mysqli->prepare('SELECT * FROM roles WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$rows = $mysqli->query('SELECT * FROM roles ORDER BY id ASC');

include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/settings.svg" alt="Roles"></span> Menaxhimi i Roleve</h2>
</div>

<section class="card">
    <form method="post" class="grid-form js-validate">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <input type="text" name="name" required placeholder="Emri i rolit" value="<?= e($edit['name'] ?? '') ?>">
        <select name="status" required>
            <option value="active" <?= (($edit['status'] ?? '') === 'active') ? 'selected' : '' ?>>active</option>
            <option value="inactive" <?= (($edit['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>inactive</option>
        </select>
        <button type="submit"><?= $edit ? 'Perditeso' : 'Shto Rol' ?></button>
    </form>
</section>

<section class="card">
    <div class="table-responsive">
        <table id="roles-table">
            <thead><tr><th>ID</th><th>Emri</th><th>Status</th><th>Veprime</th></tr></thead>
            <tbody>
            <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><strong><?= e((string) $row['name']) ?></strong></td>
                    <td><span class="status-badge <?= e((string) $row['status']) ?>"><?= e((string) $row['status']) ?></span></td>
                    <td>
                        <div class="action-links">
                            <a href="roles.php?edit=<?= (int) $row['id'] ?>" class="edit-btn">Edit</a>
                            <a href="roles.php?delete=<?= (int) $row['id'] ?>" class="delete-btn js-delete">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>

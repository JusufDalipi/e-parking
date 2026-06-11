<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin']);

if (isset($_POST['reset_password_id']) && isset($_POST['new_password'])) {
    $resetId = (int) $_POST['reset_password_id'];
    $newPass = trim($_POST['new_password']);
    if (strlen($newPass) >= 8) {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->bind_param('si', $hash, $resetId);
        $stmt->execute();
        $stmt->close();

        $statusResolved = 'resolved';
        $statusPending = 'pending';
        $stmt2 = $mysqli->prepare('UPDATE password_resets SET status = ? WHERE email = (SELECT email FROM users WHERE id = ?) AND status = ?');
        $stmt2->bind_param('sis', $statusResolved, $resetId, $statusPending);
        $stmt2->execute();
        $stmt2->close();
        flash('success', 'Fjalëkalimi u ndryshua me sukses!');
    } else {
        flash('error', 'Fjalëkalimi duhet të ketë së paku 8 karaktere.');
    }
    header('Location: users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $status = trim($_POST['status'] ?? 'active');
    $roleId = (int) ($_POST['role_id'] ?? 0);

    if ($fullName !== '' && $email !== '' && $roleId > 0) {
        if ($id > 0) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $mysqli->prepare('UPDATE users SET full_name = ?, email = ?, password_hash = ?, status = ? WHERE id = ?');
                $stmt->bind_param('ssssi', $fullName, $email, $hash, $status, $id);
            } else {
                $stmt = $mysqli->prepare('UPDATE users SET full_name = ?, email = ?, status = ? WHERE id = ?');
                $stmt->bind_param('sssi', $fullName, $email, $status, $id);
            }
            $stmt->execute();
            $stmt->close();

            $inactive = 'inactive';
            $stmt = $mysqli->prepare('UPDATE user_roles SET status = ? WHERE user_id = ?');
            $stmt->bind_param('si', $inactive, $id);
            $stmt->execute();
            $stmt->close();

            $active = 'active';
            $stmt = $mysqli->prepare('INSERT INTO user_roles (user_id, role_id, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)');
            $stmt->bind_param('iis', $id, $roleId, $active);
            $stmt->execute();
            $stmt->close();
        } else {
            if ($password === '') {
                flash('error', 'Password eshte i detyrueshem per user te ri.');
                header('Location: users.php');
                exit;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare('INSERT INTO users (full_name, email, password_hash, status) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $fullName, $email, $hash, $status);
            $stmt->execute();
            $newUserId = (int) $stmt->insert_id;
            $stmt->close();

            $active = 'active';
            $stmt = $mysqli->prepare('INSERT INTO user_roles (user_id, role_id, status) VALUES (?, ?, ?)');
            $stmt->bind_param('iis', $newUserId, $roleId, $active);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: users.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $inactive = 'inactive';

    $stmt = $mysqli->prepare("UPDATE users SET status = ?, email = CONCAT('del_', UNIX_TIMESTAMP(), '_', email) WHERE id = ? AND status != 'inactive'");
    $stmt->bind_param('si', $inactive, $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare('UPDATE user_roles SET status = ? WHERE user_id = ?');
    $stmt->bind_param('si', $inactive, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: users.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $mysqli->prepare(
        'SELECT u.*, ur.role_id
         FROM users u
         LEFT JOIN user_roles ur ON ur.user_id = u.id AND ur.status = ?
         WHERE u.id = ? LIMIT 1'
    );
    $active = 'active';
    $stmt->bind_param('si', $active, $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$roles = $mysqli->query("SELECT id, name FROM roles WHERE status = 'active' ORDER BY name");
$rows = $mysqli->query(
    'SELECT u.id, u.full_name, u.email, u.status, r.name AS role_name
     FROM users u
     LEFT JOIN user_roles ur ON ur.user_id = u.id AND ur.status = "active"
     LEFT JOIN roles r ON r.id = ur.role_id
     ORDER BY u.id DESC'
);

include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/users.svg" alt="Users"></span> Menaxhimi i Përdoruesve</h2>
</div>

<?php if ($success = flash('success')): ?>
    <div class="alert success" style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px;border-radius:8px;margin-bottom:12px;">
        <?= e($success) ?>
    </div>
<?php endif; ?>
<?php if ($error = flash('error')): ?>
    <div class="alert error" style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:12px;">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<section class="card">
    <form method="post" class="grid-form js-validate">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <input type="text" name="full_name" required placeholder="Emri i plote" value="<?= e($edit['full_name'] ?? '') ?>">
        <input type="email" name="email" required placeholder="Email" value="<?= e($edit['email'] ?? '') ?>">
        <input type="password" name="password" <?= $edit ? '' : 'required' ?> minlength="8" placeholder="Password">
        <select name="role_id" required>
            <option value="">Roli</option>
            <?php while ($r = $roles->fetch_assoc()): ?>
                <option value="<?= (int) $r['id'] ?>" <?= ((int) ($edit['role_id'] ?? 0) === (int) $r['id']) ? 'selected' : '' ?>><?= e((string) $r['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <select name="status" required>
            <option value="active" <?= (($edit['status'] ?? '') === 'active') ? 'selected' : '' ?>>Aktiv</option>
            <option value="inactive" <?= (($edit['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Jo Aktiv</option>
        </select>
        <button type="submit"><?= $edit ? 'Perditeso' : 'Shto User' ?></button>
    </form>
</section>

<section class="card">
    <div class="table-tools">
        <input type="text" placeholder="Filtro përdoruesit..." class="js-table-filter" data-target="users-table">
    </div>
    <div class="table-responsive">
        <table id="users-table">
            <thead><tr><th>ID</th><th>Emri</th><th>Email</th><th>Roli</th><th>Status</th><th>Veprime</th></tr></thead>
            <tbody>
            <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><strong><?= e((string) $row['full_name']) ?></strong></td>
                    <td><?= e((string) $row['email']) ?></td>
                    <td><span style="background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:12px;font-size:0.8rem;font-weight:600;"><?= e((string) ($row['role_name'] ?? 'Pa rol')) ?></span></td>
                    <td><span class="status-badge <?= e((string) $row['status']) ?>"><?= $row['status'] === 'active' ? 'Aktiv' : 'Jo Aktiv' ?></span></td>
                    <td>
                        <div class="action-links">
                            <a href="users.php?edit=<?= (int) $row['id'] ?>" class="edit-btn">Edit</a>
                            <button onclick="resetUserPassword(<?= (int)$row['id'] ?>, '<?= e((string)$row['email']) ?>')" class="edit-btn" style="background:#fef08a;color:#854d0e;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:0.82rem;font-weight:600;">🔑 Reset Pass</button>
                            <a href="users.php?delete=<?= (int) $row['id'] ?>" class="delete-btn js-delete">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
function resetUserPassword(id, email) {
    const newPass = prompt('🔑 Zgjidhni fjalëkalimin e ri (min 8 karaktere) për:\n' + email);
    if (newPass) {
        if (newPass.length < 8) {
            alert('Fjalëkalimi duhet të jetë së paku 8 karaktere!');
            return;
        }
        const formData = new FormData();
        formData.append('reset_password_id', id);
        formData.append('new_password', newPass);
        
        loadPage('users.php', { method: 'POST', body: formData });
    }
}
</script>

<?php include __DIR__ . '/php/footer.php'; ?>

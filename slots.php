<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin', 'Roje', 'Shofer']);

$isAdmin = checkRole('Admin');
$isShofer = checkRole('Shofer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) ($_POST['id'] ?? 0);
    $slotCode = trim($_POST['slot_code'] ?? '');
    $floorLevel = trim($_POST['floor_level'] ?? '');
    $slotType = trim($_POST['slot_type'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($id > 0) {
        $stmt = $mysqli->prepare('UPDATE parking_slots SET slot_code = ?, floor_level = ?, slot_type = ?, status = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $slotCode, $floorLevel, $slotType, $status, $id);
    } else {
        $stmt = $mysqli->prepare('INSERT INTO parking_slots (slot_code, floor_level, slot_type, status) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $slotCode, $floorLevel, $slotType, $status);
    }
    try {
        $stmt->execute();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry
            $_SESSION['flash_error'] = "Ky vend parkimi ekziston tashme ne kete kat!";
        } else {
            $_SESSION['flash_error'] = "Gabim gjate ruajtjes se dhenave.";
        }
        $stmt->close();
        header('Location: slots.php');
        exit;
    }

    header('Location: slots.php');
    exit;
}

if (isset($_GET['delete'])) {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) $_GET['delete'];
    $stmt = $mysqli->prepare('UPDATE parking_slots SET status = "deleted", slot_code = CONCAT(slot_code, "_del_", UNIX_TIMESTAMP()) WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: slots.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    if (!$isAdmin) {
        denyAccess();
    }

    $id = (int) $_GET['edit'];
    $stmt = $mysqli->prepare('SELECT * FROM parking_slots WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$rows = $mysqli->query(
    'SELECT ps.*, COUNT(DISTINCT ve.id) as active_entries, COUNT(DISTINCT r.id) as active_reservations
     FROM parking_slots ps
     LEFT JOIN vehicle_entries ve ON ve.slot_id = ps.id AND ve.exit_time IS NULL AND ve.status = "active"
     LEFT JOIN reservations r ON r.slot_id = ps.id AND r.status = "active" AND NOW() BETWEEN r.reserved_from AND r.reserved_to
     WHERE ps.status != "deleted"
     GROUP BY ps.id
     ORDER BY CAST(ps.floor_level AS SIGNED) ASC, ps.floor_level ASC, CAST(ps.slot_code AS UNSIGNED) ASC, ps.slot_code ASC'
);

include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/parking.svg" alt="Parking"></span> Menaxhimi i Vendeve</h2>
</div>

<?php if ($isAdmin): ?>
<section class="card">
    <?php if ($err = flash('error')): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endif; ?>
    <form method="post" class="grid-form js-validate">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <input type="text" name="slot_code" placeholder="Kodi i vendit" required value="<?= e($edit['slot_code'] ?? '') ?>">
        <input type="text" name="floor_level" placeholder="Kati" required value="<?= e($edit['floor_level'] ?? '') ?>">
        <select name="slot_type" required>
            <option value="" disabled <?= empty($edit['slot_type']) ? 'selected' : '' ?>>Zgjidh Tipin (Standard/VIP)</option>
            <option value="standard" <?= (strtolower($edit['slot_type'] ?? '') === 'standard') ? 'selected' : '' ?>>Standard</option>
            <option value="VIP" <?= (strtolower($edit['slot_type'] ?? '') === 'vip') ? 'selected' : '' ?>>VIP</option>
        </select>
        <select name="status" required>
            <option value="active" <?= (($edit['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($edit['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
        </select>
        <button type="submit"><?= $edit ? 'Perditeso' : 'Shto Vend' ?></button>
    </form>
</section>
<?php endif; ?>

<section class="card">
    <div class="table-tools">
        <input type="text" placeholder="Filtro..." class="js-table-filter" data-target="slots-table">
    </div>
    <div id="slots-table">
        <?php 
        $currentFloor = null;
        $isFirst = true;
        while ($row = $rows->fetch_assoc()): 
            if ($currentFloor !== $row['floor_level']):
                if (!$isFirst):
                    echo '</div>'; // close previous grid
                endif;
                $currentFloor = $row['floor_level'];
                $isFirst = false;
        ?>
            <h3 style="margin-top: 24px; margin-bottom: 12px; color: var(--primary-dark); border-bottom: 2px solid var(--primary); padding-bottom: 8px;">Kati <?= e($currentFloor) ?></h3>
            <div class="slots-grid" style="margin-top: 0;">
        <?php endif; ?>
        <?php
            $isOccupied = (int) $row['active_entries'] > 0;
            $isReserved = (int) $row['active_reservations'] > 0;
            $isInactive = $row['status'] === 'inactive';
            
            if ($isInactive) {
                $statusClass = 'available'; // Keeps it green as requested
                $statusText = 'JO AKTIV';
            } elseif ($isOccupied) {
                $statusClass = 'occupied';
                $statusText = 'E ZËNË';
            } elseif ($isReserved) {
                $statusClass = 'occupied';
                $statusText = 'E REZERVUAR';
            } else {
                $statusClass = 'available';
                $statusText = 'E LIRË';
            }
        ?>
            <div class="slot-item <?= $statusClass ?>" data-slot-id="<?= (int)$row['id'] ?>">
                <div class="slot-code"><?= e($row['slot_code']) ?></div>
                <div class="slot-status-text"><?= $statusText ?></div>
                <div style="font-size: 11px; margin-bottom: 8px; opacity: 0.8;">
                    Kati: <?= e($row['floor_level']) ?> | <?= e($row['slot_type']) ?>
                </div>
                <div class="slot-actions">
                    <?php if ($isAdmin): ?>
                        <a href="slots.php?edit=<?= (int) $row['id'] ?>">Edit</a>
                        <a href="slots.php?delete=<?= (int) $row['id'] ?>" class="js-delete">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        <?php if (!$isFirst): ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Real-time background updates
    setInterval(() => {
        const token = sessionStorage.getItem('jwt');
        fetch('php/api_slots.php', {
            headers: {
                'Authorization': token ? 'Bearer ' + token : '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(json => {
            if (json.status === 'success') {
                for (const [id, stateInfo] of Object.entries(json.data)) {
                    const slotEl = document.querySelector(`.slot-item[data-slot-id="${id}"]`);
                    if (slotEl) {
                        if (stateInfo.status === 'occupied') {
                            slotEl.classList.remove('available');
                            slotEl.classList.add('occupied');
                        } else {
                            slotEl.classList.remove('occupied');
                            slotEl.classList.add('available');
                        }
                        slotEl.querySelector('.slot-status-text').textContent = stateInfo.text;
                    }
                }
            }
        })
        .catch(err => console.error("Error polling slots:", err));
    }, 2500); // 2.5 seconds
    </script>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>

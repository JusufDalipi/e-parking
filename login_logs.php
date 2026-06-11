<?php
declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/common.php';

requireLogin();
requireRole(['Admin']);

$rows = $mysqli->query(
    'SELECT ll.id, ll.user_id, u.full_name, ll.ip_address, ll.login_time, ll.success, ll.status
     FROM login_logs ll
     LEFT JOIN users u ON u.id = ll.user_id
     ORDER BY ll.id DESC'
);

include __DIR__ . '/php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/chart.svg" alt="Logs"></span> Login Logs</h2>
</div>

<section class="card">
    <div class="table-tools">
        <input type="text" placeholder="Filtro sipas logut..." class="js-table-filter" data-target="logs-table">
    </div>
    <div class="table-responsive">
        <table id="logs-table">
            <thead>
                <tr><th>ID</th><th>User</th><th>IP</th><th>Koha</th><th>Sukses</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php while ($row = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><strong><?= e(($row['full_name'] ?? 'N/A') . ' (#' . (int) ($row['user_id'] ?? 0) . ')') ?></strong></td>
                    <td><span style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-family:monospace;"><?= e((string) ($row['ip_address'] ?? '-')) ?></span></td>
                    <td><?= e((string) $row['login_time']) ?></td>
                    <td><span class="status-badge <?= ((int) $row['success'] === 1) ? 'active' : 'inactive' ?>"><?= ((int) $row['success'] === 1) ? 'Po' : 'Jo' ?></span></td>
                    <td><span class="status-badge <?= e((string) $row['status']) ?>"><?= e((string) $row['status']) ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/php/footer.php'; ?>

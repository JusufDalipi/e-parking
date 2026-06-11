<?php
declare(strict_types=1);

require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/common.php';

requireLogin();
requireRole(['Admin']);

// Handle download request
if (($_GET['action'] ?? null) === 'download') {
    $result = $mysqli->query(
        'SELECT p.id, p.reservation_id, p.user_id, u.full_name, p.amount, p.payment_method, p.paid_at, p.reference_no, p.status
         FROM payments p
         JOIN users u ON u.id = p.user_id
         ORDER BY p.id DESC'
    );

    $xml = new SimpleXMLElement('<payments_report/>');
    $generated = $xml->addChild('generated_at', date('c'));
    $generated = $generated; // silence static analyzers

    while ($row = $result->fetch_assoc()) {
        $item = $xml->addChild('payment');
        $item->addChild('id', (string) $row['id']);
        $item->addChild('reservation_id', (string) $row['reservation_id']);
        $item->addChild('user_id', (string) $row['user_id']);
        $item->addChild('full_name', htmlspecialchars((string) $row['full_name']));
        $item->addChild('amount', (string) $row['amount']);
        $item->addChild('payment_method', htmlspecialchars((string) $row['payment_method']));
        $item->addChild('paid_at', (string) $row['paid_at']);
        $item->addChild('reference_no', htmlspecialchars((string) ($row['reference_no'] ?? '')));
        $item->addChild('status', htmlspecialchars((string) $row['status']));
    }

    header('Content-Type: application/xml; charset=UTF-8');
    header('Content-Disposition: attachment; filename="payments_' . date('Y-m-d_H-i-s') . '.xml"');
    echo $xml->asXML();
    exit;
}

// Display UI
$result = $mysqli->query(
    'SELECT p.id, p.reservation_id, p.user_id, u.full_name, p.amount, p.payment_method, p.paid_at, p.reference_no, p.status
     FROM payments p
     JOIN users u ON u.id = p.user_id
     ORDER BY p.id DESC'
);

$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}

include __DIR__ . '/../php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/export.svg" alt="Export"></span> Eksporto Pagesat në XML</h2>
</div>

<section class="card">
    <p>Shkarko të gjitha të dhënat e pageses në format XML. Dokumenti përfshin informacionin e plotë të çdo pagese.</p>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;">
        <div style="background: #f0f4f8; padding: 12px; border-radius: 8px;">
            <div style="font-size: 0.9rem; color: #666;">Totali Pagesat</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #1f6f8b;"><?= count($payments) ?></div>
        </div>
        <div style="background: #f0f4f8; padding: 12px; border-radius: 8px;">
            <div style="font-size: 0.9rem; color: #666;">Shuma Totale</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #1f6f8b;">
                <?= number_format(array_sum(array_column($payments, 'amount')), 2) ?> €
            </div>
        </div>
        <div style="background: #f0f4f8; padding: 12px; border-radius: 8px;">
            <div style="font-size: 0.9rem; color: #666;">Data Gjenerimi</div>
            <div style="font-size: 1rem; font-weight: 700; color: #1f6f8b;"><?= date('d.m.Y H:i') ?></div>
        </div>
    </div>

    <button id="btn-download-xml" onclick="downloadXML()" style="display: inline-block; background: #1f6f8b; color: white; padding: 12px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 1rem; margin-bottom: 20px;">
        ⬇️ Shkarko XML
    </button>
</section>

<script>
function downloadXML() {
    const btn = document.getElementById('btn-download-xml');
    btn.disabled = true;
    btn.textContent = '⏳ Duke gjeneruar...';

    const token = sessionStorage.getItem('jwt');
    const headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (token) headers['Authorization'] = 'Bearer ' + token;

    fetch('?action=download', { headers })
        .then(res => {
            if (!res.ok) throw new Error('Gabim gjatë eksportit: ' + res.status);
            return res.blob();
        })
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const now = new Date();
            const ts = now.getFullYear() + '-' +
                String(now.getMonth()+1).padStart(2,'0') + '-' +
                String(now.getDate()).padStart(2,'0') + '_' +
                String(now.getHours()).padStart(2,'0') + '-' +
                String(now.getMinutes()).padStart(2,'0') + '-' +
                String(now.getSeconds()).padStart(2,'0');
            a.href = url;
            a.download = 'payments_' + ts + '.xml';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        })
        .catch(err => {
            alert('❌ ' + err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = '⬇️ Shkarko XML';
        });
}
</script>

<section class="card">
    <h3>Pamja Para Eksportit</h3>
    <?php if (count($payments) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Përdoruesi</th>
                    <th>Shuma</th>
                    <th>Metoda</th>
                    <th>Data</th>
                    <th>Statusi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($payments, 0, 10) as $p): ?>
                    <tr>
                        <td><?= (int) $p['id'] ?></td>
                        <td><?= e($p['full_name']) ?></td>
                        <td><?= number_format((float) $p['amount'], 2) ?> €</td>
                        <td><?= e($p['payment_method']) ?></td>
                        <td><?= (string) $p['paid_at'] ?></td>
                        <td><span style="background: #d4edda; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;"><?= e($p['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (count($payments) > 10): ?>
            <p style="color: #666; font-size: 0.9rem; margin-top: 12px;">+ <?= count($payments) - 10 ?> rekorde të tjera në skedarën e plote XML...</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert">Nuk ka të dhëna pagesat për eksport.</div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../php/footer.php'; ?>

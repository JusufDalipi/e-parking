<?php
declare(strict_types=1);

require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/common.php';

requireLogin();
requireRole(['Admin']);

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xml_file'])) {
    if ($_FILES['xml_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Ngarkimi i file deshtoi.';
    } else {
        $tmpPath = $_FILES['xml_file']['tmp_name'];
        $xml = simplexml_load_file($tmpPath);

        if ($xml === false) {
            $error = 'File XML nuk eshte valid.';
        } else {
            $count = 0;
            foreach ($xml->slot as $slot) {
                $slotCode = trim((string) $slot->slot_code);
                $floorLevel = trim((string) $slot->floor_level);
                $slotType = trim((string) $slot->slot_type);
                $status = trim((string) $slot->status);
                if ($status === '') {
                    $status = 'active';
                }

                if ($slotCode === '' || $floorLevel === '' || $slotType === '') {
                    continue;
                }

                $check = $mysqli->prepare('SELECT id FROM parking_slots WHERE slot_code = ? AND floor_level = ? LIMIT 1');
                $check->bind_param('ss', $slotCode, $floorLevel);
                $check->execute();
                $existing = $check->get_result()->fetch_assoc();
                $check->close();

                if ($existing) {
                    $id = (int) $existing['id'];
                    $update = $mysqli->prepare('UPDATE parking_slots SET floor_level = ?, slot_type = ?, status = ? WHERE id = ?');
                    $update->bind_param('sssi', $floorLevel, $slotType, $status, $id);
                    $update->execute();
                    $update->close();
                } else {
                    $insert = $mysqli->prepare('INSERT INTO parking_slots (slot_code, floor_level, slot_type, status) VALUES (?, ?, ?, ?)');
                    $insert->bind_param('ssss', $slotCode, $floorLevel, $slotType, $status);
                    $insert->execute();
                    $insert->close();
                }

                $count++;
            }

            $message = 'Importimi perfundoi. Rekorde te procesuara: ' . $count;
        }
    }
}

include __DIR__ . '/../php/header.php';
?>
<div class="page-header">
    <h2><span class="icon"><img src="/Websherbimeprojekti/assets/icons/import.svg" alt="Import"></span> Importo Vendet nga XML</h2>
</div>

<section class="card">
    <p>Formati i pritur: slots me elementet slot_code, floor_level, slot_type, status.</p>

    <?php if ($message): ?>
        <div class="alert"><?= e($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="js-validate">
        <input type="file" name="xml_file" accept=".xml" required>
        <button type="submit">Importo XML</button>
    </form>
</section>

<section class="card">
    <h3>Shembull XML</h3>
    <pre>&lt;slots&gt;
    &lt;slot&gt;
        &lt;slot_code&gt;A-101&lt;/slot_code&gt;
        &lt;floor_level&gt;1&lt;/floor_level&gt;
        &lt;slot_type&gt;standard&lt;/slot_type&gt;
        &lt;status&gt;active&lt;/status&gt;
    &lt;/slot&gt;
&lt;/slots&gt;</pre>
</section>
<?php include __DIR__ . '/../php/footer.php'; ?>

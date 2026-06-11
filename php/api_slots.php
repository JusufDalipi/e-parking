<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/common.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$query = 'SELECT ps.id, ps.status as slot_status, COUNT(DISTINCT ve.id) as active_entries, COUNT(DISTINCT r.id) as active_reservations 
          FROM parking_slots ps
          LEFT JOIN vehicle_entries ve ON ve.slot_id = ps.id AND ve.exit_time IS NULL AND ve.status = "active"
          LEFT JOIN reservations r ON r.slot_id = ps.id AND r.status = "active" AND NOW() BETWEEN r.reserved_from AND r.reserved_to
          WHERE ps.status != "deleted"
          GROUP BY ps.id';

$result = $mysqli->query($query);
$slots = [];

while ($row = $result->fetch_assoc()) {
    $isOccupied = (int) $row['active_entries'] > 0;
    $isReserved = (int) $row['active_reservations'] > 0;
    $isInactive = $row['slot_status'] === 'inactive';
    
    if ($isInactive) {
        $slots[$row['id']] = ['status' => 'available', 'text' => 'JO AKTIV'];
    } elseif ($isOccupied) {
        $slots[$row['id']] = ['status' => 'occupied', 'text' => 'E ZËNË'];
    } elseif ($isReserved) {
        $slots[$row['id']] = ['status' => 'occupied', 'text' => 'E REZERVUAR'];
    } else {
        $slots[$row['id']] = ['status' => 'available', 'text' => 'E LIRË'];
    }
}

echo json_encode(['status' => 'success', 'data' => $slots]);
exit;

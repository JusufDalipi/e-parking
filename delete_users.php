<?php
require_once __DIR__ . '/php/config.php';

$ids = [4, 5, 6, 7];

foreach ($ids as $id) {
    // Delete from child tables first
    $mysqli->query("DELETE FROM login_logs WHERE user_id = $id");
    $mysqli->query("DELETE FROM payments WHERE user_id = $id");
    $mysqli->query("DELETE FROM vehicle_entries WHERE user_id = $id");
    $mysqli->query("DELETE FROM reservations WHERE user_id = $id");
    $mysqli->query("DELETE FROM subscriptions WHERE user_id = $id");
    $mysqli->query("DELETE FROM user_roles WHERE user_id = $id");
    
    // Delete user
    $mysqli->query("DELETE FROM users WHERE id = $id");
}

echo "Deleted users 4, 5, 6, 7 successfully.";

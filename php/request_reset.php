<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

// Check if user exists
$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // For security, don't reveal if email exists or not, just return success
    echo json_encode(['status' => 'success', 'message' => 'Administratori u njoftua.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Insert or update request
$status = 'pending';
$stmt = $mysqli->prepare('INSERT INTO password_resets (email, status) VALUES (?, ?)');
$stmt->bind_param('ss', $email, $status);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Administratori u njoftua me sukses!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gabim ne server!']);
}
$stmt->close();

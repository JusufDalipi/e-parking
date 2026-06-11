<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/jwt.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Email dhe password jane te detyrueshme.']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, full_name, email, password_hash, status FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$success = 0;
$userId = null;
$roleError = false;
$jwt = null;

if ($user && $user['status'] === 'active' && password_verify($password, $user['password_hash'])) {
    $userId = (int) $user['id'];

    $roleStmt = $mysqli->prepare(
        'SELECT r.id, r.name
         FROM roles r
         JOIN user_roles ur ON ur.role_id = r.id
         WHERE ur.user_id = ? AND ur.status = ? AND r.status = ?
         ORDER BY r.id ASC'
    );
    $active = 'active';
    $roleStmt->bind_param('iss', $userId, $active, $active);
    $roleStmt->execute();
    $rolesRes = $roleStmt->get_result();

    $roles = [];
    $roleId = null;
    while ($row = $rolesRes->fetch_assoc()) {
        if ($roleId === null) {
            $roleId = (int) $row['id'];
        }
        $roles[] = $row['name'];
    }
    $roleStmt->close();

    if ($roleId === null) {
        $roleError = true;
    } else {
        $userData = [
            'id' => $userId,
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_id' => $roleId,
            'roles' => $roles,
        ];
        
        $jwt = generate_jwt(['user' => $userData]);
        $success = 1;
    }
}

$logStmt = $mysqli->prepare('INSERT INTO login_logs (user_id, ip_address, success, status) VALUES (?, ?, ?, ?)');
$active = 'active';
$logStmt->bind_param('isis', $userId, $ip, $success, $active);
$logStmt->execute();
$logStmt->close();

if ($success === 1) {
    echo json_encode(['status' => 'success', 'token' => $jwt]);
    exit;
}

http_response_code(401);
if ($roleError) {
    echo json_encode(['error' => 'Llogaria nuk ka rol aktiv. Kontakto administratorin.']);
} else {
    echo json_encode(['error' => 'Kredencialet janë të pasakta! Nëse keni harruar fjalëkalimin, klikoni "Kontaktoni Administratorin" më poshtë.']);
}

<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

header('Content-Type: application/json');

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

if ($fullName === '' || $email === '' || $password === '') {
    echo json_encode(['error' => 'Të gjitha fushat janë të detyrueshme.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['error' => 'Fjalëkalimi duhet të jetë të paktën 8 karaktere.']);
    exit;
}

if ($password !== $passwordConfirm) {
    echo json_encode(['error' => 'Fjalëkalimet nuk përputhen.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email-i nuk është i vlefshëm.']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    echo json_encode(['error' => 'Ky email është i regjistruar tashmë. Provoni të kyçeni.']);
    exit;
}
$stmt->close();

$roleName = 'Shofer';
$activeStatus = 'active';
$stmt = $mysqli->prepare('SELECT id FROM roles WHERE name = ? AND status = ? LIMIT 1');
$stmt->bind_param('ss', $roleName, $activeStatus);
$stmt->execute();
$roleResult = $stmt->get_result();
if ($roleResult->num_rows === 0) {
    $stmt->close();
    echo json_encode(['error' => 'Gabim në sistem: Roli Shofer nuk u gjet.']);
    exit;
}
$roleId = (int) $roleResult->fetch_assoc()['id'];
$stmt->close();

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare('INSERT INTO users (full_name, email, password_hash, status) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $fullName, $email, $hash, $activeStatus);
if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(['error' => 'Ndodhi një gabim gjatë regjistrimit.']);
    exit;
}
$newUserId = (int) $stmt->insert_id;
$stmt->close();

$stmt = $mysqli->prepare('INSERT INTO user_roles (user_id, role_id, status) VALUES (?, ?, ?)');
$stmt->bind_param('iis', $newUserId, $roleId, $activeStatus);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = 'Llogaria u krijua me sukses! Tani mund të kyçeni.';
echo json_encode(['status' => 'success']);
exit;

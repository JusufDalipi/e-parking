<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/jwt.php';

function getBearerToken(): ?string {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function currentUser(): ?array
{
    static $userCache = false;
    if ($userCache !== false) {
        return $userCache;
    }

    $token = getBearerToken();
    if ($token) {
        $payload = verify_jwt($token);
        if ($payload && isset($payload['user'])) {
            $userCache = $payload['user'];
            return $userCache;
        }
    }

    $userCache = null;
    return null;
}

function currentRoleId(): ?int
{
    $user = currentUser();
    if (!$user || !isset($user['role_id'])) {
        return null;
    }

    return (int) $user['role_id'];
}

function currentRoleNames(): array
{
    $user = currentUser();
    if (!$user || !isset($user['roles']) || !is_array($user['roles'])) {
        return [];
    }

    return $user['roles'];
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function roleNameToIdMap(): array
{
    return [
        'Admin' => 1,
        'Roje' => 2,
        'Shofer' => 3,
    ];
}

function roleIdToNameMap(): array
{
    return [
        1 => 'Admin',
        2 => 'Roje',
        3 => 'Shofer',
    ];
}

function checkRole(string $requiredRole): bool
{
    $requiredRole = trim($requiredRole);
    if ($requiredRole === '') {
        return false;
    }

    $roleId = currentRoleId();
    $nameToId = roleNameToIdMap();
    if (isset($nameToId[$requiredRole]) && $roleId !== null) {
        return $roleId === $nameToId[$requiredRole];
    }

    return in_array($requiredRole, currentRoleNames(), true);
}

function isAjaxRequest(): bool {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
        || (!empty($_SERVER['HTTP_AUTHORIZATION'])) 
        || (!empty($_SERVER['Authorization']));
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        if (isAjaxRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        } else {
            echo '<!DOCTYPE html><html><head><title>Loading...</title></head><body>';
            echo '<script>';
            echo 'const token = sessionStorage.getItem("jwt");';
            echo 'if (token) {';
            echo '  fetch(window.location.href, {';
            echo '    headers: { "Authorization": "Bearer " + token, "X-Requested-With": "XMLHttpRequest" }';
            echo '  }).then(res => {';
            echo '    if (res.status === 401) { window.location.href = "login.php"; }';
            echo '    else return res.text();';
            echo '  }).then(html => { if(html) { document.open(); document.write(html); document.close(); } });';
            echo '} else { window.location.href = "login.php"; }';
            echo '</script></body></html>';
            exit;
        }
    }
}

function redirectByRole(): void
{
    if (checkRole('Shofer')) {
        header('Location: reservations.php');
        exit;
    }

    header('Location: index.php');
    exit;
}

function denyAccess(): void
{
    if (isAjaxRequest()) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden: Qasje e Ndaluar']);
        exit;
    } else {
        echo '<!DOCTYPE html><html><body><script>alert("Qasje e Ndaluar"); window.location.href="index.php";</script></body></html>';
        exit;
    }
}

function hasRole(array $allowedRoles): bool
{
    if (!isLoggedIn()) {
        return false;
    }

    foreach ($allowedRoles as $role) {
        if (checkRole((string) $role)) {
            return true;
        }
    }

    return false;
}

function requireRole(array $allowedRoles): void
{
    if (!hasRole($allowedRoles)) {
        denyAccess();
    }
}

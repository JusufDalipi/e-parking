<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
    $padding = strlen($data) % 4;
    $padding = $padding > 0 ? 4 - $padding : 0;
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', $padding));
}

function generate_jwt(array $payload): string {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload['exp'] = time() + (86400 * 7); // 7 days expiration

    $base64UrlHeader = base64url_encode($header);
    $base64UrlPayload = base64url_encode(json_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = base64url_encode($signature);

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function verify_jwt(string $jwt): ?array {
    $tokenParts = explode('.', $jwt);
    if (count($tokenParts) !== 3) {
        return null;
    }

    $header = base64url_decode($tokenParts[0]);
    $payload = base64url_decode($tokenParts[1]);
    $signature_provided = $tokenParts[2];

    $signature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], JWT_SECRET, true);
    $base64UrlSignature = base64url_encode($signature);

    if (hash_equals($base64UrlSignature, $signature_provided)) {
        $payloadArray = json_decode($payload, true);
        if (isset($payloadArray['exp']) && $payloadArray['exp'] >= time()) {
            return $payloadArray;
        }
    }
    
    return null;
}

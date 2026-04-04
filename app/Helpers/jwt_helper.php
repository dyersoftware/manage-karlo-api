<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 🔐 Access Token
function generateJWT($user)
{
    $key = getenv('JWT_SECRET');

    $payload = [
        'iss' => 'ci4-api',
        'aud' => 'ci4-users',
        'iat' => time(),
        'exp' => time() + 3600, // 1 hour
        'data' => [
            'id' => $user['id'],
            'email' => $user['email'],
        ]
    ];

    return JWT::encode($payload, $key, 'HS256');
}

// 🔐 Validate JWT
function validateJWT($token)
{
    try {
        $key = getenv('JWT_SECRET');
        return JWT::decode($token, new Key($key, 'HS256'));
    } catch (\Exception $e) {
        return null;
    }
}

// 🔁 Refresh Token Generator
function generateRefreshToken()
{
    return bin2hex(random_bytes(64)); // secure
}

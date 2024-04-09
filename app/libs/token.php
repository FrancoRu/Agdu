<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateToken($user)
{
    try {
        $data = [
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'user' => $user
        ];
        $token = JWT::encode($data, $_ENV['SECRET_KEY'], 'HS256');
        return $token;
    } catch (Exception $e) {
        return false;
    }
}

function validateToken($token)
{
    try {
        $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
        if (password_verify($decoded->user, $_ENV['USERNAME_ROOT_AGDU'])) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Models\UserModel;
use App\Modules\Auth\Models\RefreshTokenModel;


class AuthService
{
    protected $userModel;
    protected $refreshModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->refreshModel = new RefreshTokenModel();
    }

    public function login($data)
    {
        // 🔍 Find user
        $user = $this->userModel
            ->where('email', $data['email'])
            ->first();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return [
                'status' => false,
                'code' => 401,
                'message' => 'Invalid credentials'
            ];
        }

        unset($user['password']);

        // 🔐 Tokens (same as your code)
        $accessToken = generateJWT($user);
        $refreshToken = generateRefreshToken();

        // 🧹 delete old tokens
        $this->refreshModel->where('user_id', $user['id'])->delete();

        // 💾 Save refresh token
        $this->refreshModel->insert([
            'user_id' => $user['id'],
            'refresh_token' => $refreshToken,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'status' => true,
            'code' => 200,
            'message' => 'Login successful',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 3600,
            'user' => $user
        ];
    }

    public function register($data)
    {
        // ✅ Create user
        $userId = $this->userModel->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        if (!$userId) {
            return [
                'status' => false,
                'code' => 500,
                'message' => 'User registration failed'
            ];
        }

        $user = $this->userModel->find($userId);

        // remove password before response
        unset($user['password']);

        // 🔐 Token (same helper you already use)
        $token = generateJWT($user);

        return [
            'status' => true,
            'code' => 200,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ];
    }

    public function logout($data)
    {
        if (!empty($data['refresh_token'])) {
            $this->refreshModel
                ->where('refresh_token', $data['refresh_token'])
                ->delete();
        }

        return [
            'status' => true,
            'code' => 200,
            'message' => 'Logged out successfully'
        ];
    }

    public function refresh($data)
    {
        if (empty($data['refresh_token'])) {
            return [
                'status' => false,
                'code' => 422,
                'message' => 'Refresh token required'
            ];
        }

        $tokenData = $this->refreshModel
            ->where('refresh_token', $data['refresh_token'])
            ->first();

        if (!$tokenData) {
            return [
                'status' => false,
                'code' => 401,
                'message' => 'Invalid refresh token'
            ];
        }

        // ⏰ Check expiry
        if (strtotime($tokenData['expires_at']) < time()) {
            $this->refreshModel->delete($tokenData['id']);

            return [
                'status' => false,
                'code' => 401,
                'message' => 'Refresh token expired'
            ];
        }

        $user = $this->userModel->find($tokenData['user_id']);
        unset($user['password']);

        // 🔐 Generate new access token
        $newAccessToken = generateJWT($user);

        return [
            'status' => true,
            'code' => 200,
            'access_token' => $newAccessToken,
            'expires_in' => 3600
        ];
    }
}

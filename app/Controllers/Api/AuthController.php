<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RefreshTokenModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->refreshModel = new RefreshTokenModel();
    }

    // 🔐 Register
    public function register()
    {
        try {
            $data = $this->request->getJSON(true);

            // ✅ Validation
            $rules = [
                'name'     => 'required|min_length[3]|max_length[100]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => false,
                    'message' => 'Validation errors',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // ✅ Create user
            $userId = $this->userModel->insert([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ]);

            if (!$userId) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => false,
                    'message' => 'User registration failed'
                ]);
            }

            $user = $this->userModel->find($userId);
            $token = generateJWT($user);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'User registered successfully',
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ]);
        }
    }

    // 🔐 Login
      public function login()
    {
        try {
            $data = $this->request->getJSON(true);

            // ✅ Validation
            if (!$this->validate([
                'email' => 'required|valid_email',
                'password' => 'required'
            ])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $user = $this->userModel
                ->where('email', $data['email'])
                ->first();

            if (!$user || !password_verify($data['password'], $user['password'])) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ]);
            }

            unset($user['password']);

            // 🔐 Tokens
            $accessToken = generateJWT($user);
            $refreshToken = generateRefreshToken();

            // 🧹 Optional: delete old tokens for this user
            $this->refreshModel->where('user_id', $user['id'])->delete();

            // 💾 Save refresh token
            $this->refreshModel->insert([
                'user_id' => $user['id'],
                'refresh_token' => $refreshToken,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Login successful',
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 3600,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Server error'
            ]);
        }
    }

    // 🔁 REFRESH TOKEN API
    public function refresh()
    {
        try {
            $data = $this->request->getJSON(true);

            if (empty($data['refresh_token'])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => false,
                    'message' => 'Refresh token required'
                ]);
            }

            $tokenData = $this->refreshModel
                ->where('refresh_token', $data['refresh_token'])
                ->first();

            if (!$tokenData) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => false,
                    'message' => 'Invalid refresh token'
                ]);
            }

            // ⏰ Check expiry
            if (strtotime($tokenData['expires_at']) < time()) {
                // delete expired token
                $this->refreshModel->delete($tokenData['id']);

                return $this->response->setStatusCode(401)->setJSON([
                    'status' => false,
                    'message' => 'Refresh token expired'
                ]);
            }

            $user = $this->userModel->find($tokenData['user_id']);
            unset($user['password']);

            // 🔐 New Access Token
            $newAccessToken = generateJWT($user);

            return $this->response->setJSON([
                'status' => true,
                'access_token' => $newAccessToken,
                'expires_in' => 3600
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Server error'
            ]);
        }
    }

    // 🚪 LOGOUT (delete refresh token)
    public function logout()
    {
        $data = $this->request->getJSON(true);

        if (!empty($data['refresh_token'])) {
            $this->refreshModel
                ->where('refresh_token', $data['refresh_token'])
                ->delete();
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
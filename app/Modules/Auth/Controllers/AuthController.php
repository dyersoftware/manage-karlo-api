<?php

namespace App\Modules\Auth\Controllers;

use App\Controllers\BaseController;
use App\Modules\Auth\Services\AuthService;

class AuthController extends BaseController
{


    protected $service;

    public function __construct()
    {

        $this->service = new AuthService();
    }

    // 🔐 Register
    public function register()
    {
        try {
            $data = $this->request->getJSON(true);

            // ✅ Validation stays here
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

            $result = $this->service->register($data);

            return $this->response
                ->setStatusCode($result['code'])
                ->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    // 🔐 Login
    public function login()
    {
        try {
            $data = $this->request->getJSON(true);

            // ✅ Validation stays in controller (best practice)
            if (!$this->validate([
                'email' => 'required|valid_email',
                'password' => 'required'
            ])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }

            $result = $this->service->login($data);

            return $this->response
                ->setStatusCode($result['code'])
                ->setJSON($result);
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
        try {
            $data = $this->request->getJSON(true);

            $result = $this->service->logout($data);

            return $this->response
                ->setStatusCode($result['code'])
                ->setJSON($result);
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

            $result = $this->service->refresh($data);

            return $this->response
                ->setStatusCode($result['code'])
                ->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Server error'
            ]);
        }
    }
}

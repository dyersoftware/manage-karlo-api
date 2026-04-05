<?php

namespace App\Modules\Customers\Controllers;

use App\Controllers\BaseController;
use App\Modules\Customers\Services\CustomersService;

class CustomersController extends BaseController
{

    protected $service;

    public function __construct()
    {
        $this->service = new CustomersService();
    }


    // 📄 Get all customers
    public function index()
    {
        try {




            $data = $this->service->getAll();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Customers fetched successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }
    // 📄 Get single customer
    public function show($id)
    {
        try {
            $result = $this->service->getById($id);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])->setJSON([
                    'status' => false,
                    'message' => $result['error']
                ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Customer fetched successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }

    // ➕ Create customer
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            $rules = [
                'name'  => 'required|min_length[2]|max_length[100]',
                'email' => 'required|valid_email|is_unique[customers.email]',
                'phone' => 'permit_empty|min_length[10]|max_length[15]',
            ];

            if (!$this->validate($rules)) {
                return $this->failValidation($this->validator->getErrors());
            }

            $this->service->create($data);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Customer created successfully'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }

    // ✏️ Update customer
    public function update($id)
    {
        try {
            $data = $this->request->getJSON(true);

            $result = $this->service->update($id, $data);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])->setJSON([
                    'status' => false,
                    'message' => $result['error']
                ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Customer updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }

    // ❌ Delete customer
    public function delete($id)
    {
        try {
            $result = $this->service->delete($id);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])->setJSON([
                    'status' => false,
                    'message' => $result['error']
                ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($e);
        }
    }

    // ==============================
    // 🔥 COMMON RESPONSE METHODS
    // ==============================

    private function failValidation($errors)
    {
        return $this->response->setStatusCode(422)->setJSON([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $errors
        ]);
    }



    private function serverError($e)
    {
        return $this->response->setStatusCode(500)->setJSON([
            'status' => false,
            'message' => 'Internal server error',
            'error' => $e->getMessage() // ❌ remove in production
        ]);
    }
}

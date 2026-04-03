<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CustomerModel;

class CustomerController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CustomerModel();
    }

    // 📄 Get all customers
    public function index()
    {
        try {
            $data = $this->model->findAll();

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
            if (!is_numeric($id)) {
                return $this->failValidation('Invalid customer ID');
            }

            $customer = $this->model->find($id);

            if (!$customer) {
                return $this->failNotFound('Customer not found');
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Customer fetched successfully',
                'data' => $customer
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

            // ✅ Validation
            $rules = [
                'name'  => 'required|min_length[2]|max_length[100]',
                'email' => 'required|valid_email|is_unique[customers.email]',
                'phone' => 'permit_empty|min_length[10]|max_length[15]',
            ];

            if (!$this->validate($rules)) {
                return $this->failValidation($this->validator->getErrors());
            }

            $this->model->insert($data);

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
            if (!is_numeric($id)) {
                return $this->failValidation('Invalid customer ID');
            }

            $customer = $this->model->find($id);

            if (!$customer) {
                return $this->failNotFound('Customer not found');
            }

            $data = $this->request->getJSON(true);

            if (empty($data)) {
                return $this->failValidation('No data provided for update');
            }

            $this->model->update($id, $data);

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
            if (!is_numeric($id)) {
                return $this->failValidation('Invalid customer ID');
            }

            $customer = $this->model->find($id);

            if (!$customer) {
                return $this->failNotFound('Customer not found');
            }

            $this->model->delete($id);

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

    private function failNotFound($message)
    {
        return $this->response->setStatusCode(404)->setJSON([
            'status' => false,
            'message' => $message
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

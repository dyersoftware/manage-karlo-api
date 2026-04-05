<?php

namespace App\Modules\Customers\Services;

use App\Modules\Customers\Models\CustomerModel;

class CustomersService
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new CustomerModel();
        $this->validation = service('validation');
    }

    // 📄 Get all customers
    public function getAll()
    {
        $user = service('request')->user;
        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }
        return $this->model
            ->where('admin_user_id', $user->id)
            ->findAll();
    }

    // 📄 Get single customer
    public function getById($id)
    {
        $user = service('request')->user;
        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (!is_numeric($id)) {
            return ['error' => 'Invalid customer ID', 'code' => 422];
        }

        $customer = $this->model->find($id);

        if (!$customer || $customer['admin_user_id'] != $user->id) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        return $customer;
    }

    // ➕ Create customer
    public function create($data)
    {
        $user = service('request')->user;
        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (empty($data)) {
            return ['error' => 'No data provided', 'code' => 422];
        }

        // Basic validation (adjust rules as needed)
        $rules = [
            'name' => 'required|min_length[2]',
            'email' => 'required|valid_email',
        ];
        if (!$this->validation->setRules($rules)->run($data)) {
            return ['error' => $this->validation->getErrors(), 'code' => 422];
        }

        $data['admin_user_id'] = $user->id;
        try {
            $id = $this->model->insert($data);
            return ['success' => true, 'id' => $id];
        } catch (\Exception $e) {
            log_message('error', 'Customer creation failed: ' . $e->getMessage());
            return ['error' => 'Failed to create customer', 'code' => 500];
        }
    }

    // ✏️ Update customer
    public function update($id, $data)
    {
        $user = service('request')->user;
        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (!is_numeric($id)) {
            return ['error' => 'Invalid customer ID', 'code' => 422];
        }

        $customer = $this->model->find($id);

        if (!$customer || $customer['admin_user_id'] != $user->id) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        if (empty($data)) {
            return ['error' => 'No data provided for update', 'code' => 422];
        }

        // Basic validation (adjust rules as needed)
        $rules = [
            'name' => 'min_length[2]',
            'email' => 'valid_email',
        ];
        if (!$this->validation->setRules($rules)->run($data)) {
            return ['error' => $this->validation->getErrors(), 'code' => 422];
        }

        try {
            $this->model->update($id, $data);
            return ['success' => true];
        } catch (\Exception $e) {
            log_message('error', 'Customer update failed: ' . $e->getMessage());
            return ['error' => 'Failed to update customer', 'code' => 500];
        }
    }

    // ❌ Delete customer
    public function delete($id)
    {
        $user = service('request')->user;
        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (!is_numeric($id)) {
            return ['error' => 'Invalid customer ID', 'code' => 422];
        }

        $customer = $this->model->find($id);

        if (!$customer || $customer['admin_user_id'] != $user->id) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        try {
            $this->model->delete($id);
            return ['success' => true];
        } catch (\Exception $e) {
            log_message('error', 'Customer deletion failed: ' . $e->getMessage());
            return ['error' => 'Failed to delete customer', 'code' => 500];
        }
    }
}

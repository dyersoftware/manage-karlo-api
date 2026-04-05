<?php

namespace App\Modules\Customers\Services;

use App\Modules\Customers\Models\CustomerModel;

class CustomersService
{
    protected $model;

    public function __construct()
    {
        $this->model = new CustomerModel();
    }

    // 📄 Get all customers
    public function getAll()
    {
        return $this->model->findAll();
    }

    // 📄 Get single customer
    public function getById($id)
    {
        if (!is_numeric($id)) {
            return ['error' => 'Invalid customer ID', 'code' => 422];
        }

        $customer = $this->model->find($id);

        if (!$customer) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        return $customer;
    }

    // ➕ Create customer
    public function create($data)
    {
        $this->model->insert($data);
        return true;
    }

    // ✏️ Update customer
    public function update($id, $data)
    {
        if (!is_numeric($id)) {
            return ['error' => 'Invalid customer ID', 'code' => 422];
        }

        $customer = $this->model->find($id);

        if (!$customer) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        if (empty($data)) {
            return ['error' => 'No data provided for update', 'code' => 422];
        }

        $this->model->update($id, $data);

        return true;
    }

    // ❌ Delete customer
    public function delete($id)
    {
        if (!is_numeric($id)) {
            return ['error' => 'Invalid customer ID', 'code' => 422];
        }

        $customer = $this->model->find($id);

        if (!$customer) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        $this->model->delete($id);

        return true;
    }
}

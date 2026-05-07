<?php

namespace App\Modules\Customers\Services;

use App\Modules\Customers\Models\CustomerModel;
use App\Modules\Customers\Models\UserCustomerModel;

class CustomersService
{
    protected $customerModel;
    protected $usersCustomersModel;
    protected $validation;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->usersCustomersModel = new UserCustomerModel();
        $this->validation = service('validation');
    }

    // 📄 Get all customers
    public function getAll()
    {
        $user = service('request')->user;
        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }
        return $this->customerModel
            ->where('user_id', $user->id)
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

        $customer = $this->customerModel->find($id);

        if (!$customer || $customer['user_id'] != $user->id) {
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

        $data['user_id'] = $user->id;
        try {
            $id = $this->customerModel->insert($data);
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

        $customer = $this->customerModel->find($id);

        if (!$customer || $customer['user_id'] != $user->id) {
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
            $this->customerModel->update($id, $data);
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

        $customer = $this->customerModel->find($id);

        if (!$customer || $customer['user_id'] != $user->id) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        try {
            $this->customerModel->delete($id);
            return ['success' => true];
        } catch (\Exception $e) {
            log_message('error', 'Customer deletion failed: ' . $e->getMessage());
            return ['error' => 'Failed to delete customer', 'code' => 500];
        }
    }

    public function assignCustomerToUser($customerId)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (empty($customerId)) {
            return ['error' => 'customer_id required', 'code' => 422];
        }



        try {
            // check duplicate
            $exists = $this->usersCustomersModel->where([
                'user_id' => $user->id,
                'customer_id' => $customerId
            ])->countAllResults();

            if ($exists) {
                return ['error' => 'Already assigned', 'code' => 409];
            }

            $result = $this->getById($customerId);

            if (isset($result['error'])) {
                return [
                    'error' => $result['error'],
                    'code' => $result['code']
                ];
            }

            $this->usersCustomersModel->table('users_customers')->insert([
                'user_id' => $user->id,
                'customer_id' => $customerId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return ['success' => true];
        } catch (\Exception $e) {
            log_message('error', 'Assign failed: ' . $e->getMessage());
            return ['error' => 'Failed to assign customer', 'code' => 500];
        }
    }

    public function unassignCustomerFromUser($customerId)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (empty($customerId)) {
            return ['error' => 'customer_id required', 'code' => 422];
        }

        try {
            // check if relation exists
            $exists = $this->usersCustomersModel->where([
                'user_id' => $user->id,
                'customer_id' => $customerId
            ])->first();

            if (!$exists) {
                return ['error' => 'Assignment not found', 'code' => 404];
            }

            // delete relation
            $this->usersCustomersModel->where([
                'user_id' => $user->id,
                'customer_id' => $customerId
            ])->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            log_message('error', 'Unassign failed: ' . $e->getMessage());
            return ['error' => 'Failed to unassign customer', 'code' => 500];
        }
    }
    public function getAssignedCustomers()
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        return $this->customerModel
            ->select('customers.*')
            ->join('users_customers', 'users_customers.customer_id = customers.id')
            ->where('users_customers.user_id', $user->id)
            ->findAll();
    }


    public function createAndAssign($data)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (empty($data)) {
            return ['error' => 'No data provided', 'code' => 422];
        }


        try {
            // 1️⃣ Create Customer
            $data['user_id'] = $user->id;

            $customerId = $this->customerModel->insert($data);

            if (!$customerId) {
                throw new \Exception('Customer creation failed');
            }

            // 2️⃣ Check already assigned (optional safety)
            $exists = $this->usersCustomersModel->where([
                'user_id' => $user->id,
                'customer_id' => $customerId
            ])->countAllResults();

            if (!$exists) {
                // 3️⃣ Assign
                $this->usersCustomersModel->insert([
                    'user_id' => $user->id,
                    'customer_id' => $customerId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }


            return [
                'success' => true,
                'id' => $customerId
            ];
        } catch (\Exception $e) {


            log_message('error', 'Create & Assign failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to create & assign customer',
                'code' => 500
            ];
        }
    }

    // 📄 Get single getByMobileNumber
    public function getByMobileNumber($mobileNumber)
    {
        $user = service('request')->user;
        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (!is_numeric($mobileNumber)) {
            return ['error' => 'Invalid mobile number', 'code' => 422];
        }

        $customer = $this->customerModel->where('phone', $mobileNumber)->first();

        if (!$customer || $customer['user_id'] != $user->id) {
            return ['error' => 'Customer not found', 'code' => 404];
        }

        return $customer;
    }
}

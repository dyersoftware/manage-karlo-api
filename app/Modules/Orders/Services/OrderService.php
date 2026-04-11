<?php

namespace App\Modules\Orders\Services;

use App\Modules\Orders\Models\OrderModel;

class OrderService
{
    protected $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    // ✅ Create Order (user_id from JWT)
    public function create(array $data)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (empty($data)) {
            return ['error' => 'No data provided', 'code' => 422];
        }

        // ✅ Validation
        $rules = [
            'customer_id'  => 'required|integer',
            'total_amount' => 'required|decimal',
            'status'       => 'permit_empty|in_list[pending,processing,completed,cancelled]',
            'notes'        => 'permit_empty|string'
        ];

        $validation = service('validation');


        if (!$validation->setRules($rules)->run($data)) {
            return [
                'error' => $validation->getErrors(),
                'code'  => 422
            ];
        }

        // ✅ Inject user_id from JWT
        $data['user_id'] = $user->id;

        // ✅ Generate order number
        $data['order_number'] = $this->generateOrderNumber();

        try {
            $id = $this->orderModel->insert($data);

            return [
                'success' => true,
                'data' => $this->orderModel->find($id)
            ];
        } catch (\Exception $e) {
            log_message('error', 'Order create failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to create order',
                'code'  => 500
            ];
        }
    }

    // ✅ Get All Orders (by user + optional customer_id)
    public function getAll($customerId = null)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $builder = $this->orderModel
            ->select('orders.*, customers.name as customer_name')
            ->join('customers', 'customers.id = orders.customer_id', 'left')
            ->where('orders.user_id', $user->id)
            ->orderBy('orders.id', 'DESC');

        // ✅ optional filter
        if (!empty($customerId)) {
            $builder->where('orders.customer_id', $customerId);
        }

        return [
            'success' => true,
            'data' => $builder->findAll()
        ];
    }

    // ✅ Get Single Order (only own)
    public function getById($id)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $order = $this->orderModel
            ->select('orders.*, customers.name as customer_name')
            ->join('customers', 'customers.id = orders.customer_id', 'left')
            ->where('orders.id', $id)
            ->where('orders.user_id', $user->id)
            ->first();

        if (!$order) {
            return ['error' => 'Order not found', 'code' => 404];
        }

        return [
            'success' => true,
            'data' => $order
        ];
    }

    // ✅ Update Order (only own)
    public function update($id, array $data)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $order = $this->orderModel
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return ['error' => 'Order not found', 'code' => 404];
        }

        try {
            $this->orderModel->update($id, $data);

            return [
                'success' => true,
                'data' => $this->orderModel->find($id)
            ];
        } catch (\Exception $e) {
            log_message('error', 'Order update failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to update order',
                'code'  => 500
            ];
        }
    }

    // ✅ Delete Order (only own)
    public function delete($id)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $order = $this->orderModel
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return ['error' => 'Order not found', 'code' => 404];
        }

        try {
            $this->orderModel->delete($id);

            return [
                'success' => true,
                'message' => 'Order deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Order delete failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to delete order',
                'code'  => 500
            ];
        }
    }

    // 🔥 Helper
    private function generateOrderNumber()
    {
        return 'ORD-' . date('YmdHis') . rand(100, 999);
    }
}

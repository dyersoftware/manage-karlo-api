<?php

namespace App\Modules\OrdersItems\Services;

use App\Modules\Orders\Models\OrderModel;
use App\Modules\OrdersItems\Models\OrderItemModel;

class OrderItemService
{
    protected $orderItemModel;
    protected $orderModel;

    public function __construct()
    {
        $this->orderItemModel = new OrderItemModel();
        $this->orderModel     = new OrderModel();
    }

    // ✅ Create Item
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

            'order_id' => 'required|integer',

            'item_type' => 'required|in_list[shirt,pant,kurta,blouse,coat]',

            'quantity' => 'required|integer|greater_than[0]',

            'price' => 'permit_empty|numeric',

            'status' => 'permit_empty|in_list[pending,cutting,stitching,ready]',
        ];

        $validation = service('validation');

        if (!$validation->setRules($rules)->run($data)) {
            return [
                'error' => $validation->getErrors(),
                'code'  => 422
            ];
        }

        // ✅ Check Order Ownership
        $order = $this->orderModel
            ->where('id', $data['order_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return [
                'error' => 'Order not found',
                'code'  => 404
            ];
        }

        try {

            $id = $this->orderItemModel->insert($data);

            return [
                'success' => true,
                'data' => $this->orderItemModel->find($id)
            ];
        } catch (\Exception $e) {

            log_message('error', 'Order item create failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to create order item',
                'code'  => 500
            ];
        }
    }

    // ✅ Get All Items
    public function getAll($orderId = null)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $builder = $this->orderItemModel
            ->select('
                order_items.*,
                orders.order_number
            ')
            ->join('orders', 'orders.id = order_items.order_id', 'left')
            ->where('orders.user_id', $user->id)
            ->orderBy('order_items.id', 'DESC');

        // ✅ Optional Order Filter
        if (!empty($orderId)) {
            $builder->where('order_items.order_id', $orderId);
        }

        return [
            'success' => true,
            'data' => $builder->findAll()
        ];
    }

    // ✅ Get Single Item
    public function getById($id)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $item = $this->orderItemModel
            ->select('
                order_items.*,
                orders.order_number
            ')
            ->join('orders', 'orders.id = order_items.order_id', 'left')
            ->where('order_items.id', $id)
            ->where('orders.user_id', $user->id)
            ->first();

        if (!$item) {
            return [
                'error' => 'Order item not found',
                'code'  => 404
            ];
        }

        return [
            'success' => true,
            'data' => $item
        ];
    }

    // ✅ Update Item
    public function update($id, array $data)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $item = $this->orderItemModel
            ->select('order_items.*')
            ->join('orders', 'orders.id = order_items.order_id', 'left')
            ->where('order_items.id', $id)
            ->where('orders.user_id', $user->id)
            ->first();

        if (!$item) {
            return [
                'error' => 'Order item not found',
                'code'  => 404
            ];
        }

        try {

            $this->orderItemModel->update($id, $data);

            return [
                'success' => true,
                'data' => $this->orderItemModel->find($id)
            ];
        } catch (\Exception $e) {

            log_message('error', 'Order item update failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to update order item',
                'code'  => 500
            ];
        }
    }

    // ✅ Delete Item
    public function delete($id)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $item = $this->orderItemModel
            ->select('order_items.*')
            ->join('orders', 'orders.id = order_items.order_id', 'left')
            ->where('order_items.id', $id)
            ->where('orders.user_id', $user->id)
            ->first();

        if (!$item) {
            return [
                'error' => 'Order item not found',
                'code'  => 404
            ];
        }

        try {

            $this->orderItemModel->delete($id);

            return [
                'success' => true,
                'message' => 'Order item deleted successfully'
            ];
        } catch (\Exception $e) {

            log_message('error', 'Order item delete failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to delete order item',
                'code'  => 500
            ];
        }
    }

    // ✅ Update Item Status
    public function updateStatus($id, string $status)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $allowedStatuses = [
            'pending',
            'cutting',
            'stitching',
            'ready'
        ];

        if (!in_array($status, $allowedStatuses)) {
            return [
                'error' => 'Invalid status',
                'code'  => 422
            ];
        }

        $item = $this->orderItemModel
            ->select('order_items.*')
            ->join('orders', 'orders.id = order_items.order_id', 'left')
            ->where('order_items.id', $id)
            ->where('orders.user_id', $user->id)
            ->first();

        if (!$item) {
            return [
                'error' => 'Order item not found',
                'code'  => 404
            ];
        }

        try {

            $this->orderItemModel->update($id, [
                'status' => $status
            ]);

            return [
                'success' => true,
                'data' => $this->orderItemModel->find($id)
            ];
        } catch (\Exception $e) {

            log_message('error', 'Order item status update failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to update status',
                'code'  => 500
            ];
        }
    }
}

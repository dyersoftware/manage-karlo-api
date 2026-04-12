<?php

namespace App\Modules\Payments\Services;

use App\Modules\Payments\Models\PaymentModel;
use App\Modules\Orders\Models\OrderModel;

class PaymentService
{
    protected $paymentModel;
    protected $orderModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->orderModel   = new OrderModel();
    }

    // ✅ Create Payment
    public function create(array $data)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        if (empty($data['order_id'])) {
            return ['error' => 'Order ID is required', 'code' => 422];
        }
        // ✅ Check order belongs to user
        $order = $this->orderModel
            ->where('id', $data['order_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return ['error' => 'Order not found', 'code' => 404];
        }
        if (empty($order['id'])) {
            return ['error' => 'Order ID not found', 'code' => 422];
        }
        if (empty($order['customer_id'])) {
            return ['error' => 'Customer ID not found', 'code' => 422];
        }



        // ✅ Validation
        $rules = [
            'order_id' => 'required|integer',
            'customer_id' => 'permit_empty|integer',
            'amount' => 'required|decimal|greater_than[0]',
            'payment_method' => 'required|string|max_length[50]',
            'status' => 'permit_empty|in_list[pending,paid,failed]',
        ];

        $validation = service('validation');

        if (!$validation->setRules($rules)->run($data)) {
            return [
                'error' => $validation->getErrors(),
                'code'  => 422
            ];
        }

        // ✅ Inject required fields
        $data['user_id']     = $user->id;
        $data['customer_id'] = $order['customer_id'];

        try {
            $id = $this->paymentModel->insert($data);

            // 🔥 Update order status (basic logic)
            $totalPaid = $this->paymentModel->getTotalPaidByOrder($order['id']);

            $updateData = [];

            if ($totalPaid <= 0) {
                $updateData['payment_status'] = 'unpaid';
            } elseif ($totalPaid < $order['total_amount']) {
                $updateData['payment_status'] = 'partial';
                $updateData['status'] = 'processing';
            } else {
                $updateData['payment_status'] = 'paid';
                $updateData['status'] = 'completed';
            }

            $this->orderModel->update($order['id'], $updateData);

            return [
                'success' => true,
                'data' => $this->paymentModel->find($id)
            ];
        } catch (\Exception $e) {
            log_message('error', 'Payment create failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to create payment',
                'code'  => 500
            ];
        }
    }

    // ✅ Get Payments by Order
    public function getByOrder($orderId)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return ['error' => 'Order not found', 'code' => 404];
        }

        return [
            'success' => true,
            'data' => $this->paymentModel->getByOrder($orderId)
        ];
    }

    // ✅ Delete Payment
    public function delete($id)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $payment = $this->paymentModel
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            return ['error' => 'Payment not found', 'code' => 404];
        }

        try {
            $this->paymentModel->delete($id);

            return [
                'success' => true,
                'message' => 'Payment deleted successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Payment delete failed: ' . $e->getMessage());

            return [
                'error' => 'Failed to delete payment',
                'code'  => 500
            ];
        }
    }

    // ✅ Get Payments by User (optional customer filter)
    public function getByUser($customerId = null)
    {
        $user = service('request')->user;

        if (!$user) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $builder = $this->paymentModel
            ->select('payments.*, customers.name as customer_name, orders.order_number')
            ->join('orders', 'orders.id = payments.order_id', 'left')
            ->join('customers', 'customers.id = payments.customer_id', 'left')
            ->where('payments.user_id', $user->id)
            ->orderBy('payments.id', 'DESC');

        // ✅ optional filter
        if (!empty($customerId)) {
            $builder->where('payments.customer_id', $customerId);
        }

        return [
            'success' => true,
            'data' => $builder->findAll()
        ];
    }
}

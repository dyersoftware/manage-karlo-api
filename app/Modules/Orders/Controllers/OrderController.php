<?php

namespace App\Modules\Orders\Controllers;

use App\Controllers\BaseController;
use App\Modules\Orders\Services\OrderService;

class OrderController extends BaseController
{
    protected $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    // ✅ Create Order
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            $result = $this->orderService->create($data);

            // ❌ Error from service
            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])
                    ->setJSON([
                        'status' => false,
                        'message' => $result['error']
                    ]);
            }

            // ✅ Success
            return $this->response->setStatusCode(201)->setJSON([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => $result['data']
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Order create controller error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    // ✅ Get All Orders (optional customer_id filter)
    public function index()
    {
        try {
            $customerId = $this->request->getGet('customer_id');

            $result = $this->orderService->getAll($customerId);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])
                    ->setJSON([
                        'status' => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'data' => $result['data']
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Order list error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Failed to fetch orders'
            ]);
        }
    }

    // ✅ Get Single Order
    public function show($id)
    {
        try {
            $result = $this->orderService->getById($id);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])
                    ->setJSON([
                        'status' => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'data' => $result['data']
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Order detail error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Failed to fetch order'
            ]);
        }
    }

    // ✅ Update Order
    public function update($id)
    {
        try {
            $data = $this->request->getJSON(true);

            $result = $this->orderService->update($id, $data);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])
                    ->setJSON([
                        'status' => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Order updated successfully',
                'data' => $result['data']
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Order update error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Update failed'
            ]);
        }
    }

    // ✅ Delete Order
    public function delete($id)
    {
        try {
            $result = $this->orderService->delete($id);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])
                    ->setJSON([
                        'status' => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => $result['message']
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Order delete error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Delete failed'
            ]);
        }
    }
}

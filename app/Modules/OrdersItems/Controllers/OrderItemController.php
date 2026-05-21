<?php

namespace App\Modules\OrdersItems\Controllers;

use App\Controllers\BaseController;
use App\Modules\OrdersItems\Services\OrderItemService;

class OrderItemController extends BaseController
{
    protected $orderItemService;

    public function __construct()
    {
        $this->orderItemService = new OrderItemService();
    }

    // ✅ Create Item
    public function create()
    {
        try {

            $data = $this->request->getJSON(true);

            $result = $this->orderItemService->create($data);

            // ❌ Error from service
            if (isset($result['error'])) {

                return $this->response
                    ->setStatusCode($result['code'])
                    ->setJSON([
                        'status'  => false,
                        'message' => $result['error']
                    ]);
            }

            // ✅ Success
            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'status'  => true,
                    'message' => 'Order item created successfully',
                    'data'    => $result['data']
                ]);
        } catch (\Throwable $e) {

            log_message('error', 'Order item create controller error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Something went wrong'
                ]);
        }
    }

    // ✅ Get All Items
    public function index()
    {
        try {

            $orderId = $this->request->getGet('order_id');

            $result = $this->orderItemService->getAll($orderId);

            if (isset($result['error'])) {

                return $this->response
                    ->setStatusCode($result['code'])
                    ->setJSON([
                        'status'  => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response
                ->setJSON([
                    'status' => true,
                    'data'   => $result['data']
                ]);
        } catch (\Throwable $e) {

            log_message('error', 'Order item list error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Failed to fetch order items'
                ]);
        }
    }

    // ✅ Get Single Item
    public function show($id)
    {
        try {

            $result = $this->orderItemService->getById($id);

            if (isset($result['error'])) {

                return $this->response
                    ->setStatusCode($result['code'])
                    ->setJSON([
                        'status'  => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response
                ->setJSON([
                    'status' => true,
                    'data'   => $result['data']
                ]);
        } catch (\Throwable $e) {

            log_message('error', 'Order item detail error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Failed to fetch order item'
                ]);
        }
    }

    // ✅ Update Item
    public function update($id)
    {
        try {

            $data = $this->request->getJSON(true);

            $result = $this->orderItemService->update($id, $data);

            if (isset($result['error'])) {

                return $this->response
                    ->setStatusCode($result['code'])
                    ->setJSON([
                        'status'  => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response
                ->setJSON([
                    'status'  => true,
                    'message' => 'Order item updated successfully',
                    'data'    => $result['data']
                ]);
        } catch (\Throwable $e) {

            log_message('error', 'Order item update error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Update failed'
                ]);
        }
    }

    // ✅ Delete Item
    public function delete($id)
    {
        try {

            $result = $this->orderItemService->delete($id);

            if (isset($result['error'])) {

                return $this->response
                    ->setStatusCode($result['code'])
                    ->setJSON([
                        'status'  => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response
                ->setJSON([
                    'status'  => true,
                    'message' => $result['message']
                ]);
        } catch (\Throwable $e) {

            log_message('error', 'Order item delete error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Delete failed'
                ]);
        }
    }

    // ✅ Update Item Status
    public function updateStatus($id)
    {
        try {

            $data = $this->request->getJSON(true);

            $status = $data['status'] ?? null;

            $result = $this->orderItemService->updateStatus($id, $status);

            if (isset($result['error'])) {

                return $this->response
                    ->setStatusCode($result['code'])
                    ->setJSON([
                        'status'  => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response
                ->setJSON([
                    'status'  => true,
                    'message' => 'Status updated successfully',
                    'data'    => $result['data']
                ]);
        } catch (\Throwable $e) {

            log_message('error', 'Order item status update error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Status update failed'
                ]);
        }
    }
}

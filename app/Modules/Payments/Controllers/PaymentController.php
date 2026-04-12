<?php

namespace App\Modules\Payments\Controllers;

use App\Controllers\BaseController;
use App\Modules\Payments\Services\PaymentService;

class PaymentController extends BaseController
{
    protected $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }


    // ✅ Get Payments by User (optional customer_id filter)
    public function index()
    {
        try {
            $customerId = $this->request->getGet('customer_id');

            $result = $this->paymentService->getByUser($customerId);

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
            log_message('error', 'Payment list error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Failed to fetch payments'
            ]);
        }
    }
    // ✅ Create Payment
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            $result = $this->paymentService->create($data);

            if (isset($result['error'])) {
                return $this->response->setStatusCode($result['code'])
                    ->setJSON([
                        'status' => false,
                        'message' => $result['error']
                    ]);
            }

            return $this->response->setStatusCode(201)->setJSON([
                'status' => true,
                'message' => 'Payment created successfully',
                'data' => $result['data']
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Payment create error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    // ✅ Get payments by order
    public function byOrder($orderId)
    {
        try {
            $result = $this->paymentService->getByOrder($orderId);

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
            log_message('error', 'Payment list error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Failed to fetch payments'
            ]);
        }
    }

    // ✅ Delete Payment
    public function delete($id)
    {
        try {
            $result = $this->paymentService->delete($id);

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
            log_message('error', 'Payment delete error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Delete failed'
            ]);
        }
    }
    // ✅ Get Single Order
    public function show($id)
    {
        try {
            $result = $this->paymentService->getById($id);

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
            log_message('error', 'Payment detail error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'Failed to fetch payment'
            ]);
        }
    }
}

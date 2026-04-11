<?php

namespace App\Modules\Payments\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'order_id',
        'customer_id',
        'user_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'order_id' => 'required|integer',
        'customer_id' => 'permit_empty|integer',
        'user_id' => 'required|integer',
        'amount' => 'required|decimal|greater_than[0]',
        'payment_method' => 'required|string|max_length[50]',
        'status' => 'required|in_list[pending,paid,failed]',
    ];

    protected $validationMessages = [
        'order_id' => [
            'required' => 'Order ID is required',
        ],
        'customer_id' => [
            'integer' => 'Customer ID must be a number',
        ],
        'amount' => [
            'required' => 'Amount is required',
            'decimal'  => 'Amount must be a valid number',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ===============================
    // 🔥 Custom Methods (Preserved)
    // ===============================

    public function getByOrder($orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    public function getTotalPaidByOrder($orderId)
    {
        $result = $this->selectSum('amount')
            ->where('order_id', $orderId)
            ->where('status', 'paid')
            ->first();

        return $result['amount'] ?? 0;
    }

    public function getByCustomer($customerId)
    {
        return $this->where('customer_id', $customerId)->findAll();
    }
}

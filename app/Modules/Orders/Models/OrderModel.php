<?php

namespace App\Modules\Orders\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'customer_id',
        'user_id',
        'order_number',
        'total_amount',
        'advance_amount',
        'due_amount',
        'payment_status',
        'status',
        'order_date',
        'delivery_date',
        'notes',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'customer_id'   => 'required|integer',
        'user_id'       => 'required|integer',
        'order_number'  => 'required|string|max_length[50]',
        'total_amount'  => 'required|decimal',
        'advance_amount' => 'permit_empty|decimal',
        'due_amount'    => 'permit_empty|decimal',

        'payment_status' => 'permit_empty|in_list[unpaid,partial,paid]',
        'status'        => 'required|in_list[pending,in_progress,ready,delivered,cancelled]',

        'order_date'    => 'permit_empty|valid_date',
        'delivery_date' => 'permit_empty|valid_date',

        'notes'         => 'permit_empty|string',
    ];

    protected $validationMessages = [
        'customer_id' => [
            'required' => 'Customer ID is required',
            'integer'  => 'Customer ID must be a number',
        ],
        'user_id' => [
            'required' => 'User ID is required',
            'integer'  => 'User ID must be a number',
        ],
        'order_number' => [
            'required'   => 'Order number is required',
            'max_length' => 'Order number cannot exceed 50 characters',
        ],
        'total_amount' => [
            'required' => 'Total amount is required',
            'decimal'  => 'Total amount must be a valid number',
        ],
        'payment_status' => [
            'in_list' => 'Payment status must be unpaid, partial, or paid',
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list'  => 'Status must be pending, in_progress, ready, delivered, or cancelled',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks (optional future use)
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateAmounts'];
    protected $beforeUpdate   = ['calculateAmounts'];

    /**
     * Auto calculate due amount
     */
    protected function calculateAmounts(array $data)
    {
        if (isset($data['data']['total_amount'])) {

            $total   = (float) ($data['data']['total_amount'] ?? 0);
            $advance = (float) ($data['data']['advance_amount'] ?? 0);

            $data['data']['due_amount'] = max($total - $advance, 0);

            // auto payment status
            if ($advance <= 0) {
                $data['data']['payment_status'] = 'unpaid';
            } elseif ($advance < $total) {
                $data['data']['payment_status'] = 'partial';
            } else {
                $data['data']['payment_status'] = 'paid';
            }
        }

        return $data;
    }
}

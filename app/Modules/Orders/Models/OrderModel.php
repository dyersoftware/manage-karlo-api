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
    protected $allowedFields    = [
        'customer_id',
        'user_id',
        'order_number',
        'total_amount',
        'payment_type',
        'payment_status',
        'status',
        'notes',
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
    protected $validationRules      = [
        'customer_id'  => 'required|integer',
        'user_id'      => 'required|integer',
        'order_number' => 'required|string|max_length[50]',
        'total_amount' => 'required|decimal',
        'payment_type' => 'required|in_list[full,partial]',
        'payment_status' => 'permit_empty|in_list[unpaid,partial,paid]',
        'status'       => 'required|in_list[pending,processing,completed,cancelled]',
        'notes'        => 'permit_empty|string'
    ];
    protected $validationMessages   = [
        'customer_id' => [
            'required' => 'Customer ID is required',
            'integer'  => 'Customer ID must be a number',
        ],
        'user_id' => [
            'required' => 'User ID is required',
            'integer'  => 'User ID must be a number',
        ],
        'order_number' => [
            'required' => 'Order number is required',
            'string'   => 'Order number must be a string',
            'max_length' => 'Order number cannot exceed 50 characters',
        ],
        'total_amount' => [
            'required' => 'Total amount is required',
            'decimal'  => 'Total amount must be a valid number',
        ],
        'payment_type' => [
            'required' => 'Payment type is required',
            'in_list'  => 'Payment type must be either full or partial',
        ],
        'payment_status' => [
            'in_list'  => 'Payment status must be unpaid, partial, or paid',
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list'  => 'Status must be pending, processing, completed, or cancelled',
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
}

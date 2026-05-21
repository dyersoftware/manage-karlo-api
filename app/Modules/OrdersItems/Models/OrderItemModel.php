<?php

namespace App\Modules\OrdersItems\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Soft Deletes
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $protectFields = true;

    protected $allowedFields = [
        'order_id',

        'item_type',
        'quantity',
        'price',

        // Shirt
        'chest',
        'shoulder',
        'sleeve',
        'collar',

        // Pant
        'waist',
        'hip',
        'thigh',
        'bottom',

        // Common
        'length',

        'design_notes',
        'status',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    /*
    |--------------------------------------------------------------------------
    | Dates
    |--------------------------------------------------------------------------
    */

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected $validationRules = [

        'order_id' => 'required|integer',

        'item_type' => 'required|in_list[shirt,pant,kurta,blouse,coat]',

        'quantity' => 'required|integer|greater_than[0]',

        'price' => 'permit_empty|numeric',

        // Shirt Measurements
        'chest'    => 'permit_empty|numeric',
        'shoulder' => 'permit_empty|numeric',
        'sleeve'   => 'permit_empty|numeric',
        'collar'   => 'permit_empty|numeric',

        // Pant Measurements
        'waist'  => 'permit_empty|numeric',
        'hip'    => 'permit_empty|numeric',
        'thigh'  => 'permit_empty|numeric',
        'bottom' => 'permit_empty|numeric',

        // Common
        'length' => 'permit_empty|numeric',

        'design_notes' => 'permit_empty',

        'status' => 'permit_empty|in_list[pending,cutting,stitching,ready]',
    ];

    protected $validationMessages = [

        'order_id' => [
            'required' => 'Order ID is required',
            'integer'  => 'Order ID must be a number',
        ],

        'item_type' => [
            'required' => 'Item type is required',
            'in_list'  => 'Invalid item type selected',
        ],

        'quantity' => [
            'required'     => 'Quantity is required',
            'integer'      => 'Quantity must be a number',
            'greater_than' => 'Quantity must be greater than 0',
        ],

        'price' => [
            'numeric' => 'Price must be a valid number',
        ],

        'status' => [
            'in_list' => 'Invalid status selected',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /*
    |--------------------------------------------------------------------------
    | Callbacks
    |--------------------------------------------------------------------------
    */

    protected $allowCallbacks = true;

    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get items by order ID
     */
    public function getItemsByOrderId(int $orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }
}

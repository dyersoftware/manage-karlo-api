<?php

namespace App\Modules\Customers\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'address',
        'user_id',
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
        'name'  => 'required|string|max_length[100]',
        'email' => 'required|valid_email|max_length[150]',
        'phone' => 'required|string|max_length[20]',
        'address' => 'permit_empty|string',
        'user_id' => 'required|integer',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Name is required',
            'string'   => 'Name must be a string',
            'max_length' => 'Name cannot exceed 100 characters',
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Email must be a valid email address',
            'max_length' => 'Email cannot exceed 150 characters',
        ],
        'phone' => [
            'required' => 'Phone number is required',
            'string'   => 'Phone must be a string',
            'max_length' => 'Phone cannot exceed 20 characters',
        ],
        'address' => [
            'string'   => 'Address must be a string',
        ],
        'user_id' => [
            'required' => 'User ID is required',
            'integer'  => 'User ID must be a number',
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

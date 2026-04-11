<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'customer_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],

            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],

            'order_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],

            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],

            // ✅ NEW COLUMN
            'payment_type' => [
                'type'       => 'ENUM',
                'constraint' => ['full', 'partial'],
                'default'    => 'full',
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['unpaid', 'partial', 'paid'],
                'default'    => 'unpaid',
            ],

            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'completed', 'cancelled'],
                'default'    => 'pending',
            ],

            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // indexes
        $this->forge->addKey('customer_id');
        $this->forge->addKey('user_id');

        // foreign keys
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE',);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders', true);
    }
}

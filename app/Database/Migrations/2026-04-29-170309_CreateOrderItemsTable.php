<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'order_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],

            'item_type' => [
                'type'       => 'ENUM',
                'constraint' => ['shirt', 'pant', 'kurta', 'blouse', 'coat'],
            ],

            'quantity' => [
                'type'    => 'INT',
                'default' => 1,
            ],

            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],

            // =========================
            // 👕 SHIRT MEASUREMENTS
            // =========================
            'chest'     => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'shoulder'  => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'sleeve'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'collar'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],

            // =========================
            // 👖 PANT MEASUREMENTS
            // =========================
            'waist'     => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'hip'       => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'thigh'     => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'bottom'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],

            // =========================
            // 🧥 COMMON
            // =========================
            'length'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],

            'design_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'cutting', 'stitching', 'ready'],
                'default'    => 'pending',
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'deleted_at' => [ // ✅ soft delete support
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // ✅ Primary Key
        $this->forge->addKey('id', true);

        // ✅ Indexes (performance boost)
        $this->forge->addKey('order_id');
        $this->forge->addKey('item_type');
        $this->forge->addKey('status');

        // ✅ Foreign Key (with safety)
        $this->forge->addForeignKey(
            'order_id',
            'orders',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('order_items');
    }

    public function down()
    {
        $this->forge->dropTable('order_items', true);
    }
}

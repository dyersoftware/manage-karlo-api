<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersCustomers extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'customer_id' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'customer_id']); // prevent duplicate

        $this->forge->createTable('users_customers');
    }

    public function down()
    {
        //
    }
}

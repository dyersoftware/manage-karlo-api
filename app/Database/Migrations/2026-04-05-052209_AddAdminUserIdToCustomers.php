<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminUserIdToCustomers extends Migration
{
    public function up()
    {
        //
        $this->forge->addColumn('customers', [
            'admin_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'after' => 'id', // optional
            ],
        ]);
    }

    public function down()
    {
        //
    }
}

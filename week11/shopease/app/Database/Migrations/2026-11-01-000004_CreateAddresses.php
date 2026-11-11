<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel addresses: alamat pengiriman milik customer.
 */
class CreateAddresses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'recipient_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'        => ['type' => 'TEXT'],
            'city'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'province'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'postal_code'    => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'is_default'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('addresses', true);
    }

    public function down()
    {
        $this->forge->dropTable('addresses', true);
    }
}

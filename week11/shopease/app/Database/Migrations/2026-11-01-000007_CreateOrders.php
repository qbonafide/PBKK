<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel orders: header pesanan. Status pesanan terpisah dari status pembayaran.
 */
class CreateOrders extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'address_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'order_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'order_date'   => ['type' => 'DATETIME'],
            'subtotal'     => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'shipping_cost' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'total'        => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'order_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending_payment'],
            'notes'        => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_number');
        $this->forge->addKey('user_id');
        $this->forge->addKey('order_status');
        $this->forge->addKey('order_date');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('address_id', 'addresses', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('orders', true);
    }

    public function down()
    {
        $this->forge->dropTable('orders', true);
    }
}

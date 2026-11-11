<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel payments: pembayaran manual per order.
 * payment_status terpisah dari orders.order_status (BR-008).
 */
class CreatePayments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'method'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'proof_file'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_status' => ['type' => 'ENUM', 'constraint' => ['unpaid', 'submitted', 'verified', 'rejected'], 'default' => 'unpaid'],
            'verified_by'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'verified_at'    => ['type' => 'DATETIME', 'null' => true],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addKey('payment_status');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('payments', true);
    }
}

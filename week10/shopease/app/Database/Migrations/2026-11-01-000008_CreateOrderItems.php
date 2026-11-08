<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel order_items: snapshot nama & harga produk saat transaksi (BR-004).
 */
class CreateOrderItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'price'        => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'quantity'     => ['type' => 'INT', 'constraint' => 11],
            'subtotal'     => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addKey('product_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('order_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('order_items', true);
    }
}

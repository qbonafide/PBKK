<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel shipments: data kurir, nomor resi, dan status pengiriman.
 */
class CreateShipments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'courier'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tracking_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'shipment_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'shipped_at'      => ['type' => 'DATETIME', 'null' => true],
            'delivered_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shipments', true);
    }

    public function down()
    {
        $this->forge->dropTable('shipments', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel pembayaran: tagihan bulanan tiap penghuni.
 * Kombinasi (penghuni_id, bulan, tahun) dibuat unik supaya satu penghuni tidak
 * punya dua tagihan untuk periode yang sama.
 */
class CreatePembayaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'penghuni_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bulan' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
            ],
            'tahun' => [
                'type'       => 'SMALLINT',
                'constraint' => 4,
                'unsigned'   => true,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bukti_transfer' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_bayar', 'menunggu_verifikasi', 'lunas', 'ditolak'],
                'default'    => 'belum_bayar',
            ],
            'tanggal_bayar' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'catatan' => [
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
        $this->forge->addUniqueKey(['penghuni_id', 'bulan', 'tahun']);
        $this->forge->addForeignKey('penghuni_id', 'penghuni', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pembayaran', true);
    }

    public function down()
    {
        $this->forge->dropTable('pembayaran', true);
    }
}

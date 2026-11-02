<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel kamar: data kamar kos yang tampil di halaman publik dan dikelola admin.
 * `slug` dipakai untuk URL detail kamar, `status` menentukan badge di kartu kamar.
 */
class CreateKamarTable extends Migration
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
            'nomor_kamar' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['standar', 'ac', 'premium'],
                'default'    => 'standar',
            ],
            'harga_bulanan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'luas' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'fasilitas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['kosong', 'terisi', 'perbaikan'],
                'default'    => 'kosong',
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addUniqueKey('nomor_kamar');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('kamar', true);
    }

    public function down()
    {
        $this->forge->dropTable('kamar', true);
    }
}

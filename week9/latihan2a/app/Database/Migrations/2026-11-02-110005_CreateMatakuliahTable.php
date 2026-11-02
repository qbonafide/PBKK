<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel mata kuliah: satu mata kuliah dimiliki satu prodi dan diampu satu dosen.
 * `kuota` dipakai untuk membatasi jumlah mahasiswa yang boleh mengambil kelas.
 */
class CreateMatakuliahTable extends Migration
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
            'prodi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'dosen_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'sks' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
            ],
            'semester' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
            ],
            'kuota' => [
                'type'       => 'SMALLINT',
                'constraint' => 5,
                'unsigned'   => true,
                'default'    => 30,
            ],
            'hari' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
            ],
            'jam_mulai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_selesai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'ruang' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
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
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('prodi_id');
        $this->forge->addKey('dosen_id');
        $this->forge->addForeignKey('prodi_id', 'prodi', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('dosen_id', 'dosen', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('matakuliah', true);
    }

    public function down()
    {
        $this->forge->dropTable('matakuliah', true);
    }
}

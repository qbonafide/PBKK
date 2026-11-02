<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel mahasiswa. Setiap mahasiswa punya satu akun login (unik pada user_id),
 * satu program studi, dan boleh punya satu dosen wali (nullable).
 */
class CreateMahasiswaTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'prodi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'dosen_wali_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nim' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'angkatan' => [
                'type'       => 'SMALLINT',
                'constraint' => 4,
                'unsigned'   => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nim');
        $this->forge->addKey('prodi_id');
        $this->forge->addKey('dosen_wali_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('prodi_id', 'prodi', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('dosen_wali_id', 'dosen', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('mahasiswa', true);
    }

    public function down()
    {
        $this->forge->dropTable('mahasiswa', true);
    }
}

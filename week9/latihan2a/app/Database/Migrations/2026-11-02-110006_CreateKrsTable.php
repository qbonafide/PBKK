<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel KRS: satu baris = satu mata kuliah yang diambil seorang mahasiswa
 * pada satu tahun ajaran. Nilai diisi dosen pengampu setelah perkuliahan selesai.
 *
 * Unik gabungan (mahasiswa_id, matakuliah_id, tahun_ajaran) mencegah mahasiswa
 * mengambil mata kuliah yang sama dua kali di tahun ajaran yang sama.
 */
class CreateKrsTable extends Migration
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
            'mahasiswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'matakuliah_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nilai_angka' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nilai_huruf' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'null'       => true,
            ],
            'bobot' => [
                'type'       => 'DECIMAL',
                'constraint' => '3,2',
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
        $this->forge->addUniqueKey(['mahasiswa_id', 'matakuliah_id', 'tahun_ajaran'], 'krs_unik');
        $this->forge->addForeignKey('mahasiswa_id', 'mahasiswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('matakuliah_id', 'matakuliah', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('krs', true);
    }

    public function down()
    {
        $this->forge->dropTable('krs', true);
    }
}

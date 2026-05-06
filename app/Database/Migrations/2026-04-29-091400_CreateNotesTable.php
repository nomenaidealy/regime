<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateNotesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'inscription_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'cours_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'note' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => new RawSql('NULL'),
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->addForeignKey('inscription_id', 'inscriptions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cours_id', 'cours', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notes');
    }

    public function down()
    {
        $this->forge->dropTable('notes');
    }
}

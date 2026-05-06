<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParcoursCoursTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'parcours_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'cours_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'est_optionnel' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'groupe_option' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->addForeignKey('parcours_id', 'parcours', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cours_id', 'cours', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('parcours_cours');
    }

    public function down()
    {
        $this->forge->dropTable('parcours_cours');
    }
}

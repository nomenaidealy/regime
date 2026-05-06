<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParcoursTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nom' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'responsable' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->createTable('parcours');
    }

    public function down()
    {
        $this->forge->dropTable('parcours');
    }
}

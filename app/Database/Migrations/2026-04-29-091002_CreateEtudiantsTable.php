<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEtudiantsTable extends Migration
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
            'prenoms' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'date_naissance' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'lieu_naissance' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->createTable('etudiants');
    }

    public function down()
    {
        $this->forge->dropTable('etudiants');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInscriptionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'etudiant_id' => [
                'type' => 'INT',
                'null' => false,
            ],
            'niveau' => [
                'type'       => 'ENUM',
                'constraint' => ['L1', 'L2', 'L3', 'M1', 'M2'],
                'null'       => false,
            ],
            'annee_universitaire' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'matricule' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
                'unique'     => true,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->addForeignKey('etudiant_id', 'etudiants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inscriptions');
    }

    public function down()
    {
        $this->forge->dropTable('inscriptions');
    }
}

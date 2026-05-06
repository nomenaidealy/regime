<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'code_ue' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'intitule' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'credits' => [
                'type' => 'INT',
                'null' => false,
            ],
            'semestre' => [
                'type' => 'INT',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->createTable('cours');
    }

    public function down()
    {
        $this->forge->dropTable('cours');
    }
}

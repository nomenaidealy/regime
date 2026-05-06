<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterNotesUpdatedAtTimestamp extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE notes MODIFY updated_at TIMESTAMP NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE notes MODIFY updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
    }
}
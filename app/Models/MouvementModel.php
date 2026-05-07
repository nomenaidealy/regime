<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementModel extends Model
{
    protected $table = 'mouvement';

    public function addCredit($userId, $montant, $soldeApres, $description)
    {
        return $this->insert([
            'id_user'       => $userId,
            'montant'       => $montant,
            'type'          => 'CREDIT',
            'montant_apres' => $soldeApres,
            'description'   => $description,
            'date_mouvement'=> date('Y-m-d H:i:s')
        ]);
    }
}
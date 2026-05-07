<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementModel extends Model
{
    protected $table = 'mouvement';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;

    protected $allowedFields = [
        'id_user',
        'montant',
        'type',
        'montant_apres',
        'description',
        'date_mouvement'
    ];

    /**
     * Retourne le solde courant d'un utilisateur (dernier mouvement)
     *
     * @param int $userId
     * @return float
     */
    public function getLastBalance(int $userId): float
    {
        $row = $this->where('id_user', $userId)
                    ->orderBy('date_mouvement', 'DESC')
                    ->limit(1)
                    ->first();

        return $row ? (float)$row['montant_apres'] : 0.0;
    }

    /**
     * Ajoute un mouvement de type CREDIT
     */
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

    /**
     * Ajoute un mouvement de type DEBIT
     */
    public function addDebit($userId, $montant, $soldeApres, $description)
    {
        return $this->insert([
            'id_user'       => $userId,
            'montant'       => $montant,
            'type'          => 'DEBIT',
            'montant_apres' => $soldeApres,
            'description'   => $description,
            'date_mouvement'=> date('Y-m-d H:i:s')
        ]);
    }
}
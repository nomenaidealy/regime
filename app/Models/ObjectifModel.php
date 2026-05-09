<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjectifModel extends Model
{
    protected $table = 'user_objectif';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['id_user', 'id_objectif', 'valeur_objectif', 'date_choix'];

    /**
     * Retourne le dernier objectif choisi par un utilisateur (join avec la table `objectif`).
     * @param int $userId
     * @return object|null
     */
    public function getLatestForUser(int $userId)
    {
        $db = \Config\Database::connect();
        $row = $db->table('user_objectif uo')
            ->select('uo.*, o.libelle, o.id AS objectif_id')
            ->join('objectif o', 'o.id = uo.id_objectif')
            ->where('uo.id_user', $userId)
            ->orderBy('uo.date_choix', 'DESC')
            ->limit(1)
            ->get()->getRow();

        return $row ?: null;
    }
}

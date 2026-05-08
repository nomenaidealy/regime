<?php

namespace App\Models;

use CodeIgniter\Model;

class UserObjectifModel extends Model
{
    // La table dans la base SQL s'appelle `user_objectif` (voir le dump SQL).
    protected $table            = 'user_objectif';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_user', 'id_objectif', 'valeur_objectif', 'date_choix'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Insère un objectif choisi par l'utilisateur.
     * @param int $userId
     * @param int $objectifId
     * @param float $valeur
     * @return int|false id inséré ou false
     */
    public function insertObjectif(int $userId, $objectifId, $valeur)
    {
        // Valeur par défaut si vide
        $val = $valeur !== null && $valeur !== '' ? (float)$valeur : 0.00;

        $data = [
            'id_user' => $userId,
            'id_objectif' => (int)$objectifId,
            'valeur_objectif' => $val,
        ];

        try {
            $this->insert($data);
            return $this->insertID();
        } catch (\Exception $e) {
            // en cas d'erreur (duplicate unique, fk, etc.) retourner false
            return false;
        }
    }

}

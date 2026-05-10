<?php

namespace App\Models;

use CodeIgniter\Model;

class CodePromoModel extends Model
{
    protected $table            = 'code_promo';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'code',
        'montant',
    ];

    protected $validationRules = [
        'code'    => 'required|min_length[3]|max_length[50]|is_unique[code_promo.code]',
        'montant' => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'code' => [
            'required'   => 'Le code est obligatoire',
            'is_unique'  => 'Ce code existe déjà',
            'min_length' => 'Le code doit avoir au moins 3 caractères',
        ],
        'montant' => [
            'required'     => 'Le montant est obligatoire',
            'numeric'      => 'Le montant doit être un nombre',
            'greater_than' => 'Le montant doit être supérieur à 0',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

   
    //? à revoir :
    public function getAvailableCode(string $code, int $userId): ?array
    {
        $codeData = $this->where('code', $code)->first();

        if (!$codeData) {
            return null;
        }

        $db = \Config\Database::connect();
        $alreadyUsed = $db->table('demande_code_promo')
            ->where('id_user', $userId)
            ->where('id_code_promo', $codeData['id'])
            ->countAllResults();

        return $alreadyUsed > 0 ? null : $codeData;
    }

    /**
     * Retourne la liste des codes avec leurs statistiques de demandes.
     */
    public function getAllWithDemandStats(): array
    {
        return $this->db->table('code_promo cp')
            ->select(
                'cp.id, cp.code, cp.montant,
                 COUNT(dc.id) AS total_demandes,
                 SUM(CASE WHEN dc.statut = "EN_ATTENTE" THEN 1 ELSE 0 END) AS demandes_en_attente,
                 SUM(CASE WHEN dc.statut = "VALIDE" THEN 1 ELSE 0 END) AS demandes_validees,
                 SUM(CASE WHEN dc.statut = "REJETE" THEN 1 ELSE 0 END) AS demandes_rejetees,
                 MAX(dc.date_demande) AS derniere_demande,
                 CASE WHEN COUNT(dc.id) > 0 THEN "Pris" ELSE "Non pris" END AS statut_pris'
            )
            ->join('demande_code_promo dc', 'dc.id_code_promo = cp.id', 'left')
            ->groupBy('cp.id, cp.code, cp.montant')
            ->orderBy('cp.code', 'ASC')
            ->get()
            ->getResultArray();
    }
}
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

   
    public function getAvailableCode(string $code, int $userId): ?array
    {
        $codeData = $this->where('code', $code)->first();

        if (!$codeData) {
            return null;
        }

        $db = \Config\Database::connect();
        $alreadyUsed = $db->table('demande_code_promo')
            ->where('id_code_promo', $codeData['id'])
            ->where('id_user', $userId)
            ->whereIn('statut', ['EN_ATTENTE', 'VALIDE'])
            ->countAllResults();

        return $alreadyUsed > 0 ? null : $codeData;
    }
}
<?php
namespace App\Models;

use CodeIgniter\Model;

class UserGoldModel extends Model
{
    protected $table            = 'user_gold_at_time';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_user',
        'date_achat_gold'
    ];

    protected $validationRules = [
        'id_user'         => 'required|numeric|is_not_unique[users.id]',
        'date_achat_gold' => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
        'id_user' => [
            'required'       => 'L\'ID utilisateur est obligatoire',
            'numeric'        => 'L\'ID utilisateur doit être un nombre',
            'is_not_unique'  => 'Cet utilisateur n\'existe pas'
        ],
        'date_achat_gold' => [
            'valid_date' => 'La date doit être une date valide'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

  
    public function grantUserToGold($idUser, $idGold)
    {
        $data = [
            'id_user'         => $idUser,
            'idGold'        => $idGold,
            'date_achat_gold' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }
}

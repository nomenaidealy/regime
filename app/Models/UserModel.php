<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nom', 'email', 'mdp', 'genre','taille','poids','is_gold','date_achat_gold' ,'date_inscription'];


    // Validation
    protected $validationRules      = [
        'nom' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'mdp' => 'required|min_length[6]',
        'genre' => 'required|in_list[Homme,Femme,Autre]',
        'taille' => 'required|numeric',
        'poids' => 'required|numeric',
        'is_gold' => 'required|in_list[0,1]',
        'date_achat_gold' => 'permit_empty|valid_date',
        'date_inscription' => 'permit_empty|valid_date'
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
   
}

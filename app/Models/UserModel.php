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

    protected $allowedFields = [
        'nom', 'email', 'mdp', 'genre', 'taille', 'poids'
        
    ];

    
    protected $validationRules = [
        'nom'    => 'required|min_length[3]|max_length[100]',
        'email'  => 'required|valid_email|is_unique[users.email]',
        'mdp'    => 'required|min_length[6]',
        'genre'  => 'required|in_list[Homme,Femme]', 
        'taille' => 'required|numeric|greater_than[0]',
        'poids'  => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'nom'    => ['required' => 'Le nom est obligatoire'],
        'email'  => ['required' => 'L\'email est obligatoire', 'is_unique' => 'Cet email est déjà utilisé'],
        'mdp'    => ['required' => 'Le mot de passe est obligatoire', 'min_length' => 'Minimum 6 caractères'],
        'genre'  => ['required' => 'Le genre est obligatoire'],
        'taille' => ['required' => 'La taille est obligatoire'],
        'poids'  => ['required' => 'Le poids est obligatoire'],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Récupérer un user par email
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    // Vérifier si un user est Gold
    public function isGold(int $userId): bool
    {
        $db = \Config\Database::connect();
        return $db->table('user_gold_at_time')
                  ->where('id_user', $userId)
                  ->countAllResults() > 0;
    }

    // Calculer le solde du portefeuille
    public function getSolde(int $userId): float
    {
        $db = \Config\Database::connect();
        $row = $db->table('mouvement')
                  ->where('id_user', $userId)
                  ->orderBy('date_mouvement', 'DESC')
                  ->limit(1)
                  ->get()->getRow();
        return $row ? (float)$row->montant_apres : 0.00;
    }
}
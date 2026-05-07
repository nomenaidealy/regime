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

    // ─────────────────────────────────────────
    // WIZARD INSCRIPTION - VALIDATION PAR ÉTAPES
    // ─────────────────────────────────────────

    /**
     * Valide l'étape 1 du wizard: infos personnelles
     * @param array $data Contient: nom, email, genre
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateStep1($data)
    {
        $rules = [
            'nom'   => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'genre' => 'required|in_list[Homme,Femme]',
        ];

        $messages = [
            'nom'   => ['required' => 'Le nom est obligatoire', 'min_length' => 'Minimum 3 caractères'],
            'email' => ['required' => 'L\'email est obligatoire', 'valid_email' => 'Format d\'email invalide'],
            'genre' => ['required' => 'Le genre est obligatoire'],
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules, $messages);

        if (!$validation->run($data)) {
            return [
                'valid' => false,
                'errors' => $validation->getErrors()
            ];
        }

        // Vérifie si l'email existe déjà
        if ($this->where('email', $data['email'])->first()) {
            return [
                'valid' => false,
                'errors' => ['email' => 'Cet email est déjà utilisé']
            ];
        }

        return ['valid' => true];
    }

    /**
     * Valide l'étape 2 du wizard: infos de santé
     * @param array $data Contient: taille, poids, objectif
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateStep2($data)
    {
        $rules = [
            'taille'  => 'required|numeric|greater_than[0]',
            'poids'   => 'required|numeric|greater_than[0]',
            'objectif' => 'required|in_list[Perte de poids,Prise de poids,Maintien]',
        ];

        $messages = [
            'taille'  => ['required' => 'La taille est obligatoire', 'numeric' => 'Valeur numérique requise'],
            'poids'   => ['required' => 'Le poids est obligatoire', 'numeric' => 'Valeur numérique requise'],
            'objectif' => ['required' => 'L\'objectif est obligatoire'],
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules, $messages);

        if (!$validation->run($data)) {
            return [
                'valid' => false,
                'errors' => $validation->getErrors()
            ];
        }

        return ['valid' => true];
    }

    /**
     * Valide l'étape 3 du wizard: sécurité
     * @param array $data Contient: mdp, mdp_confirm
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateStep3($data)
    {
        $rules = [
            'mdp' => 'required|min_length[6]',
            'mdp_confirm' => 'required|matches[mdp]',
        ];

        $messages = [
            'mdp' => ['required' => 'Le mot de passe est obligatoire', 'min_length' => 'Minimum 6 caractères'],
            'mdp_confirm' => ['required' => 'Confirmation obligatoire', 'matches' => 'Les mots de passe ne correspondent pas'],
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules, $messages);

        if (!$validation->run($data)) {
            return [
                'valid' => false,
                'errors' => $validation->getErrors()
            ];
        }

        return ['valid' => true];
    }

    /**
     * Crée un nouvel utilisateur (après validation de toutes les étapes)
     * @param array $data Complet: nom, email, genre, taille, poids, mdp
     * @return int|false Id du nouvel utilisateur ou false
     */
    public function createUser(array $data)
    {
        $userData = [
            'nom'    => $data['nom'],
            'email'  => $data['email'],
            'genre'  => $data['genre'],
            'taille' => $data['taille'],
            'poids'  => $data['poids'],
            'mdp'    => password_hash($data['mdp'], PASSWORD_DEFAULT)
        ];

        if ($this->insert($userData)) {
            return $this->insertID();
        }

        return false;
    }

    // ─────────────────────────────────────────
    // AUTRES FONCTIONS
    // ─────────────────────────────────────────

    // Récupérer un user par email
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function verifyUser(string $email, string $mdp)
    {
        try {
            $user = $this->where('email', $email)->first();

            if (!$user) {
                // Email non trouvé
                return ['success' => false, 'message' => "Adresse email inconnue."];
            }

            if ($user['mdp'] !== $mdp) {
                // Mot de passe incorrect
                return ['success' => false, 'message' => "Mot de passe incorrect."];
            }

            // Connexion réussie
            return ['success' => true, 'user' => $user];
        } catch (\Exception $e) {
            // Erreur technique
            return ['success' => false, 'message' => "Erreur technique : " . $e->getMessage()];
        }
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
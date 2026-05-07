<?php

namespace App\Models;

use CodeIgniter\Model;

class CodePromoModel extends Model
{
    protected $table = 'code_promo';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'code',
        'montant',
        'id_user_utilise',
        'date_utilisation'
    ];

    protected $validationRules = [
        'code'    => 'required|min_length[3]|max_length[50]|is_unique[code_promo.code]',
        'montant' => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'code'    => [
            'required'    => 'Le code est obligatoire',
            'is_unique'   => 'Ce code existe déjà',
            'min_length'  => 'Le code doit avoir au moins 3 caractères'
        ],
        'montant' => [
            'required'      => 'Le montant est obligatoire',
            'numeric'       => 'Le montant doit être un nombre',
            'greater_than'  => 'Le montant doit être supérieur à 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // ────────────────────────────────────────
    // GET VALID CODE
    // ────────────────────────────────────────

    /**
     * Récupère un code promo valide (non utilisé)
     * 
     * @param string $code Le code promo à vérifier
     * @return array|null Le code promo s'il est valide, null sinon
     */
    public function getValidCode($code)
    {
        return $this->where('code', $code)
            ->where('id_user_utilise', null)
            ->first();
    }

    // ────────────────────────────────────────
    // USE CODE PROMO AND CREDIT ACCOUNT
    // ────────────────────────────────────────

    /**
     * Utilise un code promo pour créditer un compte utilisateur
     * 
     * @param string $code Le code promo à utiliser
     * @param int $userId L'ID de l'utilisateur
     * @return array ['success' => bool, 'message' => string, 'montant' => float|null]
     */
    public function useCodeAndCredit($code, $userId)
    {
        // Vérifier que le code est valide
        $codeData = $this->getValidCode($code);

        if (!$codeData) {
            return [
                'success' => false,
                'message' => 'Code promo invalide ou déjà utilisé',
                'montant' => null
            ];
        }

        // Démarrer une transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Récupérer le solde actuel
            $mouvementModel = new MouvementModel();
            $currentBalance = 0;
            $lastMouvement = $db->table('mouvement')
                ->where('id_user', $userId)
                ->orderBy('date_mouvement', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();

            if ($lastMouvement) {
                $currentBalance = (float)$lastMouvement->montant_apres;
            }

            // Calculer le nouveau solde
            $newBalance = $currentBalance + (float)$codeData['montant'];

            // Enregistrer le mouvement de crédit
            $mouvementAdded = $mouvementModel->addCredit(
                $userId,
                (float)$codeData['montant'],
                $newBalance,
                "Crédit code promo: {$code}"
            );

            if (!$mouvementAdded) {
                throw new \Exception("Impossible d'enregistrer le mouvement");
            }

            // Marquer le code comme utilisé
            $codeUpdated = $this->update($codeData['id'], [
                'id_user_utilise'  => $userId,
                'date_utilisation' => date('Y-m-d H:i:s')
            ]);

            if (!$codeUpdated) {
                throw new \Exception("Impossible de mettre à jour le code promo");
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du traitement du code promo',
                    'montant' => null
                ];
            }

            return [
                'success' => true,
                'message' => "Félicitations! {$codeData['montant']} € ont été crédités à votre compte.",
                'montant' => (float)$codeData['montant']
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => "Erreur: " . $e->getMessage(),
                'montant' => null
            ];
        }
    }

    // ────────────────────────────────────────
    // MARK USED
    // ────────────────────────────────────────

    /**
     * Marque un code promo comme utilisé
     * 
     * @param int $id L'ID du code promo
     * @param int $userId L'ID de l'utilisateur
     * @return bool
     */
    public function markUsed($id, $userId)
    {
        return $this->update($id, [
            'id_user_utilise'  => $userId,
            'date_utilisation' => date('Y-m-d H:i:s')
        ]);
    }
}
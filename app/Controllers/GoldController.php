<?php

namespace App\Controllers;

use App\Models\GoldModel;
use CodeIgniter\Controller;

class GoldController extends Controller
{
    protected $goldModel;
    protected $session;

    public function __construct()
    {
        $this->goldModel = new GoldModel();
        $this->session = session();
    }

    // ────────────────────────────────────────
    // ACTIVATE GOLD OPTION
    // ────────────────────────────────────────

    /**
     * Active l'option Gold pour l'utilisateur connecté
     * Redirige vers le dashboard après activation
     */
    public function activate()
    {
        // Vérifier si l'utilisateur est connecté
        $userId = $this->session->get('user_id');
        
        if (!$userId) {
            return redirect()->to('login')->with('error', 'Vous devez être connecté pour activer l\'option Gold');
        }

        // Vérifier si l'utilisateur n'a pas déjà l'option Gold
        $existingGold = $this->goldModel
            ->where('id_user', $userId)
            ->first();

        if ($existingGold) {
            return redirect()->to('dashboard')->with('warning', 'Vous avez déjà l\'option Gold active');
        }

        // Accorder le statut Gold
        $result = $this->goldModel->grantUserToGold($userId);

        if ($result) {
            return redirect()->to('dashboard')->with('success', 'Félicitations! Vous avez activé l\'option Gold');
        } else {
            return redirect()->to('dashboard')->with('error', 'Une erreur est survenue lors de l\'activation de l\'option Gold');
        }
    }

    // ────────────────────────────────────────
    // CHECK GOLD STATUS
    // ────────────────────────────────────────

    /**
     * Vérifie si un utilisateur a l'option Gold active
     * 
     * @param int $userId L'ID de l'utilisateur
     * @return bool True si Gold actif, false sinon
     */
    public function hasGold($userId)
    {
        return $this->goldModel
            ->where('id_user', $userId)
            ->first() !== null;
    }
}

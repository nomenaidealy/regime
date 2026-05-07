<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserObjectifModel;
use App\Models\CodePromoModel;
use App\Models\MouvementModel;
use CodeIgniter\Controller;


class UserController extends Controller
{
    // ─────────────────────────────────────────
    // INSCRIPTION
    // ─────────────────────────────────────────
    public function inscription()
    {
        return view('template/inscription');
    }

      protected $userModel;
    protected $objectifModel;
    protected $promoModel;
    protected $mouvementModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->objectifModel = new UserObjectifModel();
        $this->promoModel    = new CodePromoModel();
        $this->mouvementModel= new MouvementModel();
    }

    // =========================
    // INSCRIPTION - WIZARD AJAX
    // =========================

    /**
     * Endpoint AJAX - Valide l'étape 1 du wizard (infos personnelles)
     */
    public function validateStep1()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }

        $data = [
            'nom'   => $this->request->getPost('nom'),
            'email' => $this->request->getPost('email'),
            'genre' => $this->request->getPost('genre'),
        ];

        $result = $this->userModel->validateStep1($data);

        if ($result['valid']) {
            // Sauvegarder en session
            session()->set('inscription_step1', $data);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    /**
     * Endpoint AJAX - Valide l'étape 2 du wizard (infos santé)
     */
    public function validateStep2()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }

        $data = [
            'taille'  => $this->request->getPost('taille'),
            'poids'   => $this->request->getPost('poids'),
            'objectif' => $this->request->getPost('objectif'),
        ];

        $result = $this->userModel->validateStep2($data);

        if ($result['valid']) {
            // Sauvegarder en session
            session()->set('inscription_step2', $data);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    /**
     * Endpoint AJAX - Valide l'étape 3 du wizard (sécurité)
     */
    public function validateStep3()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }

        $data = [
            'mdp'          => $this->request->getPost('mdp'),
            'mdp_confirm'  => $this->request->getPost('mdp_confirm'),
        ];

        $result = $this->userModel->validateStep3($data);

        if ($result['valid']) {
            // Sauvegarder en session
            session()->set('inscription_step3', $data);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    /**
     * Endpoint AJAX - Finalise l'inscription (création de l'utilisateur)
     */
    public function completeInscription()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }

        // Récupérer les données sauvegardées en session
        $step1Data = session()->get('inscription_step1') ?? [];
        $step2Data = session()->get('inscription_step2') ?? [];
        $step3Data = session()->get('inscription_step3') ?? [];

        // Fusionner toutes les données
        $data = array_merge($step1Data, $step2Data, [
            'mdp' => $step3Data['mdp'] ?? '',
        ]);

        // Crée l'utilisateur
        $userId = $this->userModel->createUser($data);

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la création']);
        }

        // Ajoute l'objectif
        $this->objectifModel->insertObjectif(
            $userId,
            $step2Data['objectif'] ?? null,
            $this->request->getPost('valeur_objectif')
        );

        // Crée la session
        session()->set([
            'user_id'    => $userId,
            'user_nom'   => $data['nom'],
            'isLoggedIn' => true
        ]);

        // Nettoyer les données temporaires
        session()->remove(['inscription_step1', 'inscription_step2', 'inscription_step3']);

        return $this->response->setJSON(['success' => true, 'redirect' => 'dashboard']);
    }

    /**
     * (Ancien endpoint - conservation pour compatibilité temporaire)
     */
    public function saveUser()
    {
        return redirect()->to('inscription');
    }

    // =========================
    // WALLET
    // =========================
    public function rechargerWallet()
    {
        $userId = session()->get('user_id');
        $code   = $this->request->getPost('code');

        $promo = $this->promoModel->getValidCode($code);

        if (!$promo) {
            return redirect()->back()->with('error', 'Code invalide');
        }

        $solde = $this->userModel->getSolde($userId);
        $newSolde = $solde + $promo['montant'];

        $this->mouvementModel->addCredit(
            $userId,
            $promo['montant'],
            $newSolde,
            "Code promo $code"
        );

        $this->promoModel->markUsed($promo['id'], $userId);

        session()->set('solde', $newSolde);

        return redirect()->back()->with('success', 'Rechargé');
    }    

    // ─────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────
    public function loginPage()
    {
        return view('/template/user/login');
    }

    public function login()
    {
        $userModel = new UserModel();
        $email     = $this->request->getPost('email');
        $mdp       = $this->request->getPost('mdp');

        $result = $userModel->verifyUser($email, $mdp);
        if (!$result['success']) {
            return redirect()->to('login')->with('login_error', $result['message']);
        }
        $user = $result['user'];

        // Vérifier si Gold
        $db     = \Config\Database::connect();
        $isGold = $db->table('user_gold_at_time')
            ->where('id_user', $user['id'])
            ->countAllResults() > 0;

        // Calculer solde
        $dernierMouvement = $db->table('mouvement')
            ->where('id_user', $user['id'])
            ->orderBy('date_mouvement', 'DESC')
            ->limit(1)
            ->get()->getRow();
        $solde = $dernierMouvement ? (float)$dernierMouvement->montant_apres : 0.00;

        // Créer session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'user_nom'   => $user['nom'],
            'user_email' => $user['email'],
            'user_genre' => $user['genre'],
            'is_gold'    => $isGold,
            'solde'      => $solde,
        ]);

        return redirect()->to('dashboard')
            ->with('success', 'Bon retour, ' . $user['nom'] . ' !');
    }

    // ─────────────────────────────────────────
    // LOGOUT
    // ─────────────────────────────────────────
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login')
            ->with('success', 'Vous êtes déconnecté.');
    }

    // ─────────────────────────────────────────
    // PROFIL
    // ─────────────────────────────────────────
    public function profil()
    {
        $userId    = session()->get('user_id');
        $userModel = new UserModel();
        $db        = \Config\Database::connect();

        $user = $userModel->find($userId);

        // IMC
        $imc = round($user['poids'] / ($user['taille'] * $user['taille']), 1);

        // Objectif actuel
        $objectif = $db->table('user_objectif uo')
            ->join('objectif o', 'o.id = uo.id_objectif')
            ->where('uo.id_user', $userId)
            ->orderBy('uo.date_choix', 'DESC')
            ->limit(1)
            ->get()->getRow();

        // Solde
        $dernierMouvement = $db->table('mouvement')
            ->where('id_user', $userId)
            ->orderBy('date_mouvement', 'DESC')
            ->limit(1)
            ->get()->getRow();
        $solde = $dernierMouvement ? (float)$dernierMouvement->montant_apres : 0.00;

        // Gold ?
        $isGold = $db->table('user_gold_at_time')
            ->where('id_user', $userId)
            ->countAllResults() > 0;

        return view('profil', [
            'user'     => $user,
            'imc'      => $imc,
            'objectif' => $objectif,
            'solde'    => $solde,
            'isGold'   => $isGold,
        ]);
    }

}

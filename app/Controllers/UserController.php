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

        // Récupérer les données sauvegardées en session, ou fallback aux POST si session manquante
        $step1Data = session()->get('inscription_step1') ?? [];
        $step2Data = session()->get('inscription_step2') ?? [];
        $step3Data = session()->get('inscription_step3') ?? [];

        // Fallback vers POST si les sessions sont absentes (robuste pour appels directs)
        $step1Data['nom']   = $step1Data['nom']   ?? $this->request->getPost('nom');
        $step1Data['email'] = $step1Data['email'] ?? $this->request->getPost('email');
        $step1Data['genre'] = $step1Data['genre'] ?? $this->request->getPost('genre');

        $step2Data['taille']   = $step2Data['taille']   ?? $this->request->getPost('taille');
        $step2Data['poids']    = $step2Data['poids']    ?? $this->request->getPost('poids');
        $step2Data['objectif'] = $step2Data['objectif'] ?? $this->request->getPost('objectif');

        $step3Data['mdp'] = $step3Data['mdp'] ?? $this->request->getPost('mdp');

        // Fusionner toutes les données
        $data = array_merge($step1Data, $step2Data, [
            'mdp' => $step3Data['mdp'] ?? '',
        ]);

        // Crée l'utilisateur
        $userId = $this->userModel->createUser($data);

        if (!$userId) {
            // Récupérer erreurs du modèle si disponibles
            $errors = $this->userModel->errors();
            $msg = 'Erreur lors de la création';
            if (!empty($errors)) {
                $msg = implode(' | ', $errors);
            }
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
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

        return $this->response->setJSON(['success' => true, 'redirect' => '/dashboard']);
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

        session()->remove(['is_admin', 'admin_id', 'admin_login']);

        // Déléguer vérifs au modèle
        $isGold = $userModel->isGold((int)$user['id']);
        $solde = $userModel->getSoldeActuelle((int)$user['id']);

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'user_nom'   => $user['nom'],
            'user_email' => $user['email'],
            'user_genre' => $user['genre'],
            'is_gold'    => $isGold,
            'solde'      => $solde,
        ]);

        return redirect()->to('/user/mes-regimes')->with('success', 'Bon retour, ' . $user['nom'] . ' !');
    }

    /**
     * Ancien dashboard conservé pour compatibilité : redirige vers Mes régimes.
     */
    public function dashboard()
    {
        return redirect()->to('/user/mes-regimes');
    }

    /**
     * Page Mes régimes : suggestions de régimes + abonnements de l'utilisateur
     */
    public function mesRegimes()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('login')->with('error', 'Connectez-vous pour voir vos régimes.');
        }
        // déléguer la logique métier au modèle
        $userModel = new UserModel();
        $data = $userModel->getMesRegimesData($userId);
        if (empty($data)) return redirect()->to('login')->with('error', 'Utilisateur introuvable.');

        return view('template/mesRegimes', $data);
    }

    /**
     * Détails d'une suggestion de régime
     */
    public function regimeDetails($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('login')->with('error', 'Connectez-vous pour voir les détails.');

        if (!$id || !is_numeric($id)) {
            return redirect()->to('/user/mes-regimes')->with('error', 'Régime introuvable.');
        }

        // récupérer info utilisateur / objectif
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) return redirect()->to('login')->with('error', 'Utilisateur introuvable.');

        $objectifModel = new \App\Models\ObjectifModel();
        $objectif = $objectifModel->getLatestForUser($userId);

        $regimeModel = new \App\Models\RegimeModel();
        $details = $regimeModel->getRegimeDetailsForUser((int)$id, (float)$user['poids'], (float)$user['taille'], $objectif);

        if (empty($details)) return redirect()->to('/user/mes-regimes')->with('error', 'Régime introuvable.');

        return view('template/regimeDetails', [
            'regime' => $details['regime'],
            'prixs' => $details['prixs'],
            'jours_estimes' => $details['jours_estimes'],
            'possible' => $details['possible'],
        ]);
    }

    /**
     * Endpoint POST : souscrire à un régime (faire ce régime)
     */
    public function souscrire($dietId)
    {
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('login')->with('error', 'Connectez-vous pour souscrire.');

        if (!$dietId || !is_numeric($dietId)) {
            return redirect()->to('user/mes-regimes')->with('error', 'Régime invalide.');
        }

        $prixId = $this->request->getPost('prix_id') ? (int)$this->request->getPost('prix_id') : null;
        $nombreJours = $this->request->getPost('nombre_jour') ? (int)$this->request->getPost('nombre_jour') : null;

        $userModel = new UserModel();
        $result = $userModel->subscribeToRegime((int)$userId, (int)$dietId, $prixId, $nombreJours);

        if ($result['success']) {
            return redirect()->to('user/mes-regimes')->with('success', $result['message']);
        }
        return redirect()->to('user/mes-regimes')->with('error', $result['message']);
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
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('login')->with('error', 'Connectez-vous pour voir votre profil.');

        $userModel = new UserModel();
        $data = $userModel->getProfilData($userId);
        if (empty($data)) return redirect()->to('login')->with('error', 'Utilisateur introuvable.');

        return view('profil', $data);
    }

}

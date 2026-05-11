<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserObjectifModel;
use App\Models\CodePromoModel;
use App\Models\MouvementModel;
use App\Models\UserExportModel;
use CodeIgniter\Controller;


class UserController extends Controller
{
    // ─────────────────────────────────────────
    // INSCRIPTION
    // ─────────────────────────────────────────
    public function inscription()
    {
        return view('template/user/inscription');
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

    // (suppression des méthodes d'inscription par étapes)
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

        return view('template/user/regime/list', $data);
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

        return view('template/user/regime/detail', [
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

        return view('template/user/profil', $data);
    }

    /**
     * Exporter la liste des suggestions et abonnements en PDF
     */
    public function exportMesRegimes()
    {
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('login')->with('error', 'Connectez-vous pour exporter.');

        $userModel = new UserModel();
        $data = $userModel->getMesRegimesData($userId);
        if (empty($data)) return redirect()->to('/user/mes-regimes')->with('error', 'Aucune donnée à exporter.');

        $user = $data['user'] ?? ['nom' => 'Utilisateur'];
        $suggestions = $data['suggestions'] ?? [];
        $subscriptions = $data['subscriptions'] ?? [];

        $exportModel = new UserExportModel();
        $pdf = $exportModel->generatePdf($user, $suggestions, $subscriptions);

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="mes-regimes.pdf"')
            ->setBody($pdf);
    }

}

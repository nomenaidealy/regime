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

        $db = \Config\Database::connect();

        // Vérifier si Gold
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

        return redirect()->to('/mes-regimes')->with('success', 'Bon retour, ' . $user['nom'] . ' !');
    }

    /**
     * Ancien dashboard conservé pour compatibilité : redirige vers Mes régimes.
     */
    public function dashboard()
    {
        return redirect()->to('/mes-regimes');
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

        $userModel = new UserModel();
        $db = \Config\Database::connect();

        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to('login')->with('error', 'Utilisateur introuvable.');
        }

        $imc = round($user['poids'] / ($user['taille'] * $user['taille']), 1);

        $objectif = $db->table('user_objectif uo')
            ->join('objectif o', 'o.id = uo.id_objectif')
            ->where('uo.id_user', $userId)
            ->orderBy('uo.date_choix', 'DESC')
            ->limit(1)
            ->get()->getRow();

        // Solde
        $solde = $userModel->getSoldeActuelle($userId);

        $isGold = $db->table('user_gold_at_time')
            ->where('id_user', $userId)
            ->countAllResults() > 0;

        // Suggestions de régimes
        $suggestions = $db->table('diet d')
            ->select('d.id AS diet_id, d.nom AS diet_nom, d.description AS diet_description, d.variation_poids_jour, d.viande_percent, d.volaille_percent, d.poisson_percent, s.libelle AS sport_libelle, s.variation_poids_seance, dp.prix AS prix_30, dp.id AS prix_id')
            ->join('sport s', 's.id = d.id_sport', 'left')
            ->join('diet_prix dp', 'dp.id_diet = d.id AND dp.duree = 30', 'left')
            ->orderBy('d.nom', 'ASC')
            ->get()->getResultArray();

        // Calcul de la durée personnalisée pour chaque suggestion selon l'objectif utilisateur
        $userWeight = (float)$user['poids'];
        $userHeight = (float)$user['taille'];

        // Valeur cible (poids ou IMC selon l'objectif)
        $targetWeight = null;
        if ($objectif && isset($objectif->valeur_objectif)) {
            // Si l'objectif est un IMC (libelle contenant "IMC" ou id_objectif == 3), convertir en poids
            $libelle = strtolower($objectif->libelle ?? '');
            if (strpos($libelle, 'imc') !== false) {
                $targetIMC = (float)$objectif->valeur_objectif;
                $targetWeight = $targetIMC * ($userHeight * $userHeight);
            } else {
                $targetWeight = (float)$objectif->valeur_objectif;
            }
        }

        foreach ($suggestions as &$s) {
            $s['jours_estimes'] = null;
            $s['possible'] = true;
            if ($targetWeight === null || !isset($s['variation_poids_jour']) || $s['variation_poids_jour'] == 0) {
                $s['possible'] = false;
                continue;
            }

            $delta = $targetWeight - $userWeight; // >0 => besoin de prendre du poids, <0 => perdre
            if (abs($delta) < 0.001) {
                $s['jours_estimes'] = 0;
                continue;
            }

            $varJour = (float)$s['variation_poids_jour'];

            // Vérifier compatibilité sens : perte vs prise
            if ($delta > 0 && $varJour <= 0) {
                $s['possible'] = false; // régime non adapté (fait perdre au lieu de prendre)
                continue;
            }
            if ($delta < 0 && $varJour >= 0) {
                $s['possible'] = false; // régime non adapté (fait prendre au lieu de perdre)
                continue;
            }

            $jours = (int)ceil(abs($delta) / abs($varJour));
            $s['jours_estimes'] = $jours;
        }

        // Ne garder que les suggestions recommandées
        $suggestions = array_values(array_filter($suggestions, function ($s) {
            return isset($s['possible']) && $s['possible'];
        }));

        // Abonnements
        $subscriptions = $db->table('user_diet ud')
            ->select('ud.*, dp.duree, dp.prix, d.nom AS diet_nom')
            ->join('diet_prix dp', 'dp.id = ud.id_diet_prix')
            ->join('diet d', 'd.id = dp.id_diet')
            ->where('ud.id_user', $userId)
            ->orderBy('ud.date_debut', 'DESC')
            ->get()->getResultArray();

        return view('template/mesRegimes', [
            'user' => $user,
            'imc' => $imc,
            'objectif' => $objectif,
            'solde' => $solde,
            'isGold' => $isGold,
            'suggestions' => $suggestions,
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Détails d'une suggestion de régime
     */
    public function regimeDetails($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('login')->with('error', 'Connectez-vous pour voir les détails.');
        }

        if (!$id || !is_numeric($id)) {
            return redirect()->to('/mes-regimes')->with('error', 'Régime introuvable.');
        }

        $db = \Config\Database::connect();

        $regime = $db->table('diet d')
            ->select('d.id AS diet_id, d.nom AS diet_nom, d.description AS diet_description, d.variation_poids_jour, d.viande_percent, d.volaille_percent, d.poisson_percent, s.libelle AS sport_libelle, s.description AS sport_description, s.variation_poids_seance')
            ->join('sport s', 's.id = d.id_sport', 'left')
            ->where('d.id', $id)
            ->get()->getRowArray();

        if (!$regime) {
            return redirect()->to('/mes-regimes')->with('error', 'Régime introuvable.');
        }

        $prixs = $db->table('diet_prix')
            ->where('id_diet', $id)
            ->orderBy('duree', 'ASC')
            ->get()->getResultArray();

        return view('template/regimeDetails', [
            'regime' => $regime,
            'prixs' => $prixs,
        ]);
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
        // Solde
        $solde = $userModel->getSoldeActuelle($userId);

        // Gold ?
        $isGold = $db->table('user_gold_at_time')
            ->where('id_user', $userId)
            ->countAllResults() > 0;

        // Régime en cours / dernier régime souscrit
        $regimeActuel = $db->table('user_diet ud')
            ->select('ud.date_debut, ud.prix_paye, ud.remise_gold, dp.duree, dp.prix, d.nom AS diet_nom, d.description AS diet_description')
            ->join('diet_prix dp', 'dp.id = ud.id_diet_prix')
            ->join('diet d', 'd.id = dp.id_diet')
            ->where('ud.id_user', $userId)
            ->orderBy('ud.date_debut', 'DESC')
            ->limit(1)
            ->get()->getRow();

        return view('profil', [
            'user'     => $user,
            'imc'      => $imc,
            'objectif' => $objectif,
            'solde'    => $solde,
            'isGold'   => $isGold,
            'regimeActuel' => $regimeActuel,
        ]);
    }

}

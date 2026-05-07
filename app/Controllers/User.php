<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class User extends Controller
{
    // ─────────────────────────────────────────
    // INSCRIPTION
    // ─────────────────────────────────────────
    public function inscription()
    {
        return view('template/inscription');
    }
public function saveUser()
{
    $userModel = new UserModel();
    $email     = $this->request->getPost('email');

    // Email déjà utilisé ?
    if ($userModel->where('email', $email)->first()) {
        return redirect()->to('inscription')
                         ->with('error', 'Cet email est déjà utilisé.');
    }

    $nom             = $this->request->getPost('nom');
    $genre           = $this->request->getPost('genre');
    $taille          = $this->request->getPost('taille');
    $poids           = $this->request->getPost('poids');
    $mdp             = $this->request->getPost('mdp');
    $objectif        = $this->request->getPost('objectif');
    $valeurObjectif  = $this->request->getPost('valeur_objectif');

    // Si objectif = IMC idéal (3) → calculer automatiquement
    if ($objectif == 3) {
        $tailleEnCm = $taille * 100;
        if ($genre == 'Femme') {
            $poidsIdeal = $tailleEnCm - 100 - (($tailleEnCm - 150) / 2.5);
        } else {
            $poidsIdeal = $tailleEnCm - 100 - (($tailleEnCm - 150) / 4);
        }
        $valeurObjectif = round(abs($poids - $poidsIdeal), 2);
    }
    // Objectif 1 ou 2 → valeur saisie par l'utilisateur (déjà dans $valeurObjectif)

    // Insert user
    $userModel->insert([
        'nom'    => $nom,
        'email'  => $email,
        'genre'  => $genre,
        'taille' => $taille,
        'poids'  => $poids,
        'mdp'    => password_hash($mdp, PASSWORD_DEFAULT),
    ]);

    $userId = $userModel->getInsertID();
    $db     = \Config\Database::connect();

    // Insert objectif
    $db->table('user_objectif')->insert([
        'id_user'         => $userId,
        'id_objectif'     => $objectif,
        'date_choix'      => date('Y-m-d H:i:s'),
        'valeur_objectif' => $valeurObjectif,
    ]);

    // Session
    session()->set([
        'isLoggedIn' => true,
        'user_id'    => $userId,
        'user_nom'   => $nom,
        'user_genre' => $genre,
        'is_gold'    => false,
        'solde'      => 0.00,
    ]);

    return redirect()->to('dashboard')
                     ->with('success', 'Bienvenue sur NutriPlan, ' . $nom . ' !');
}

    // ─────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────
    public function loginPage()
    {
        // Si déjà connecté → redirect dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('dashboard');
        }
        return view('template/login');
    }

    public function login()
    {
        $userModel = new UserModel();
        $email     = $this->request->getPost('email');
        $mdp       = $this->request->getPost('mdp');

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($mdp, $user['mdp'])) {
            return redirect()->to('login')
                             ->with('error', 'Email ou mot de passe incorrect.');
        }

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

    // ─────────────────────────────────────────
    // RECHARGER WALLET avec code promo
    // ─────────────────────────────────────────
    public function rechargerWallet()
    {
        $userId = session()->get('user_id');
        $code   = $this->request->getPost('code');
        $db     = \Config\Database::connect();

        // Vérifier si code existe et non utilisé
        $codePromo = $db->table('code_promo')
                        ->where('code', $code)
                        ->where('id_user_utilise', null)
                        ->get()->getRow();

        if (!$codePromo) {
            return redirect()->back()
                             ->with('error', 'Code invalide ou déjà utilisé.');
        }

        // Solde actuel
        $dernierMouvement = $db->table('mouvement')
                               ->where('id_user', $userId)
                               ->orderBy('date_mouvement', 'DESC')
                               ->limit(1)
                               ->get()->getRow();
        $soldeCourant = $dernierMouvement ? (float)$dernierMouvement->montant_apres : 0.00;
        $nouveauSolde = $soldeCourant + $codePromo->montant;

        // Enregistrer mouvement CREDIT
        $db->table('mouvement')->insert([
            'id_user'      => $userId,
            'montant'      => $codePromo->montant,
            'type'         => 'CREDIT',
            'montant_apres'=> $nouveauSolde,
            'description'  => 'Code promo : ' . $code,
        ]);

        // Marquer code utilisé
        $db->table('code_promo')
           ->where('id', $codePromo->id)
           ->update([
               'id_user_utilise'  => $userId,
               'date_utilisation' => date('Y-m-d H:i:s'),
           ]);

        // Mettre à jour session
        session()->set('solde', $nouveauSolde);

        return redirect()->back()
                         ->with('success', 'Portefeuille rechargé de ' . number_format($codePromo->montant, 0, ',', ' ') . ' Ar !');
    }
}
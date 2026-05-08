<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AdminController extends BaseController
{
    public function index()
    {
        return view('template/admin/login');
    }

    public function tolog()
    {
        $login = (string) $this->request->getPost('login');
        $mdp   = (string) $this->request->getPost('mdp');

        $adminModel = new AdminModel();
        $result = $adminModel->tolog($login, $mdp);

        if (!$result['success']) {
            return redirect()->to('admin')->with('login_error', $result['message']);
        }

        $admin = $result['admin'];

        session()->remove(['user_id', 'user_nom', 'user_email', 'user_genre', 'is_gold', 'solde']);

        session()->set([
            'isLoggedIn'  => true,
            'is_admin'    => true,
            'admin_id'    => $admin['id'],
            'admin_login' => $admin['login'],
        ]);

        return redirect()->to('admin/dashboard')->with('success', 'Bienvenue, administrateur.');
    }

    public function dashboard()
    {
        // Exemple de données — remplacer par requêtes réelles vers les modèles
        $stats = [
            'regimes_by_type' => [
                'Minceur' => 12,
                'Prise de masse' => 6,
                'Équilibré' => 9,
            ],
            'inscriptions_month' => [
                '2026-01' => 5,
                '2026-02' => 8,
                '2026-03' => 12,
                '2026-04' => 9,
                '2026-05' => 3,
            ],
            'top_regimes' => [
                ['id' => 1, 'nom' => 'Méditerranéen Minceur', 'inscriptions' => 24],
                ['id' => 2, 'nom' => 'Protéiné Gain', 'inscriptions' => 15],
                ['id' => 3, 'nom' => 'Equilibre Vital', 'inscriptions' => 11],
            ],
            'recent_users' => [
                ['id' => 10, 'name' => 'Alice', 'created_at' => '2026-05-01'],
                ['id' => 11, 'name' => 'Bob', 'created_at' => '2026-05-03'],
            ],
        ];

        return view('template/adminDashboard', ['stats' => $stats]);
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('admin')->with('success', 'Déconnexion réussie.');
    }
}

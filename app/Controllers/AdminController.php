<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
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
}

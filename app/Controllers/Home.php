<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GoldModel;

class Home extends Controller
{
    
   public function index()
{
    $goldModel = new GoldModel();
    $gold = $goldModel->getGoldPrixRecent();

    // Valeur par défaut si la table est vide
    if (!$gold) {
        $gold = [
            'prix'    => 0.00,
            'percent' => 0.00,
        ];
    }

    return view('template/accueil', ['gold' => $gold]);
}
}

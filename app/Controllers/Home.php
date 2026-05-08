<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GoldModel;

class Home extends Controller
{
    
    public function index()
    {
        $goldModel = new GoldModel();

        $data['prixGold'] = $goldModel->getGoldPrixRecent();

    
        $prixGold = $data['prixGold'];
        return view('template/accueil', ['prixGold' => $prixGold]);

    }

}

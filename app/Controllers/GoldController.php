<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class GoldController extends BaseController
{
     public function index()
    {
        $goldModel = new GoldModel();

        $data = $goldModel->getGoldPrixRecent();

        return view('accueil', $data);
    }
}

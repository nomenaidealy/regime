<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class RegimeController extends BaseController
{
    public function form()
    {
        // Fournir la variable $sports à la vue pour éviter l'erreur "Undefined variable $sports".
        // À remplacer par un vrai chargement depuis la BDD (ex: via un SportModel) si besoin.
        $sports = [];
        return view('template/regimeForm', ['sports' => $sports]);
    }

    public function list()
    {
        // Charger les régimes depuis la BDD (ex: via un RegimeModel)
        $regimes = [];
        return view('template/regimeList', ['regimes' => $regimes]);
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SportController extends BaseController
{
    public function form()
    {
        return view('template/sportForm');
    }

    public function list()
    {
        // Charger les sports depuis la BDD (ex: via un SportModel)
        $sports = [];
        return view('template/sportList', ['sports' => $sports]);
    }
}

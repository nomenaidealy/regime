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


    public function save()
    {
         $sportModel = new \App\Models\SportModel ;
         $data = $this->request->getPost();
         $libelle = $data['libelle'] ?? '';
         $description = $data['description'] ?? '';
         $variation_poids = $data['variation_poids_seance'] ?? '';

         $sportModel->insertSport($libelle, $description, $variation_poids);

       
        return redirect()->to('/admin/sports')->with('success', 'sport insere avec succees');
        
    }

    public function list()
    {
        $sportModel = new \App\Models\SportModel();
        $sports = $sportModel->findAll();
        return view('template/sportList', ['sports' => $sports]);
    }

    public function update($id)
    {
        $sportModel = new \App\Models\SportModel();
        $sport  = $sportModel->findById($id);
        if (!$sport) {
            return redirect()->to('/admin/sports')->with('error', 'Sport non trouvé');
        }

        $data = $this->request->getPost();
        $libelle = $data['libelle'] ?? '';
        $description = $data['description'] ?? '';
        $variation_poids = $data['variation_poids_seance'] ?? '';

        $sport->update($libelle, $description, $variation_poids);
        return redirect()->to('/admin/sports')->with('success', 'sport modifie avec succees');
    }
}
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
        $sportModel = new \App\Models\SportModel();
        $data = $this->request->getPost();
        $libelle = trim($data['libelle'] ?? '');
        $description = $data['description'] ?? '';
        $variation_poids = $data['variation_poids_seance'] ?? '';

        if ($libelle === '') {
            return redirect()->back()->withInput()->with('error', 'Le libellé est requis.');
        }

        if ($variation_poids === '' || !is_numeric($variation_poids)) {
            return redirect()->back()->withInput()->with('error', 'La variation de poids doit être un nombre.');
        }

        // Prevent duplicate libelle
        if ($sportModel->where('libelle', $libelle)->first()) {
            return redirect()->back()->withInput()->with('error', 'Une activité avec ce libellé existe déjà.');
        }

        try {
            $sportModel->insertSport($libelle, $description, $variation_poids);
            return redirect()->to('/admin/sports')->with('success', 'Sport inséré avec succès');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'insertion : ' . $e->getMessage());
        }
        
    }

    public function list()
    {
        $sportModel = new \App\Models\SportModel();
        $sports = $sportModel->findAll();
        return view('template/sportList', ['sports' => $sports]);
    }

    public function update($id)
    {
        // validate id
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Identifiant invalide.');
        }

        $sportModel = new \App\Models\SportModel();
        $sport = $sportModel->find($id);
        if (!$sport) {
            return redirect()->to('/admin/sports')->with('error', 'Sport non trouvé');
        }

        $data = $this->request->getPost();
        $libelle = trim($data['libelle'] ?? '');
        $description = $data['description'] ?? '';
        $variation_poids = $data['variation_poids_seance'] ?? '';

        if ($libelle === '') {
            return redirect()->back()->withInput()->with('error', 'Le libellé est requis.');
        }

        $updateData = [
            'libelle' => $libelle,
            'description' => $description,
            'variation_poids_seance' => (is_numeric($variation_poids) ? $variation_poids : null),
        ];

        try {
            $sportModel->update($id, $updateData);
            return redirect()->to('/admin/sports')->with('success', 'Sport modifié avec succès');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Identifiant invalide.');
        }

        try {
            $sportModel = new \App\Models\SportModel();
            $sport = $sportModel->find($id);
            if (!$sport) {
                return redirect()->to('/admin/sports')->with('error', 'Sport introuvable.');
            }
        } catch (\Throwable $e) {
            return redirect()->to('/admin/sports')->with('error', 'Impossible de charger l\'activité : ' . $e->getMessage());
        }

        return view('template/sportForm', ['sport' => $sport, 'mode' => 'edit']);
    }

    /**
     * Show confirmation page with related diets before destructive delete
     */
    public function confirmDelete($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Identifiant invalide.');
        }

        try {
            $db = \Config\Database::connect();
            $sport = $db->table('sport')->where('id', $id)->get()->getRowArray();
            if (!$sport) {
                return redirect()->to('/admin/sports')->with('error', 'Activité introuvable.');
            }

            $diets = $db->table('diet')->where('id_sport', $id)->get()->getResultArray();
        } catch (\Throwable $e) {
            return redirect()->to('/admin/sports')->with('error', 'Impossible d\'accéder à la base : ' . $e->getMessage());
        }

        return view('template/sportDeleteConfirm', ['sport' => $sport, 'diets' => $diets]);
    }

    /**
     * Force-delete: remove diets referencing the sport and then the sport itself
     */
    public function forceDelete($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Identifiant invalide.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            // Delete diets that reference this sport. Diet deletion will cascade to diet_prix (ON DELETE CASCADE)
            $db->table('diet')->where('id_sport', $id)->delete();

            // Now delete the sport itself
            $db->table('sport')->where('id', $id)->delete();

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to('/admin/sports')->with('error', 'Erreur lors de la suppression.');
            }

            return redirect()->to('/admin/sports')->with('success', 'Activité sportive et éléments liés supprimés.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/admin/sports')->with('error', 'Impossible de supprimer l\'activité : ' . $e->getMessage());
        }
    }

    /**
     * Remove sport but keep diets: set id_sport = NULL and subtract sport variation from diet.variation_poids_jour
     */
    public function removeKeepDiets($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Identifiant invalide.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $sport = $db->table('sport')->where('id', $id)->get()->getRowArray();
            if (!$sport) {
                return redirect()->to('/admin/sports')->with('error', 'Activité introuvable.');
            }

            $adj = 0;
            if (isset($sport['variation_poids_seance']) && is_numeric($sport['variation_poids_seance'])) {
                $adj = floatval($sport['variation_poids_seance']);
            }

            // Subtract sport variation from diets' variation_poids_jour and set id_sport to NULL
            // Use a bound query to avoid SQL injection and preserve decimals
            $db->query('UPDATE diet SET variation_poids_jour = variation_poids_jour - ?, id_sport = NULL WHERE id_sport = ?', [$adj, $id]);

            // Delete sport record
            $db->table('sport')->where('id', $id)->delete();

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to('/admin/sports')->with('error', 'Erreur lors de la suppression.');
            }

            return redirect()->to('/admin/sports')->with('success', 'Activité supprimée. Les régimes liés ont été conservés et ajustés.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/admin/sports')->with('error', 'Impossible de supprimer l\'activité : ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Identifiant invalide.');
        }

        $db = \Config\Database::connect();

        $db->transStart();
        try {
            // Delete diets that reference this sport. Diet deletion will cascade to diet_prix (ON DELETE CASCADE)
            $db->table('diet')->where('id_sport', $id)->delete();

            // Now delete the sport itself
            $db->table('sport')->where('id', $id)->delete();

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to('/admin/sports')->with('error', 'Erreur lors de la suppression.');
            }

            return redirect()->to('/admin/sports')->with('success', 'Activité sportive et éléments liés supprimés.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/admin/sports')->with('error', 'Impossible de supprimer l\'activité : ' . $e->getMessage());
        }
    }
}
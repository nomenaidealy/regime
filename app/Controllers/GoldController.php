<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\GoldModel;

class GoldController extends BaseController
{
     public function index()
    {
        $goldModel = new GoldModel();

        $data = $goldModel->getGoldPrixRecent();

        return view('accueil', $data);
    }

    // GET — affiche le formulaire de création/modification Gold
    public function adminNewIndex()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        $goldModel = new GoldModel();

        return view('template/admin/gold/new', [
            'gold' => $goldModel->getGoldPrixRecent(),
        ]);
    }

    // POST — traite la soumission du formulaire Gold
    public function adminNew()
    {
        if (!session()->get('is_admin')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès administrateur requis']);
        }

        $goldModel = new GoldModel();

        // Log inputs for debugging
        log_message('debug', 'Gold adminNew POST data: ' . json_encode($this->request->getPost()));
        $prix = $this->request->getPost('prix');
        $percent = $this->request->getPost('percent');

        if (!is_numeric($prix) || (float) $prix <= 0) {
            return redirect()->back()->withInput()->with('error', 'Le prix doit être un nombre supérieur à 0.');
        }

        if (!is_numeric($percent) || (float) $percent <= 0 || (float) $percent >= 1) {
            return redirect()->back()->withInput()->with('error', 'La remise doit être un nombre décimal entre 0 et 1 (ex: 0.15).');
        }

        $saved = $goldModel->createGoldConfig((float) $prix, (float) $percent);
        log_message('debug', 'Gold createGoldConfig returned: ' . var_export($saved, true));

        if ($saved !== false) {
            return redirect()->to('/admin/gold/new')->with('success', 'Configuration Gold enregistrée avec succès.');
        }

        // Récupérer l'erreur SQL pour diagnostiquer
        $dbError = $goldModel->db->error();
        $errMsg = 'Impossible d’enregistrer la configuration Gold.';
        if (!empty($dbError['message'])) {
            $errMsg .= ' SQL error: ' . $dbError['message'] . ' (code ' . ($dbError['code'] ?? 'N/A') . ')';
        }

        return redirect()->back()->withInput()->with('error', $errMsg);
    }

    // Debug helper: attempt a test insert and return result (admin only)
    public function adminDebugInsert()
    {
        if (!session()->get('is_admin')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès admin requis']);
        }

        $goldModel = new GoldModel();
        $prix = 1.00;
        $percent = 0.10;

        $id = $goldModel->createGoldConfig($prix, $percent);
        if ($id !== false) {
            return $this->response->setJSON(['success' => true, 'insertId' => $id]);
        }

        $err = $goldModel->db->error();
        return $this->response->setJSON(['success' => false, 'error' => $err]);
    }
}

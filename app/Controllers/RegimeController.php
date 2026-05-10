<?php
namespace App\Controllers;
use App\Controllers\BaseController;

class RegimeController extends BaseController
{
    private function normalizeDecimal($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    public function form()
    {
        try {
            $regimeModel = new \App\Models\RegimeModel();
            $sports = $regimeModel->getAllSports();
        } catch (\Throwable $e) {
         
            session()->setFlashdata('error', 'Impossible de récupérer la liste des sports : base de données inaccessible.');
            $sports = [];
        }

        return view('template/admin/regime/form', ['sports' => $sports]);
    }

    public function list()
    {
        try {
            $regimeModel = new \App\Models\RegimeModel();
            $regimes = $regimeModel->getAdminRegimesList();
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Impossible de lister les régimes : base de données inaccessible.');
            $regimes = [];
        }

        return view('template/admin/regime/list', ['regimes' => $regimes]);
    }



    public function save()
    {
        $regimeModel = new \App\Models\RegimeModel();
        $nom = $this->request->getPost('nom');

        if ($regimeModel->existsByNom((string) $nom)) {
            return redirect()->back()->with('error', 'Ce régime existe déjà.');
        }

       
        $description = $this->request->getPost('description');
        $variation_poids_jour = $this->normalizeDecimal($this->request->getPost('variation_poids_jour'));
        $viande = (int)$this->request->getPost('viande_percent');
        $volaille = (int)$this->request->getPost('volaille_percent');
        $poisson = (int)$this->request->getPost('poisson_percent');
        $id_sport = $this->request->getPost('id_sport') ?: null;

        if ($variation_poids_jour === null) {
            return redirect()->back()->withInput()->with('error', 'La variation de poids doit être un nombre valide (ex: 0,10).');
        }

        if (($viande + $volaille + $poisson) !== 100) {
            return redirect()->back()->withInput()->with('error', 'La somme des pourcentages doit être égale à 100%.');
        }

     
        $durees = $this->request->getPost('duree') ?? [];
        $prixs  = $this->request->getPost('prix')  ?? [];

        

       
        if (!is_array($durees)) $durees = [$durees];
        if (!is_array($prixs))  $prixs  = [$prixs];

        if (count($durees) !== count($prixs)) {
            return redirect()->back()->withInput()->with('error', 'Incohérence entre durées et prix fournis.');
        }

        $priceRows = [];

        // Valider chaque paire
        for ($i = 0; $i < count($durees); $i++) {
            $rawD = $durees[$i];
            $rawP = $prixs[$i] ?? null;
            $d = intval($rawD);
            $p = $this->normalizeDecimal($rawP);

            if ($d <= 0) {
                return redirect()->back()->withInput()->with('error', "Durée invalide à la ligne " . ($i+1) . ": \"" . $rawD . "\". Chaque durée doit être un entier positif.");
            }
            if ($rawP === null || $p === null || $p < 0) {
                return redirect()->back()->withInput()->with('error', "Prix invalide à la ligne " . ($i+1) . ": \"" . ($rawP ?? '') . "\". Chaque prix doit être un nombre >= 0.");
            }

            $priceRows[] = ['duree' => $d, 'prix' => $p];
        }

        try {
            $dietData = [
                'nom'                 => $nom,
                'description'         => $description,
                'variation_poids_jour'=> $variation_poids_jour,
                'viande_percent'      => $viande,
                'volaille_percent'    => $volaille,
                'poisson_percent'     => $poisson,
                'id_sport'            => $id_sport,
            ];

            $dietId = $regimeModel->createWithPrices($dietData, $priceRows);
            if ($dietId === false) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors de la sauvegarde en base.');
            }

            return redirect()->to('admin/regimes')->with('success', 'Régime créé avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur serveur : ' . $e->getMessage());
        }
    }

  
    public function update($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->back()->with('error', 'Identifiant de régime invalide.');
        }

        $regimeModel  = new \App\Models\RegimeModel();

        $nom = $this->request->getPost('nom');

        
        if ($regimeModel->existsByNom((string) $nom, (int) $id)) {
            return redirect()->back()->withInput()->with('error', 'Ce régime existe déjà.');
        }

 
        $description = $this->request->getPost('description');
        $variation_poids_jour = $this->normalizeDecimal($this->request->getPost('variation_poids_jour'));
        $viande = (int)$this->request->getPost('viande_percent');
        $volaille = (int)$this->request->getPost('volaille_percent');
        $poisson = (int)$this->request->getPost('poisson_percent');
        $id_sport = $this->request->getPost('id_sport') ?: null;

        if ($variation_poids_jour === null) {
            return redirect()->back()->withInput()->with('error', 'La variation de poids doit être un nombre valide (ex: 0,10).');
        }

        if (($viande + $volaille + $poisson) !== 100) {
            return redirect()->back()->withInput()->with('error', 'La somme des pourcentages doit être égale à 100%.');
        }

    
        $durees = $this->request->getPost('duree') ?? [];
        $prixs  = $this->request->getPost('prix')  ?? [];

       
        if (!is_array($durees)) $durees = [$durees];
        if (!is_array($prixs))  $prixs  = [$prixs];

        if (count($durees) !== count($prixs)) {
            return redirect()->back()->withInput()->with('error', 'Incohérence entre durées et prix fournis.');
        }

        $priceRows = [];

        for ($i = 0; $i < count($durees); $i++) {
            $rawD = $durees[$i];
            $rawP = $prixs[$i] ?? null;
            $d = intval($rawD);
            $p = $this->normalizeDecimal($rawP);

            if ($d <= 0) {
                return redirect()->back()->withInput()->with('error', "Durée invalide à la ligne " . ($i+1) . ": \"" . $rawD . "\". Chaque durée doit être un entier positif.");
            }
            if ($rawP === null || $p === null || $p < 0) {
                return redirect()->back()->withInput()->with('error', "Prix invalide à la ligne " . ($i+1) . ": \"" . ($rawP ?? '') . "\". Chaque prix doit être un nombre >= 0.");
            }

            $priceRows[] = ['duree' => $d, 'prix' => $p];
        }


        try {
            $dietData = [
                'nom'                 => $nom,
                'description'         => $description,
                'variation_poids_jour'=> $variation_poids_jour,
                'viande_percent'      => $viande,
                'volaille_percent'    => $volaille,
                'poisson_percent'     => $poisson,
                'id_sport'            => $id_sport,
            ];

            if (!$regimeModel->updateWithPrices((int) $id, $dietData, $priceRows)) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour en base.');
            }

            return redirect()->to('admin/regimes')->with('success', 'Régime mis à jour avec succès !');

        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur serveur : ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('admin/regimes')->with('error', 'Identifiant invalide.');
        }

        try {
            $regimeModel = new \App\Models\RegimeModel();
            $data = $regimeModel->getForEdit((int) $id);
            $diet = $data['diet'] ?? null;
            if (!$diet) {
                return redirect()->to('admin/regimes')->with('error', 'Régime introuvable.');
            }

            $prixs = $data['prixs'];
            $sports = $data['sports'];
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Impossible de charger le régime : base de données inaccessible.');
            return redirect()->to('admin/regimes');
        }

        return view('template/admin/regime/form', [
            'diet' => $diet,
            'prixs' => $prixs,
            'sports' => $sports,
            'mode' => 'edit',
        ]);
    }

    public function delete($id)
    {
        if (!$id || !is_numeric($id)) {
            return redirect()->to('admin/regimes')->with('error', 'Identifiant de régime invalide.');
        }

        $regimeModel = new \App\Models\RegimeModel();

        
        try {
            if (!$regimeModel->deleteWithPrices((int) $id)) {
                return redirect()->to('admin/regimes')->with('error', 'Erreur lors de la suppression du régime.');
            }

            return redirect()->to('admin/regimes')->with('success', 'Régime supprimé avec succès !');
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Impossible de supprimer ce régime : ' . $e->getMessage());
            return redirect()->to('admin/regimes');
        }
    }
}
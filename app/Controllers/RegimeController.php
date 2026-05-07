<?php
namespace App\Controllers;
use App\Controllers\BaseController;

class RegimeController extends BaseController
{
    public function form()
    {
        $db     = \Config\Database::connect();
        $sports = $db->table('sport')->get()->getResultArray();
        return view('template/regimeForm', ['sports' => $sports]);
    }

    public function list()
    {
        $db      = \Config\Database::connect();
        $regimes = $db->table('diet')
                      ->join('sport', 'sport.id = diet.id_sport', 'left')
                      ->get()->getResultArray();
        return view('template/regimeList', ['regimes' => $regimes]);
    }



    public function save()
    {
        $db  = \Config\Database::connect();
        $nom = $this->request->getPost('nom');

        if ($db->table('diet')->where('nom', $nom)->get()->getRow()) {
            return redirect()->back()->with('error', 'Ce régime existe déjà.');
        }

       
        $description = $this->request->getPost('description');
        $variation_poids_jour = $this->request->getPost('variation_poids_jour');
        $viande = (int)$this->request->getPost('viande_percent');
        $volaille = (int)$this->request->getPost('volaille_percent');
        $poisson = (int)$this->request->getPost('poisson_percent');
        $id_sport = $this->request->getPost('id_sport') ?: null;

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

        // Valider chaque paire
        for ($i = 0; $i < count($durees)-1; $i++) {
            $rawD = $durees[$i];
            $rawP = $prixs[$i] ?? null;
            $d = intval($rawD);
            $p = is_numeric($rawP) ? floatval($rawP) : null;

            if ($d <= 0) {
                return redirect()->back()->withInput()->with('error', "Durée invalide à la ligne " . ($i+1) . ": \"" . $rawD . "\". Chaque durée doit être un entier positif.");
            }
            if ($rawP === null || !is_numeric($rawP) || $p < 0) {
                return redirect()->back()->withInput()->with('error', "Prix invalide à la ligne " . ($i+1) . ": \"" . ($rawP ?? '') . "\". Chaque prix doit être un nombre >= 0.");
            }
        }

        // Utiliser une transaction pour insérer le régime et ses prix
        $db->transStart();

        try {
            $db->table('diet')->insert([
                'nom'                 => $nom,
                'description'         => $description,
                'variation_poids_jour'=> $variation_poids_jour,
                'viande_percent'      => $viande,
                'volaille_percent'    => $volaille,
                'poisson_percent'     => $poisson,
                'id_sport'            => $id_sport,
            ]);

            $dietId = $db->insertID();

            // Insérer les prix
            for ($i = 0; $i < count($durees)-1; $i++) {
                $d = intval($durees[$i]);
                $p = floatval($prixs[$i]);
                $db->table('diet_prix')->insert([
                    'id_diet' => $dietId,
                    'duree'   => $d,
                    'prix'    => $p,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors de la sauvegarde en base.');
            }

            return redirect()->to('admin/regimes')->with('success', 'Régime créé avec succès !');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Erreur serveur : ' . $e->getMessage());
        }
    }


    
}
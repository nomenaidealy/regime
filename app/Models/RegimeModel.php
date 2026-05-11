<?php

namespace App\Models;

use CodeIgniter\Model;

class RegimeModel extends Model
{
    protected $table            = 'diet';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nom', 'description' , 'variation_poids_jour' , 'viande_percent', 'volaille_percent', 'poisson_percent', 'id_sport'];

    public function getAllSports(): array
    {
        return $this->db->table('sport')->get()->getResultArray();
    }

    public function getAdminRegimesList(): array
    {
        return $this->db->table('diet d')
            ->select([
                'd.id AS diet_id',
                'd.nom AS diet_nom',
                'd.description AS diet_description',
                'd.viande_percent',
                'd.volaille_percent',
                'd.poisson_percent',
                'd.variation_poids_jour',
                'd.id_sport',
                's.id AS sport_id',
                's.libelle AS sport_libelle',
                's.variation_poids_seance',
                'dp.prix AS prix_30',
            ])
            ->join('sport s', 's.id = d.id_sport', 'left')
            ->join('diet_prix dp', 'dp.id_diet = d.id AND dp.duree = 30', 'left')
            ->get()
            ->getResultArray();
    }

    public function existsByNom(string $nom, ?int $excludeId = null): bool
    {
        $builder = $this->db->table('diet')->where('nom', $nom);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->get()->getRowArray() !== null;
    }

    public function createWithPrices(array $dietData, array $prices): int|false
    {
        $this->db->transStart();

        $this->db->table('diet')->insert($dietData);
        $dietId = (int) $this->db->insertID();

        foreach ($prices as $priceData) {
            $this->db->table('diet_prix')->insert([
                'id_diet' => $dietId,
                'duree' => (int) $priceData['duree'],
                'prix' => (float) $priceData['prix'],
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return $dietId;
    }

    public function updateWithPrices(int $id, array $dietData, array $prices): bool
    {
        $this->db->transStart();

        $this->db->table('diet')->where('id', $id)->update($dietData);
        $this->db->table('diet_prix')->where('id_diet', $id)->delete();

        foreach ($prices as $priceData) {
            $this->db->table('diet_prix')->insert([
                'id_diet' => $id,
                'duree' => (int) $priceData['duree'],
                'prix' => (float) $priceData['prix'],
            ]);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function getForEdit(int $id): array
    {
        $diet = $this->db->table('diet')->where('id', $id)->get()->getRowArray();
        if (!$diet) {
            return [];
        }

        return [
            'diet' => $diet,
            'prixs' => $this->db->table('diet_prix')->where('id_diet', $id)->orderBy('duree')->get()->getResultArray(),
            'sports' => $this->getAllSports(),
        ];
    }

    public function deleteWithPrices(int $id): bool
    {
        $this->db->transStart();

        $this->db->table('diet_prix')->where('id_diet', $id)->delete();
        $this->db->table('diet')->where('id', $id)->delete();

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    


    public function getSuggestionsForUser(float $userWeight, float $userHeight, $objectif = null): array
    {
        $db = \Config\Database::connect();

        $suggestions = $db->table('diet d')
            ->select('d.id AS diet_id, d.nom AS diet_nom, d.description AS diet_description, d.variation_poids_jour, d.viande_percent, d.volaille_percent, d.poisson_percent, s.libelle AS sport_libelle, s.variation_poids_seance, dp.prix AS prix_30, dp.id AS prix_id')
            ->join('sport s', 's.id = d.id_sport', 'left')
            ->join('diet_prix dp', 'dp.id_diet = d.id AND dp.duree = 30', 'left')
            ->orderBy('d.nom', 'ASC')
            ->get()->getResultArray();

        // calculer cible
        $targetWeight = null;
        if ($objectif && isset($objectif->valeur_objectif)) {
            $libelle = strtolower($objectif->libelle ?? '');
            if (strpos($libelle, 'imc') !== false) {
                $targetIMC = (float)$objectif->valeur_objectif;
                $targetWeight = $targetIMC * ($userHeight * $userHeight);
            } else {
                $targetWeight = (float)$objectif->valeur_objectif;
            }
        }

        foreach ($suggestions as &$s) {
            $s['jours_estimes'] = null;
            $s['possible'] = true;
            if ($targetWeight === null || !isset($s['variation_poids_jour']) || $s['variation_poids_jour'] == 0) {
                $s['possible'] = false;
                continue;
            }

            $delta = $targetWeight - $userWeight;
            if (abs($delta) < 0.001) {
                $s['jours_estimes'] = 0;
                continue;
            }

            $varJour = (float)$s['variation_poids_jour'];
            if ($delta > 0 && $varJour <= 0) { $s['possible'] = false; continue; }
            if ($delta < 0 && $varJour >= 0) { $s['possible'] = false; continue; }

            $jours = (int)ceil(abs($delta) / abs($varJour));
            $s['jours_estimes'] = $jours;
        }

        // ne garder que les recommandés
        $suggestions = array_values(array_filter($suggestions, function ($s) { return isset($s['possible']) && $s['possible']; }));

        // Si l'objectif est un IMC, trier par durée estimée (ascendant)
        $isImcObjective = false;
        if ($objectif) {
            $lib = strtolower($objectif->libelle ?? '');
            if (strpos($lib, 'imc') !== false || (isset($objectif->objectif_id) && (int)$objectif->objectif_id === 3)) {
                $isImcObjective = true;
            }
        }

        if ($isImcObjective) {
            usort($suggestions, function ($a, $b) {
                $ja = isset($a['jours_estimes']) && $a['jours_estimes'] !== null ? $a['jours_estimes'] : PHP_INT_MAX;
                $jb = isset($b['jours_estimes']) && $b['jours_estimes'] !== null ? $b['jours_estimes'] : PHP_INT_MAX;
                return $ja <=> $jb;
            });
        }

        return $suggestions;
    }

    /**
     * Retourne les détails d'un régime + prixs + durée estimée pour un utilisateur
     */
    public function getRegimeDetailsForUser(int $dietId, float $userWeight, float $userHeight, $objectif = null): array
    {
        $db = \Config\Database::connect();

        $regime = $db->table('diet d')
            ->select('d.id AS diet_id, d.nom AS diet_nom, d.description AS diet_description, d.variation_poids_jour, d.viande_percent, d.volaille_percent, d.poisson_percent, s.libelle AS sport_libelle, s.description AS sport_description, s.variation_poids_seance')
            ->join('sport s', 's.id = d.id_sport', 'left')
            ->where('d.id', $dietId)
            ->get()->getRowArray();

        if (!$regime) return [];

        $prixs = $db->table('diet_prix')->where('id_diet', $dietId)->orderBy('duree', 'ASC')->get()->getResultArray();

        $jours_estimes = null;
        $possible = true;

        // calculer cible
        $targetWeight = null;
        if ($objectif && isset($objectif->valeur_objectif)) {
            $libelle = strtolower($objectif->libelle ?? '');
            if (strpos($libelle, 'imc') !== false) {
                $targetIMC = (float)$objectif->valeur_objectif;
                $targetWeight = $targetIMC * ($userHeight * $userHeight);
            } else {
                $targetWeight = (float)$objectif->valeur_objectif;
            }
        }

        if ($targetWeight === null || !isset($regime['variation_poids_jour']) || $regime['variation_poids_jour'] == 0) {
            $possible = false;
        } else {
            $delta = $targetWeight - $userWeight;
            if (abs($delta) < 0.001) {
                $jours_estimes = 0;
            } else {
                $varJour = (float)$regime['variation_poids_jour'];
                if ($delta > 0 && $varJour <= 0) $possible = false;
                if ($delta < 0 && $varJour >= 0) $possible = false;
                if ($possible) $jours_estimes = (int)ceil(abs($delta) / abs($varJour));
            }
        }

        return [
            'regime' => $regime,
            'prixs' => $prixs,
            'jours_estimes' => $jours_estimes,
            'possible' => $possible,
        ];
    }

    /**
     * Récupère le prix d'un régime pour une durée donnée (ex: 30 jours)
     * @param int $dietId
     * @param int $duree
     * @return array|null
     */
    public function getPriceForDuration(int $dietId, int $duree = 30)
    {
        $db = \Config\Database::connect();
        $row = $db->table('diet_prix')->where('id_diet', $dietId)->where('duree', $duree)->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * Récupère un prix par son id
     */
    public function getPriceById(int $prixId)
    {
        $db = \Config\Database::connect();
        $row = $db->table('diet_prix')->where('id', $prixId)->get()->getRowArray();
        return $row ?: null;
    }

}

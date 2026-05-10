<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class SportModel extends Model
{
    protected $table            = 'sport';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['libelle', 'description', 'variation_poids_seance'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    public function insertSport($libelle , $description , $variation_poids)
    {
        return $this->insert([
            'libelle' => $libelle,
            'description' => $description,
            'variation_poids_seance' => $variation_poids

        ]) ;
    }

    public function existsByLibelle(string $libelle, ?int $excludeId = null): bool
    {
        $builder = $this->where('libelle', $libelle);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->first() !== null;
    }

    public function getForDeleteConfirmation(int $id): array
    {
        $sport = $this->find($id);
        if (!$sport) {
            return ['sport' => null, 'diets' => []];
        }

        $diets = $this->db->table('diet')->where('id_sport', $id)->get()->getResultArray();

        return [
            'sport' => $sport,
            'diets' => $diets,
        ];
    }

    public function deleteSportWithDiets(int $id): bool
    {
        $this->db->transStart();

        $this->db->table('diet')->where('id_sport', $id)->delete();
        $this->delete($id);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function removeKeepDiets(int $id): bool
    {
        $sport = $this->find($id);
        if (!$sport) {
            throw new RuntimeException('Activité introuvable.');
        }

        $adj = 0.0;
        if (isset($sport['variation_poids_seance']) && is_numeric($sport['variation_poids_seance'])) {
            $adj = (float) $sport['variation_poids_seance'];
        }

        $this->db->transStart();

        $this->db->query(
            'UPDATE diet SET variation_poids_jour = variation_poids_jour - ?, id_sport = NULL WHERE id_sport = ?',
            [$adj, $id]
        );
        $this->delete($id);

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}

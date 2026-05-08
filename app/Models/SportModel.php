<?php

namespace App\Models;

use CodeIgniter\Model;

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
}

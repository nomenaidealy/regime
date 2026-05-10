<?php

namespace App\Models;

use CodeIgniter\Model;

class GoldModel extends Model
{
    protected $table            = 'gold';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['prix', 'percent', 'date_update'];

    public function getGoldPrixRecent(){
    $gold = $this 
        ->orderBy('date_update', 'DESC')
        ->first();

    return $gold;
}
    
    public function createGoldConfig(float $prix, float $percent): int|false
    {
        return $this->insert([
            'prix' => $prix,
            'percent' => $percent,
            'date_update' => date('Y-m-d H:i:s'),
        ]);
    }
}

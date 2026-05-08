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
    protected $allowedFields    = ['prix', 'date_update'];

    public function getGoldPrixRecent(){
    $gold = $this 
        ->orderBy('date_update', 'DESC')
        ->first();

    return $gold['prix'];
}
}

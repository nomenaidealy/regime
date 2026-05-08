<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'login',
        'mdp',
    ];

    public function tolog(string $login, string $mdp): array
    {
        $admin = $this->where('login', $login)->first();

        if (!$admin || ($admin['mdp'] ?? '') !== $mdp) {
            return [
                'success' => false,
                'message' => 'Identifiants administrateur invalides.',
            ];
        }

        return [
            'success' => true,
            'admin'   => $admin,
        ];
    }
}
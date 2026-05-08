<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationAdminModel extends Model
{
    protected $table            = 'notification_admin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_admin',
        'type',
        'id_demande',
        'message',
        'lue',
        'date_creation',
        'date_lecture',
    ];

   
    public function creerNotification(int $demandeId, string $message): bool
    {
        return (bool) $this->insert([
            'id_admin'   => null,
            'type'       => 'CODE_PROMO',
            'id_demande' => $demandeId,
            'message'    => $message,
            'lue'        => 0,
        ]);
    }

   
    public function marquerLue(int $demandeId): bool
    {
        return (bool) $this->where('id_demande', $demandeId)
            ->set([
                'lue'          => 1,
                'date_lecture' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }

   
    public function getNonLues(): array
    {
        return $this->db->table('notification_admin n')
            ->select('n.*, d.statut, u.nom AS user_nom,
                      cp.code, cp.montant')
            ->join('demande_code_promo d', 'd.id = n.id_demande')
            ->join('users u',             'u.id = d.id_user')
            ->join('code_promo cp',       'cp.id = d.id_code_promo')
            ->where('n.lue', 0)
            ->orderBy('n.date_creation', 'DESC')
            ->get()->getResultArray();
    }

   
    public function countNonLues(): int
    {
        return $this->where('lue', 0)->countAllResults();
    }
}
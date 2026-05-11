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
        'id_demande',      // * Solution 2 : une seule colonne
        'message',
        'lue',
        'date_creation',
        'date_lecture',
    ];

    
    public function creerNotification(
        int    $demandeId,
        string $message,
        string $type = 'CODE_PROMO'
    ): bool {
        return (bool) $this->insert([
            'id_admin'   => null,
            'type'       => $type,
            'id_demande' => $demandeId,
            'message'    => $message,
            'lue'        => 0,
        ]);
    }


    public function marquerLue(int $demandeId, string $type): bool
    {
        return (bool) $this->where('id_demande', $demandeId)
            ->where('type', $type)
            ->set([
                'lue'          => 1,
                'date_lecture' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }

    public function getNonLues(): array
{
    // * n.id_demande (pas n.demande_id)
    $sqlCodePromo = "
        SELECT
            n.id,
            n.type,
            n.id_demande,
            n.message,
            n.lue,
            n.date_creation,
            u.nom    AS user_nom,
            u.email  AS user_email,
            cp.code  AS code_promo,
            cp.montant,
            NULL     AS motif_rejet
        FROM notification_admin n
        JOIN demande_code_promo d  ON d.id  = n.id_demande
        JOIN users u               ON u.id  = d.id_user
        JOIN code_promo cp         ON cp.id = d.id_code_promo
        WHERE n.lue  = 0
          AND n.type = 'CODE_PROMO'
    ";

    $sqlGold = "
        SELECT
            n.id,
            n.type,
            n.id_demande,
            n.message,
            n.lue,
            n.date_creation,
            u.nom    AS user_nom,
            u.email  AS user_email,
            NULL     AS code_promo,
            g.prix   AS montant,
            NULL     AS motif_rejet
        FROM notification_admin n
        JOIN demande_gold dg ON dg.id = n.id_demande
        JOIN users u         ON u.id  = dg.id_user
        JOIN gold g          ON g.id  = (
            SELECT id FROM gold ORDER BY date_update DESC LIMIT 1
        )
        WHERE n.lue  = 0
          AND n.type = 'DEMANDE_GOLD'
    ";

    return $this->db->query("
        ({$sqlCodePromo})
        UNION ALL
        ({$sqlGold})
        ORDER BY date_creation DESC
    ")->getResultArray();
}
    
    public function countNonLues(): int
    {
        return $this->where('lue', 0)->countAllResults();
    }

    public function countNonLuesParType(): array
    {
        $results = $this->db
            ->table('notification_admin')
            ->select('type, COUNT(*) as total')
            ->where('lue', 0)
            ->groupBy('type')
            ->get()->getResultArray();

        // Retourne ['CODE_PROMO' => 3, 'DEMANDE_GOLD' => 1]
        return array_column($results, 'total', 'type');
    }
}
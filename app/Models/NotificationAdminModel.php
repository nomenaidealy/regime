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

    // ────────────────────────────────────────
    // CRÉER UNE NOTIFICATION (universel)
    // ────────────────────────────────────────

    /**
     * * Solution 2 : une seule méthode pour les 2 types.
     * Le type détermine dans quelle table chercher le demande_id.
     *
     * @param int    $demandeId  ID dans demande_code_promo OU demande_gold
     * @param string $message
     * @param string $type      'CODE_PROMO' | 'DEMANDE_GOLD'
     */
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

    // ────────────────────────────────────────
    // MARQUER COMME LUE
    // ────────────────────────────────────────

    /**
     * * Solution 2 : on filtre par type + demande_id
     * pour éviter les collisions (un id=1 existe dans les 2 tables).
     */
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

    // ────────────────────────────────────────
    // NOTIFICATIONS NON LUES AVEC DÉTAILS
    // ────────────────────────────────────────

    /**
     * * Solution 2 : UNION des 2 types pour afficher
     * toutes les notifs dans un seul panel admin.
     */
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
    // ────────────────────────────────────────
    // COMPTER LES NON LUES
    // ────────────────────────────────────────

    public function countNonLues(): int
    {
        return $this->where('lue', 0)->countAllResults();
    }

    // ────────────────────────────────────────
    // NON LUES PAR TYPE (optionnel)
    // ────────────────────────────────────────

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
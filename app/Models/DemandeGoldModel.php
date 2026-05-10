<?php

namespace App\Models;

use CodeIgniter\Model;

class DemandeGoldModel extends Model
{
    protected $table            = 'demande_gold';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_user',
        'statut',
        'date_demande',
        'date_traitement',
        'id_admin',
        'motif_rejet',
    ];

    protected $useTimestamps = false;

    // ────────────────────────────────────────
    // CRÉER UNE DEMANDE EN_ATTENTE
    // ────────────────────────────────────────

    public function creerDemande(int $userId): int|false
    {
        $inserted = $this->insert([
            'id_user' => $userId,
            'statut'  => 'EN_ATTENTE',
        ]);

        return $inserted ? $this->db->insertID() : false;
    }

    // ────────────────────────────────────────
    // VÉRIFIER SI UNE DEMANDE EN_ATTENTE OU VALIDÉE EXISTE
    // ────────────────────────────────────────

    /**
     * * CORRECTION : la vérification d'existence doit aussi
     * bloquer si une demande est déjà EN_ATTENTE ou VALIDE,
     * pas seulement chercher par id.
     */
    public function demandeExistante(int $userId): bool
    {
        return $this->where('id_user', $userId)
            ->whereIn('statut', ['EN_ATTENTE', 'VALIDE'])
            ->countAllResults() > 0;
    }

    public function getEnAttente(int $demandeId): ?array
    {
        return $this->where('id', $demandeId)
            ->where('statut', 'EN_ATTENTE')
            ->first();
    }

    // ────────────────────────────────────────
    // MARQUER VALIDÉE / REJETÉE
    // ────────────────────────────────────────

    public function marquerValide(int $demandeId, int $adminId): bool
    {
        return (bool) $this->update($demandeId, [
            'statut'          => 'VALIDE',
            'date_traitement' => date('Y-m-d H:i:s'),
            'id_admin'        => $adminId,
        ]);
    }

    public function marquerRejete(int $demandeId, int $adminId, string $motif = ''): bool
    {
        return (bool) $this->update($demandeId, [
            'statut'          => 'REJETE',
            'date_traitement' => date('Y-m-d H:i:s'),
            'id_admin'        => $adminId,
            'motif_rejet'     => $motif ?: null,   // * null si vide, pas ''
        ]);
    }

    // ────────────────────────────────────────
    // LISTE AVEC DÉTAILS (ADMIN)
    // ────────────────────────────────────────

    public function getAllWithDetails(?string $statut = null): array
    {
        $builder = $this->db->table('demande_gold d')
            ->select('d.*, u.nom AS user_nom, u.email AS user_email,
                      a.login AS admin_login')
            ->join('users u', 'u.id = d.id_user')
            ->join('admin a', 'a.id = d.id_admin', 'left')
            ->orderBy('d.date_demande', 'DESC');  // * tri manquant

        if ($statut) {
            $builder->where('d.statut', $statut);
        }

        return $builder->get()->getResultArray();
    }
}
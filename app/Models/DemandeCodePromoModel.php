<?php

namespace App\Models;

use CodeIgniter\Model;

class DemandeCodePromoModel extends Model
{
    protected $table            = 'demande_code_promo';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_user',
        'id_code_promo',
        'statut',
        'date_demande',
        'date_traitement',
        'id_admin',
        'motif_rejet',
    ];

    protected $useTimestamps = false;

  
   
    public function creerDemande(int $userId, int $codePromoId): int|false
    {
        $inserted = $this->insert([
            'id_user'       => $userId,
            'id_code_promo' => $codePromoId,
            'statut'        => 'EN_ATTENTE',
        ]);

        return $inserted ? $this->db->insertID() : false;
    }


    public function getEnAttente(int $demandeId): ?array
    {
        return $this->where('id', $demandeId)
            ->where('statut', 'EN_ATTENTE')
            ->first();
    }

  
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
            'motif_rejet'     => $motif ?: null,
        ]);
    }

   
    public function getAllWithDetails(?string $statut = null): array
    {
        $builder = $this->db->table('demande_code_promo d')
            ->select('d.*, u.nom AS user_nom, u.email AS user_email,
                      cp.code, cp.montant')
            ->join('users u',       'u.id = d.id_user')
            ->join('code_promo cp', 'cp.id = d.id_code_promo')
            ->orderBy('d.date_demande', 'DESC');

        if ($statut !== null) {
            $builder->where('d.statut', $statut);
        }

        return $builder->get()->getResultArray();
    }

    public function getByUser(int $userId): array
    {
        return $this->db->table('demande_code_promo d')
            ->select('d.id, d.statut, d.date_demande, d.motif_rejet,
                    cp.code, cp.montant')
            ->join('code_promo cp', 'cp.id = d.id_code_promo')
            ->where('d.id_user', $userId)
            ->orderBy('d.date_demande', 'DESC')
            ->limit(10)
            ->get()->getResultArray();
    }
    }
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

    /**
     * Récupère la répartition des régimes par sport
     * @return array Clé: nom du sport, Valeur: nombre de régimes
     */
    public function getRegimesByType(): array
    {
        $db = \Config\Database::connect();
        $result = $db->table('diet d')
            ->select('s.libelle, COUNT(d.id) as count')
            ->join('sport s', 'd.id_sport = s.id', 'left')
            ->groupBy('s.id, s.libelle')
            ->get()
            ->getResultArray();

        $data = [];
        foreach ($result as $row) {
            $label = $row['libelle'] ?? 'Non catégorisé';
            $data[$label] = (int)$row['count'];
        }

        return $data;
    }

    /**
     * Récupère le nombre d'inscriptions par mois (derniers 6 mois)
     * @return array Clé: YYYY-MM, Valeur: nombre d'inscriptions
     */
    public function getInscriptionsByMonth(): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->query(
            "SELECT DATE_FORMAT(date_inscription, '%Y-%m') AS month, COUNT(*) as count
             FROM users
             GROUP BY DATE_FORMAT(date_inscription, '%Y-%m')
             ORDER BY month ASC
             LIMIT 6"
        )->getResultArray();

        $data = [];
        foreach ($result as $row) {
            $data[$row['month']] = (int)$row['count'];
        }

        return $data;
    }

    /**
     * Récupère les régimes les plus souscrits (top 5)
     * @return array Liste des régimes avec nombre d'inscriptions
     */
    public function getTopRegimes(): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('diet d')
            ->select('d.id, d.nom, COUNT(ud.id) as inscriptions')
            ->join('user_diet ud', 'd.id = (SELECT id_diet FROM diet_prix WHERE id = ud.id_diet_prix)', 'left')
            ->groupBy('d.id, d.nom')
            ->orderBy('inscriptions', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Alternative plus simple : compter directement via diet_prix
        $result = $db->table('diet_prix dp')
            ->select('d.id, d.nom, COUNT(ud.id) as inscriptions')
            ->join('diet d', 'd.id = dp.id_diet')
            ->join('user_diet ud', 'ud.id_diet_prix = dp.id', 'left')
            ->groupBy('d.id, d.nom')
            ->orderBy('inscriptions', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'id' => (int)$row['id'],
                'nom' => $row['nom'],
                'inscriptions' => (int)($row['inscriptions'] ?? 0)
            ];
        }

        return $data;
    }

    /**
     * Récupère les derniers utilisateurs inscrits (top 10)
     * @return array Liste des utilisateurs avec date d'inscription
     */
    public function getRecentUsers(): array
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('users')
            ->select('id, nom as name, date_inscription as created_at')
            ->orderBy('date_inscription', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $result;
    }

    /**
     * Récupère les statistiques globales du dashboard
     * @return array Toutes les stats pour le dashboard
     */
    public function getDashboardStats(): array
    {
        return [
            'regimes_by_type' => $this->getRegimesByType(),
            'inscriptions_month' => $this->getInscriptionsByMonth(),
            'top_regimes' => $this->getTopRegimes(),
            'recent_users' => $this->getRecentUsers(),
        ];
    }
}
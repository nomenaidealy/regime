<?php

namespace App\Controllers;

use App\Models\DemandeGoldModel;
use App\Models\NotificationAdminModel;
use App\Models\GoldModel;
use App\Models\MouvementModel;

class DemandeGoldController extends BaseController
{
    protected DemandeGoldModel       $demandeGoldModel;
    protected NotificationAdminModel $notifModel;
    protected GoldModel              $goldModel;
    protected $session;

    public function __construct()
    {
        $this->demandeGoldModel = new DemandeGoldModel();
        $this->notifModel       = new NotificationAdminModel();
        $this->goldModel        = new GoldModel();
        $this->session          = session();
    }

    // ────────────────────────────────────────
    // GET — Page de confirmation
    // ────────────────────────────────────────

    public function showActivate()
    {
        $userId = (int) $this->session->get('user_id');

        if ($this->demandeGoldModel->demandeExistante($userId)) {
            return redirect()->to('user/dashboard')
                ->with('warning', 'Vous avez déjà une demande Gold en cours ou active.');
        }

        $gold = $this->goldModel->getGoldPrixRecent();

        if (!$gold) {
            return redirect()->to('user/dashboard')
                ->with('error', 'Tarif Gold indisponible.');
        }

        return view('template/goldActivate', ['gold' => $gold]);
    }

    // ────────────────────────────────────────
    // POST — Soumettre la demande Gold
    // ────────────────────────────────────────

    public function activate()
    {
        $userId = (int) $this->session->get('user_id');

        // ✅ Re-vérifier même en POST (double soumission possible)
        if ($this->demandeGoldModel->demandeExistante($userId)) {
            return redirect()->to('user/dashboard')
                ->with('warning', 'Vous avez déjà une demande Gold en cours ou active.');
        }

        // Récupérer l'utilisateur
        $db       = \Config\Database::connect();
        $user     = $db->table('users')->where('id', $userId)->get()->getRow();
        $userName = $user ? $user->nom : "Utilisateur #{$userId}";

        // ✅ Tout dans une transaction pour garantir cohérence
        $db->transStart();

        $demandeId = $this->demandeGoldModel->creerDemande($userId);

        if (!$demandeId) {
            $db->transRollback();
            return redirect()->to('user/dashboard')
                ->with('error', 'Erreur lors de la création de la demande.');
        }

        // ✅ Notification insérée dans la même transaction
        $notified = $this->notifModel->creerNotification(
            $demandeId,
            "Nouvelle demande Gold de {$userName}",
            'DEMANDE_GOLD'
        );

        if (!$notified) {
            $db->transRollback();
            return redirect()->to('user/dashboard')
                ->with('error', 'Erreur lors de la création de la notification.');
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('user/dashboard')
                ->with('error', 'Erreur lors de la soumission.');
        }

        return redirect()->to('user/dashboard')
            ->with('success', 'Demande Gold soumise. Un administrateur va la traiter.');
    }

    // ────────────────────────────────────────
    // ADMIN — Liste des demandes Gold
    // ────────────────────────────────────────

    public function adminDemandes()
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        $statut = $this->request->getGet('statut') ?? null;

        return view('template/demandeGoldAdmin', [
            'demandes' => $this->demandeGoldModel->getAllWithDetails($statut),
            'statut'   => $statut,
        ]);
    }

    // ────────────────────────────────────────
    // ADMIN — Valider (AJAX)
    // ────────────────────────────────────────

    public function adminValider()
    {
        if (!$this->session->get('is_admin') || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false, 'message' => 'Accès non autorisé',
            ]);
        }

        // ✅ CORRECTION : était 'id_demande', maintenant unifié avec 'demande_id'
        $demandeId = (int) $this->request->getPost('demande_id');
        $adminId   = (int) $this->session->get('admin_id');

        if (!$demandeId) {
            return $this->response->setJSON([
                'success' => false, 'message' => 'ID de demande manquant',
            ]);
        }

        $demande = $this->demandeGoldModel->getEnAttente($demandeId);

        if (!$demande) {
            return $this->response->setJSON([
                'success' => false, 'message' => 'Demande introuvable ou déjà traitée.',
            ]);
        }

        $gold = $this->goldModel->getGoldPrixRecent();

        if (!$gold) {
            return $this->response->setJSON([
                'success' => false, 'message' => 'Tarif Gold introuvable.',
            ]);
        }

        // Vérifier le solde
        $db      = \Config\Database::connect();
        $lastMvt = $db->table('mouvement')
            ->where('id_user', $demande['id_user'])
            ->orderBy('date_mouvement', 'DESC')
            ->limit(1)->get()->getRow();

        $currentBalance = $lastMvt ? (float) $lastMvt->montant_apres : 0.0;

        if ($currentBalance < (float) $gold['prix']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Solde insuffisant ({$currentBalance} Ar). Prix Gold : {$gold['prix']} Ar.",
            ]);
        }

        // ✅ Transaction : débit + gold + validation + notif lue
        $db->transStart();

        $newBalance     = $currentBalance - (float) $gold['prix'];
        $mouvementModel = new MouvementModel();
        $mouvementModel->addDebit(
            $demande['id_user'],
            (float) $gold['prix'],
            $newBalance,
            'Activation option Gold'
        );

        $this->goldModel->grantUserToGold($demande['id_user'], $gold['id']);
        $this->demandeGoldModel->marquerValide($demandeId, $adminId);
        $this->notifModel->marquerLue($demandeId, 'DEMANDE_GOLD');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false, 'message' => 'Erreur lors de la validation.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Demande Gold validée. {$gold['prix']} Ar débités.",
        ]);
    }

    // ────────────────────────────────────────
    // ADMIN — Rejeter (AJAX)
    // ────────────────────────────────────────

    public function adminRejeter()
    {
        if (!$this->session->get('is_admin') || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false, 'message' => 'Accès non autorisé',
            ]);
        }

        // ✅ Unifié avec 'demande_id'
        $demandeId = (int) $this->request->getPost('demande_id');
        $adminId   = (int) $this->session->get('admin_id');
        $motif     = trim($this->request->getPost('motif') ?? '');

        if (!$demandeId) {
            return $this->response->setJSON([
                'success' => false, 'message' => 'ID de demande manquant',
            ]);
        }

        $demande = $this->demandeGoldModel->getEnAttente($demandeId);

        if (!$demande) {
            return $this->response->setJSON([
                'success' => false, 'message' => 'Demande introuvable ou déjà traitée.',
            ]);
        }

        $this->demandeGoldModel->marquerRejete($demandeId, $adminId, $motif);
        $this->notifModel->marquerLue($demandeId, 'DEMANDE_GOLD');

        return $this->response->setJSON([
            'success' => true, 'message' => 'Demande Gold rejetée.',
        ]);
    }
}
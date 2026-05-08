<?php

namespace App\Controllers;

use App\Models\CodePromoModel;
use App\Models\DemandeCodePromoModel;
use App\Models\NotificationAdminModel;
use App\Models\MouvementModel;
use CodeIgniter\Controller;

class CodePromoController extends Controller
{
    protected CodePromoModel          $codePromoModel;
    protected DemandeCodePromoModel   $demandeModel;
    protected NotificationAdminModel  $notifModel;
    protected $session;

    public function __construct()
    {
        $this->codePromoModel = new CodePromoModel();
        $this->demandeModel   = new DemandeCodePromoModel();
        $this->notifModel     = new NotificationAdminModel();
        $this->session        = session();
    }

  
    public function form()
    {
        if (!$this->session->get('user_id')) {
            return redirect()->to('login')->with('error', 'Vous devez être connecté');
        }

        return view('template/codePromoForm');
    }

  
    public function redeem()
    {
        $userId = (int) $this->session->get('user_id');

        if (!$userId) {
            return redirect()->to('login')->with('error', 'Vous devez être connecté');
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Accès non autorisé',
            ]);
        }

        $code = strtoupper(trim($this->request->getPost('code') ?? ''));

        if (empty($code)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez saisir un code promo',
            ]);
        }

        // 1. Vérifier la disponibilité du code
        $codeData = $this->codePromoModel->getAvailableCode($code, $userId);

        if (!$codeData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code invalide, déjà utilisé ou en attente de validation.',
            ]);
        }

        // 2. Récupérer le nom de l'utilisateur
        $db       = \Config\Database::connect();
        $user     = $db->table('users')->where('id', $userId)->get()->getRow();
        $userName = $user ? $user->nom : "Utilisateur #{$userId}";

        // 3. Créer la demande EN_ATTENTE
        $demandeId = $this->demandeModel->creerDemande($userId, $codeData['id']);

        if (!$demandeId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la soumission de la demande.',
            ]);
        }

        // 4. Créer la notification admin
        $this->notifModel->creerNotification(
            $demandeId,
            "{$userName} a soumis le code {$code} ({$codeData['montant']})"
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Demande soumise. Un administrateur va la traiter prochainement.',
        ]);
    }

    // ────────────────────────────────────────
    // ADMIN — Créer un code promo
    // ────────────────────────────────────────

    public function adminForm()
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        return view('template/codePromoAdminForm');
    }

    public function adminCreate()
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        $data = [
            'code'    => strtoupper(trim($this->request->getPost('code') ?? '')),
            'montant' => $this->request->getPost('montant'),
        ];

        if (!$this->codePromoModel->validate($data)) {
            return redirect()->back()->withInput()
                ->with('error', 'Erreur de validation')
                ->with('errors', $this->codePromoModel->errors());
        }

        if ($this->codePromoModel->save($data)) {
            return redirect()->to('/admin/codepromo/list')
                ->with('success', "Code '{$data['code']}' créé avec succès");
        }

        return redirect()->back()->withInput()
            ->with('error', 'Erreur lors de la création du code promo');
    }

    public function adminList()
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        return view('template/codePromoAdminList', [
            'codes' => $this->codePromoModel->getAllWithDemandStats(),
        ]);
    }
  

    public function mesDemandes()
    {
        $userId = (int) $this->session->get('user_id');

        if (!$userId || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false, 'message' => 'Accès non autorisé',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $this->demandeModel->getByUser($userId),
        ]);
    }
        // ────────────────────────────────────────
    // ADMIN — Liste des demandes
    // ────────────────────────────────────────

    public function adminDemandes()
    {
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        $statut = $this->request->getGet('statut') ?? null;

        return view('template/codePromoAdminDemandes', [
            'demandes' => $this->demandeModel->getAllWithDetails($statut),
            'statut'   => $statut,
        ]);
    }

    // ────────────────────────────────────────
    // ADMIN — Valider une demande (AJAX)
    // ────────────────────────────────────────

    public function adminValider()
    {
        if (!$this->session->get('is_admin') || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Accès non autorisé',
            ]);
        }

        $demandeId = (int) $this->request->getPost('demande_id');
        $adminId   = (int) $this->session->get('admin_id');

        if (!$demandeId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de demande manquant',
            ]);
        }

        // 1. Récupérer la demande EN_ATTENTE
        $demande = $this->demandeModel->getEnAttente($demandeId);

        if (!$demande) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Demande introuvable ou déjà traitée.',
            ]);
        }

        $codeData = $this->codePromoModel->find($demande['id_code_promo']);

        if (!$codeData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code promo associé introuvable.',
            ]);
        }

        // 2. Créditer le solde via MouvementModel
        $db          = \Config\Database::connect();
        $lastMvt     = $db->table('mouvement')
            ->where('id_user', $demande['id_user'])
            ->orderBy('date_mouvement', 'DESC')
            ->limit(1)->get()->getRow();

        $currentBalance = $lastMvt ? (float) $lastMvt->montant_apres : 0.0;
        $newBalance     = $currentBalance + (float) $codeData['montant'];

        $mouvementModel = new MouvementModel();
        $credited = $mouvementModel->addCredit(
            $demande['id_user'],
            (float) $codeData['montant'],
            $newBalance,
            "Code promo {$codeData['code']} validé par admin"
        );

        if (!$credited) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors du crédit du solde.',
            ]);
        }

        // 3. Marquer la demande comme VALIDÉE
        $this->demandeModel->marquerValide($demandeId, $adminId);

        // 4. Marquer la notification comme lue
        $this->notifModel->marquerLue($demandeId);

        return $this->response->setJSON([
            'success' => true,
            'message' => "Demande validée. {$codeData['montant']} crédités.",
            'montant' => (float) $codeData['montant'],
        ]);
    }

    // ────────────────────────────────────────
    // ADMIN — Rejeter une demande (AJAX)
    // ────────────────────────────────────────

    public function adminRejeter()
    {
        if (!$this->session->get('is_admin') || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Accès non autorisé',
            ]);
        }

        $demandeId = (int) $this->request->getPost('demande_id');
        $adminId   = (int) $this->session->get('admin_id');
        $motif     = trim($this->request->getPost('motif') ?? '');

        if (!$demandeId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de demande manquant',
            ]);
        }

        $demande = $this->demandeModel->getEnAttente($demandeId);

        if (!$demande) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Demande introuvable ou déjà traitée.',
            ]);
        }

        // Marquer rejetée + notif lue
        $this->demandeModel->marquerRejete($demandeId, $adminId, $motif);
        $this->notifModel->marquerLue($demandeId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Demande rejetée avec succès.',
        ]);
    }

    // ────────────────────────────────────────
    // ADMIN — Notifications non lues (AJAX)
    // ────────────────────────────────────────

    public function adminNotifications()
    {
        if (!$this->session->get('is_admin')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Accès non autorisé',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'count'   => $this->notifModel->countNonLues(),
            'data'    => $this->notifModel->getNonLues(),
        ]);
    }
}
<?php

namespace App\Controllers;

use App\Models\CodePromoModel;
use CodeIgniter\Controller;

class CodePromoController extends Controller
{
    protected $codePromoModel;
    protected $session;

    public function __construct()
    {
        $this->codePromoModel = new CodePromoModel();
        $this->session = session();
    }

    // ────────────────────────────────────────
    // DISPLAY CODE PROMO FORM
    // ────────────────────────────────────────

    /**
     * Affiche la page de saisie du code promo
     */
    public function form()
    {
        $userId = $this->session->get('user_id');

        if (!$userId) {
            return redirect()->to('login')->with('error', 'Vous devez être connecté');
        }

        return view('template/codePromoForm');
    }

    // ────────────────────────────────────────
    // REDEEM CODE PROMO
    // ────────────────────────────────────────

    /**
     * Traite la saisie du code promo et crédite le compte
     */
    public function redeem()
    {
        $userId = $this->session->get('user_id');

        if (!$userId) {
            return redirect()->to('login')->with('error', 'Vous devez être connecté');
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Accès non autorisé'
            ]);
        }

        $code = strtoupper(trim($this->request->getPost('code')));

        if (empty($code)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez saisir un code promo'
            ]);
        }

        // Utiliser le code promo
        $result = $this->codePromoModel->useCodeAndCredit($code, $userId);

        if (!$result['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);
        }

        // Mettre à jour la session si nécessaire (pour le solde)
        // On pourrait le refaire ici mais c'est optionnel

        return $this->response->setJSON([
            'success' => true,
            'message' => $result['message'],
            'montant' => $result['montant']
        ]);
    }

    // ────────────────────────────────────────
    // ADMIN SECTION: CREATE CODE PROMO
    // ────────────────────────────────────────

    /**
     * Affiche la page de création d'un code promo (admin)
     */
    public function adminForm()
    {
        // Vérifier si admin
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        return view('template/codePromoAdminForm');
    }

    /**
     * Crée un nouveau code promo (admin)
     */
    public function adminCreate()
    {
        // Vérifier si admin
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        $code = strtoupper(trim($this->request->getPost('code')));
        $montant = $this->request->getPost('montant');

        $data = [
            'code' => $code,
            'montant' => $montant
        ];

        if ($this->codePromoModel->validate($data)) {
            if ($this->codePromoModel->save($data)) {
                return redirect()->to('admin/codepromo/list')->with(
                    'success',
                    "Code promo '{$code}' créé avec succès"
                );
            }
        }

        return redirect()->back()->withInput()
            ->with('error', 'Erreur lors de la création du code promo')
            ->with('errors', $this->codePromoModel->errors());
    }

    /**
     * Liste tous les codes promo (admin)
     */
    public function adminList()
    {
        // Vérifier si admin
        if (!$this->session->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Accès administrateur requis');
        }

        $codes = $this->codePromoModel->findAll();

        return view('template/codePromoAdminList', [
            'codes' => $codes
        ]);
    }
}

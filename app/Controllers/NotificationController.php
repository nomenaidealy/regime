<?php

namespace App\Controllers;

use App\Models\NotificationAdminModel;

class NotificationController extends BaseController
{
    protected NotificationAdminModel $notifModel;
    protected $session;

    public function __construct()
    {
        $this->notifModel = new NotificationAdminModel();
        $this->session    = session();
    }

    // ── AJAX : retourne toutes les notifs non lues ─────────────
    public function index()
    {
        if (!$this->session->get('is_admin')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false, 'message' => 'Accès non autorisé',
            ]);
        }

        $nonLues = $this->notifModel->getNonLues();

        return $this->response->setJSON([
            'success' => true,
            'count'   => count($nonLues),
            'data'    => $nonLues,
        ]);
    }
}
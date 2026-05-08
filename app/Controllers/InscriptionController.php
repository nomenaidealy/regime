<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class InscriptionController extends Controller
{
    // Affiche la page d'inscription
    public function index()
    {
        return view('template/inscription');
    }

    // Validation AJAX étape 1
    public function validateStep1()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }
        $data = [
            'nom'   => $this->request->getPost('nom'),
            'email' => $this->request->getPost('email'),
            'genre' => $this->request->getPost('genre'),
        ];
        $userModel = new UserModel();
        $result = $userModel->validateStep1($data);
        if ($result['valid']) {
            session()->set('inscription_step1', $data);
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    // Validation AJAX étape 2
    public function validateStep2()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }
        $data = [
            'taille'   => $this->request->getPost('taille'),
            'poids'    => $this->request->getPost('poids'),
            'objectif' => $this->request->getPost('objectif'),
        ];
        $userModel = new UserModel();
        $result = $userModel->validateStep2($data);
        if ($result['valid']) {
            session()->set('inscription_step2', $data);
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    // Validation AJAX étape 3
    public function validateStep3()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }
        $data = [
            'mdp' => $this->request->getPost('mdp'),
        ];
        $userModel = new UserModel();
        $result = $userModel->validateStep3($data);
        if ($result['valid']) {
            session()->set('inscription_step3', $data);
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    // Finalisation de l'inscription
    public function complete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }
        $step1 = session()->get('inscription_step1') ?? [];
        $step2 = session()->get('inscription_step2') ?? [];
        $step3 = session()->get('inscription_step3') ?? [];
        $data = array_merge($step1, $step2, $step3);
        $userModel = new UserModel();
        $userId = $userModel->insert([
            'nom'    => $data['nom'] ?? '',
            'email'  => $data['email'] ?? '',
            'genre'  => $data['genre'] ?? '',
            'taille' => $data['taille'] ?? '',
            'poids'  => $data['poids'] ?? '',
            'mdp'    => isset($data['mdp']) ? password_hash($data['mdp'], PASSWORD_DEFAULT) : '',
        ], true);
        if (!$userId) {
            $errors = $userModel->errors();
            $msg = 'Erreur lors de la création';
            if (!empty($errors)) {
                $msg = implode(' | ', $errors);
            }
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }
        session()->set([
            'user_id'    => $userId,
            'user_nom'   => $data['nom'],
            'isLoggedIn' => true
        ]);
        session()->remove(['inscription_step1', 'inscription_step2', 'inscription_step3']);
        return $this->response->setJSON(['success' => true, 'redirect' => '/dashboard']);
    }
}

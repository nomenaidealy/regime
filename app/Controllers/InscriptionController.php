<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserObjectifModel;
use CodeIgniter\Controller;

class InscriptionController extends Controller
{
    // Affiche la page d'inscription
    public function index()
    {
        return view('template/user/inscription');
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
            return $this->response->setJSON(['success' => true, 'errors' => []]);
        }
        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    // Validation AJAX étape 2
    public function validateStep2()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }
        try {
            $data = [
                'taille'   => $this->request->getPost('taille'),
                'poids'    => $this->request->getPost('poids'),
                'objectif' => $this->request->getPost('objectif'),
                'valeur_objectif' => $this->request->getPost('valeur_objectif') ?? '',
            ];
            $userModel = new UserModel();
            $result = $userModel->validateStep2($data);
            if ($result['valid']) {
                // Conserver toutes les données en session, même valeur_objectif
                session()->set('inscription_step2', $data);
                return $this->response->setJSON(['success' => true, 'errors' => []]);
            }
            return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Validation AJAX étape 3
    public function validateStep3()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }
        $data = [
            'mdp' => $this->request->getPost('mdp'),
            'mdp_confirm' => $this->request->getPost('mdp_confirm'),
        ];
        $userModel = new UserModel();
        $result = $userModel->validateStep3($data);
        if ($result['valid']) {
            session()->set('inscription_step3', $data);
            return $this->response->setJSON(['success' => true, 'errors' => []]);
        }
        return $this->response->setJSON(['success' => false, 'errors' => $result['errors']]);
    }

    // Finalisation de l'inscription
    public function completeInsc()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Accès non autorisé']);
        }
        $step1 = session()->get('inscription_step1') ?? [];
        $step2 = session()->get('inscription_step2') ?? [];
        $step3 = session()->get('inscription_step3') ?? [];
        $data = array_merge($step1, $step2, $step3);
        $userModel = new UserModel();
        $userId = $userModel->createUser($data);
        if (!$userId) {
            $errors = $userModel->errors();
            $msg = 'Erreur lors de la création';
            if (!empty($errors)) {
                $msg = implode(' | ', $errors);
            }
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        // Insérer l'objectif
        $userObjectifModel = new UserObjectifModel();
        
        // Déterminer la valeur de l'objectif selon le choix
        $objectifId = (int)$data['objectif'];
        $valeurObjectif = null;
        
        if ($objectifId === 3) {
            // Objectif 3 = "Atteindre son IMC idéal" → valeur = 22.5
            $valeurObjectif = 22.5;
        } elseif (isset($data['valeur_objectif']) && $data['valeur_objectif'] !== '' && $data['valeur_objectif'] !== '0') {
            // Objectifs 1 ou 2 → prendre la valeur saisie
            $valeurObjectif = (float)$data['valeur_objectif'];
        }
        
        $objectifResult = $userObjectifModel->insertObjectif(
            $userId, 
            $objectifId, 
            $valeurObjectif
        );
        
        if (!$objectifResult) {
            // L'utilisateur a été créé mais l'objectif n'a pas pu être inséré
            return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de l\'objectif']);
        }

        session()->set([
            'user_id'    => $userId,
            'user_nom'   => $data['nom'],
            'isLoggedIn' => true
        ]);
        session()->remove(['inscription_step1', 'inscription_step2', 'inscription_step3']);
        return $this->response->setJSON(['success' => true, 'redirect' => '']);
    }
}

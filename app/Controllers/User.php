<?php

namespace App\Controllers;
use App\Models\UserModel;

class User extends BaseController
{
    public function inscription()
    {
        return view('template/inscription');
    }

    public function saveUser()
    {
        $model = new UserModel();

        $data = [
            'nom'    => $this->request->getPost('nom'),
            'email'  => $this->request->getPost('email'),
            'genre'  => $this->request->getPost('genre'),
            'taille' => $this->request->getPost('taille'),
            'poids'  => $this->request->getPost('poids'),
            'mdp'    => password_hash($this->request->getPost('mdp'), PASSWORD_DEFAULT),
        ];

        $model->insert($data);

        // créer le wallet automatiquement
        $userId = $model->getInsertID();
        $db = \Config\Database::connect();
        $db->table('wallet')->insert([
            'id_user' => $userId,
            'solde'   => 0.00,
        ]);

        return redirect()->to('/login');
    }

    public function loginPage()
    {
        return view('login');
    }

    public function login()
    {
        $model = new UserModel();
        $email = $this->request->getPost('email');
        $mdp   = $this->request->getPost('mdp');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($mdp, $user['mdp'])) {
            session()->set([
                'isLoggedIn' => true,
                'user_id'    => $user['id'],
                'user_nom'   => $user['nom'],
            ]);
            return redirect()->to('/dashboard');
        }

        return redirect()->to('/login')->with('error', 'Email ou mot de passe incorrect');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
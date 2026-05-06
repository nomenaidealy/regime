<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Home extends Controller
{
    public function index()
    {
        return view('template/login');
    }

    public function login()
    {
        return view('template/login');
    }

    public function dashboard()
    {
        return view('template/dashboard');
    }

    public function list()
    {
        return view('template/list');
    }

    public function form()
    {
        return view('template/form');
    }
}

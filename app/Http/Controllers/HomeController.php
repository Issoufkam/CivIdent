<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Afficher la page d'accueil
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('home');  // Retourne la vue d'accueil
    }

    public function propos()
    {
        return view('apropos');  // Retourne la vue propos
    }

    public function contact()
    {
        return view('contacts');  // Retourne la vue contact
    }
}

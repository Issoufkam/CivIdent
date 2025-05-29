<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CitoyenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Pages publiques
Route::get('/', [HomeController::class, 'index'])->name('accueil');
Route::get('/propos', [HomeController::class, 'propos'])->name('propos');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Authentification et enregistrement
Route::controller(RegisteredUserController::class)->group(function () {
    Route::get('/register', 'create')->name('register');
    Route::post('/register', 'store');
});

// Mot de passe oublié par téléphone
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/mot-de-passe/oublie', 'showPhoneForm')->name('password.phone.request');
    Route::post('/mot-de-passe/envoyer-code', 'sendResetCode')->name('password.sms.send');
    Route::get('/mot-de-passe/verifier', 'showVerificationForm')->name('password.phone.verify');
    Route::post('/mot-de-passe/verifier', 'verifyAndReset')->name('password.phone.reset');
});

// Auth Laravel Breeze
require __DIR__.'/auth.php';

// Routes pour le citoyen
Route::get('/citoyen/demandes/deces', [CitoyenController::class, 'formDeces'])->name('deces');

Route::middleware(['auth', 'verified'])->prefix('/citoyen')->name('citoyen.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [CitoyenController::class, 'dashboard'])->name('dashboard');

    // Gestion des demandes
    Route::prefix('demandes')->name('demandes.')->group(function () {
        // Formulaires spécifiques
        Route::get('/naissance', [CitoyenController::class, 'formNaissance'])->name('naissance');
        Route::get('/mariage', [CitoyenController::class, 'formMariage'])->name('mariage');
        Route::get('/deces', [CitoyenController::class, 'formDeces'])->name('deces');
        Route::get('/certificat-vie', [CitoyenController::class, 'formVie'])->name('vie');
        Route::get('/certificat-entretien', [CitoyenController::class, 'formEntretien'])->name('entretien');
        Route::get('/certificat-revenu', [CitoyenController::class, 'formRevenu'])->name('revenu');

        // Routes CRUD
        Route::get('/', [CitoyenController::class, 'index'])->name('index');
        Route::get('/creer', [CitoyenController::class, 'create'])->name('create');
        Route::post('/', [CitoyenController::class, 'store'])->name('store');
        Route::get('/{document}', [CitoyenController::class, 'show'])->name('show');
        Route::get('/{document}/modifier', [CitoyenController::class, 'edit'])->name('edit');
        Route::put('/{document}', [CitoyenController::class, 'update'])->name('update');
    });

    // Simulateur de paiement
    Route::get('/paiement/{document}', [PaymentController::class, 'show'])->name('paiement.form');
    Route::post('/paiement/{document}', [PaymentController::class, 'payer'])->name('paiement');
});

// Profil utilisateur
Route::middleware('auth')->controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'edit')->name('profile.edit');
    Route::patch('/profile', 'update')->name('profile.update');
    Route::delete('/profile', 'destroy')->name('profile.destroy');
});

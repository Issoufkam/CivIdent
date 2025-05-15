<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\Auth\RegisteredUserController;


// Routes d'accueil et d'authentification
Route::get('/', [HomeController::class, 'index'])->name('accueil');
Route::get('/propos', [HomeController::class, 'propos'])->name('propos');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Registration routes
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register.form'); // Display the form
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register'); // Handle form submission
Route::get('/mot-de-passe/oublie', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showPhoneForm'])->name('password.phone.request');
Route::post('/mot-de-passe/envoyer-code', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetCode'])->name('password.sms.send');

//login routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

//citoyens routes
Route::get('/dashboard', [DocumentRequestController::class, 'index'])->name('citoyen.dashboard');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DocumentRequestController::class, 'dashboard'])->name('citoyen.dashboard');
    Route::get('/demandes/create/{type}', [DocumentRequestController::class, 'create'])->name('citoyen.demandes.create');
    Route::post('/demandes', [DocumentRequestController::class, 'store'])->name('citoyen.demandes.store');
    Route::get('/demandes/{document}', [DocumentRequestController::class, 'show'])->name('citoyen.demandes.show');
});

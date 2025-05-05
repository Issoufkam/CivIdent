<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CitoyenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;


// Routes d'accueil et d'authentification
Route::get('/', [HomeController::class, 'index'])->name('accueil');

// Registration routes
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register.form'); // Display the form
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register'); // Handle form submission
Route::get('/mot-de-passe/oublie', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showPhoneForm'])->name('password.phone.request');
Route::post('/mot-de-passe/envoyer-code', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetCode'])->name('password.sms.send');

// Auth routes provided by Breeze
require __DIR__.'/auth.php';

// Routes protégées par rôle

// Admin Routes
Route::middleware(['auth', 'role:citoyen'])->prefix('citoyen')->name('citoyen.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('agents', AdminController::class)->except(['show']);
    Route::resource('communes', AdminController::class)->except(['show']);
    Route::resource('roles', AdminController::class)->except(['show']);
    Route::get('admin/roles', [AdminController::class, 'indexRoles'])->name('admin.roles.index');
    Route::get('admin/roles/create', [AdminController::class, 'createRole'])->name('admin.roles.create');
    Route::post('admin/roles', [AdminController::class, 'storeRole'])->name('admin.roles.store');
    Route::get('admin/roles/{id}/edit', [AdminController::class, 'editRole'])->name('admin.roles.edit');
    Route::put('admin/roles/{id}', [AdminController::class, 'updateRole'])->name('admin.roles.update');
    Route::delete('admin/roles/{id}', [AdminController::class, 'destroyRole'])->name('admin.roles.destroy');
});

// Agent Routes
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
    Route::resource('demandes', AgentController::class)->only(['index', 'show', 'update']);
    Route::resource('actes', AgentController::class)->only(['index', 'create', 'store']);
});

// Citoyen Routes
Route::get('/citoyen/dashboard', [CitoyenController::class, 'dashboard'])->name('citoyen.dashboard');
Route::get('/citoyen/demandes', [CitoyenController::class, 'index'])->name('citoyen.demandes.index');
Route::get('paiements', [CitoyenController::class, 'paiements'])->name('paiements.index');

Route::middleware(['auth', 'role:citoyen'])->prefix('citoyen')->name('citoyen.')->group(function () {
    //Route::get('/dashboard', [CitoyenController::class, 'index'])->name('dashboard');
    //Route::resource('demandes', CitoyenController::class)->only(['index', 'create', 'store', 'show']);
    // Route::get('paiements', [CitoyenController::class, 'paiements'])->name('paiements.index');
});

// Authenticated session and profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Password reset routes
Route::get('/mot-de-passe/oublie', [ForgotPasswordController::class, 'showPhoneForm'])->name('password.phone.request');
Route::post('/mot-de-passe/envoyer-code', [ForgotPasswordController::class, 'sendResetCode'])->name('password.sms.send');
Route::get('/mot-de-passe/verifier', [ForgotPasswordController::class, 'showVerificationForm'])->name('password.phone.verify');
Route::post('/mot-de-passe/verifier', [ForgotPasswordController::class, 'verifyAndReset'])->name('password.phone.reset');
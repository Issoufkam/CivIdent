@extends('layouts.app')

@section('content')
<div class="container register-container">
    <div class="row register-card bg-white">
        <!-- Colonne Image -->
        <div class="col-md-6 register-image"></div>

        <!-- Colonne Formulaire -->
        <div class="col-md-6 p-5 overflow-auto">
            <h4 class="text-center mb-4">Créer un compte</h4>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" required>
                    @error('nom') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom complet</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                    @error('prenom') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Numéro de téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" value="{{ old('telephone') }}" required>
                    @error('telephone') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-dark w-100">S'inscrire</button>

                <p class="text-center mt-3">Déjà inscrit ? <a class="text-success" href="{{ route('login') }}">Se connecter</a></p>
            </form>
        </div>
    </div>
</div>

@endsection

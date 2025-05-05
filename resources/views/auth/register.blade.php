@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center form-container">
    <div class="form-box">
        <h4 class="text-center mb-4">Créer un compte</h4>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Koffi" value="{{ old('nom') }}" required>
                @error('nom') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom complet</label>
                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Ex: Jean Emmanuel" value="{{ old('prenom') }}" required>
                @error('prenom') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="telephone" class="form-label">Numéro de téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="01 23 45 67" value="{{ old('telephone') }}" required>
                @error('telephone') <div class="text-danger">{{ $message }}</div> @enderror

            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="exemple@mail.com" value="{{ old('email') }}" required>
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-dark w-100">S'inscrire</button>

            <p class="text-center mt-3">Déjà inscrit ? <a class="text-success" href="{{ route('login') }}">Se connecter</a></p>
            <p class="text-center"><a class="text-success" href="#">Mot de passe oublié ?</a></p>
        </form>
    </div>
</div>
@endsection

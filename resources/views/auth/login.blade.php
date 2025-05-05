@extends('layouts.app')

@section('content')

<div class="container d-flex align-items-center justify-content-center form-container">
    <div class="form-box">
        <h4 class="text-center mb-4">Se connecter</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="telephone" class="form-label">Numéro de téléphone</label>
                <input type="text" name="telephone" class="form-control" id="telephone" placeholder="07XXXXXXXX" required value="{{ old('telephone') }}">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="********" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Connexion</button>

            <p class="text-center">
                <a class="text-success" href="{{ route('password.phone.request') }}">Mot de passe oublié ?</a>
            </p>

            <p class="text-center">
                <a class="text-success" href="#">Mot de passe oublié ?</a>
            </p>
        </form>
    </div>
</div>

@endsection

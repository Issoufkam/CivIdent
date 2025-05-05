@extends('layouts.app')
@section('content')
<div class="container">
    <h4>Vérification du code</h4>
    <form method="POST" action="{{ route('password.phone.reset') }}">
        @csrf
        <input type="hidden" name="telephone" value="{{ $telephone }}">
        <div class="mb-3">
            <label for="code">Code reçu par SMS</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password_confirmation">Confirmer mot de passe</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Réinitialiser</button>
    </form>
</div>
@endsection

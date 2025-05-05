@extends('layouts.app')
@section('content')
<div class="container">
    <h4>Réinitialisation par téléphone</h4>
    <form method="POST" action="{{ route('password.sms.send') }}">
        @csrf
        <div class="mb-3">
            <label for="telephone">Numéro de téléphone</label>
            <input type="text" name="telephone" class="form-control" placeholder="0700000000" required>
        </div>
        <button type="submit" class="btn btn-dark">Envoyer le code</button>
    </form>
</div>
@endsection

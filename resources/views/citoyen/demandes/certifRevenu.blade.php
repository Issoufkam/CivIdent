@extends('layouts.app')

@section('content')

<div class="container my-5">
  <h2 class="mb-4 text-center section-title">Demande de Certificat de Non-Revenu</h2>

  <form method="POST" action="{{ route('demande.nonrevenu.revenu') }}">
    @csrf

    <div class="row g-3">

      <div class="col-md-6">
        <label for="nom" class="form-label">Nom</label>
        <input type="text" class="form-control" id="nom" name="nom" required>
      </div>

      <div class="col-md-6">
        <label for="prenoms" class="form-label">Prénoms</label>
        <input type="text" class="form-control" id="prenoms" name="prenoms" required>
      </div>

      <div class="col-md-6">
        <label for="date_naissance" class="form-label">Date de Naissance</label>
        <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
      </div>

      <div class="col-md-6">
        <label for="lieu_naissance" class="form-label">Lieu de Naissance</label>
        <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
      </div>

      <div class="col-md-6">
        <label for="profession" class="form-label">Profession</label>
        <input type="text" class="form-control" id="profession" name="profession">
      </div>

      <div class="col-md-6">
        <label for="adresse" class="form-label">Adresse Complète</label>
        <input type="text" class="form-control" id="adresse" name="adresse" required>
      </div>

      <div class="col-12">
        <label for="motif" class="form-label">Motif de la Demande</label>
        <textarea class="form-control" id="motif" name="motif" rows="3" required></textarea>
      </div>

      <div class="col-12">
        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" id="confirmation" required>
          <label class="form-check-label" for="confirmation">
            Je certifie que les informations fournies sont exactes.
          </label>
        </div>
      </div>

      <div class="col-12 mt-4">
        <button type="submit" class="btn btn-primary w-100 py-2">Soumettre la Demande</button>
      </div>
    </div>
  </form>
</div>
@endsection

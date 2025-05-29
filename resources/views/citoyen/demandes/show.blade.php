@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Détails de la demande #{{ $demande->registry_number }}</h2>

    <ul>
        <li>Type : {{ $demande->type->label() }}</li>
        <li>Statut : {{ $demande->status->label() }}</li>
        <li>Date de création : {{ $demande->created_at->format('d/m/Y H:i') }}</li>
        <li>Commune : {{ $demande->commune->name }}</li>
        <!-- Exemple pour afficher les métadonnées décodées -->
        @php
            $metadata = is_string($demande->metadata)
                ? json_decode($demande->metadata, true)
                : $demande->metadata;
        @endphp

        @if($metadata)
            <h4>Informations supplémentaires :</h4>
            <ul>
                @foreach ($metadata as $key => $value)
                    <li>{{ ucfirst(str_replace('_', ' ', $key)) }} : {{ $value }}</li>
                @endforeach
            </ul>
        @endif

    </ul>

    @if ($demande->justificatif_path)
        <p>
            Justificatif : <a href="{{ asset('storage/' . $demande->justificatif_path) }}" target="_blank">Voir le document</a>
        </p>
    @endif

    <a href="{{ route('citoyen.demandes.index') }}" class="btn btn-secondary mt-3">Retour aux demandes</a>
</div>
@endsection

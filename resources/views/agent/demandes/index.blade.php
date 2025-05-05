@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Demandes</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandes as $demande)
                    <tr>
                        <td>{{ $demande->id }}</td>
                        <td>{{ $demande->description }}</td>
                        <td>{{ $demande->status }}</td>
                        <td>
                            <a href="{{ route('agent.demandes.show', $demande->id) }}" class="btn btn-info">Voir</a>
                            <a href="{{ route('agent.demandes.update', $demande->id) }}" class="btn btn-success">Mettre à jour</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

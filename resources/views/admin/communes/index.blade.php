@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Liste des Communes</h1>
        <a href="{{ route('admin.communes.create') }}" class="btn btn-primary">Ajouter une Commune</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($communes as $commune)
                    <tr>
                        <td>{{ $commune->id }}</td>
                        <td>{{ $commune->name }}</td>
                        <td>
                            <a href="{{ route('admin.communes.edit', $commune->id) }}" class="btn btn-warning">Modifier</a>
                            <form action="{{ route('admin.communes.destroy', $commune->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
